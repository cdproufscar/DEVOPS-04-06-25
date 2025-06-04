<?php
session_start();
require 'conexao.php';

$id_componente = $_GET['id'] ?? null;
if (!$id_componente) {
    die("Componente não especificado.");
}

$componente_temp = null;
if (isset($_SESSION['componentes_temp'])) {
    foreach ($_SESSION['componentes_temp'] as $comp) {
        if ($comp['id'] == $id_componente) {
            $componente_temp = $comp;
            break;
        }
    }
}

$from_session = false;
$materiais = $ferramentas = $passos = [];

if ($componente_temp) {
    $componente = $componente_temp;
    $from_session = true;
    $materiais = $componente['materiais'] ?? [];
    $ferramentas = $componente['ferramentas'] ?? [];
    $passos = $componente['passos'] ?? [];
} else {
    $stmt = $pdo->prepare("SELECT * FROM componentes WHERE id_componente = :id");
    $stmt->execute([':id' => $id_componente]);
    $componente = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$componente) die("Componente não encontrado.");

    $componente['imagens'] = json_decode($componente['imagens'], true) ?? [];
    $componente['arquivos'] = json_decode($componente['arquivos'], true) ?? [];

    $stmtMat = $pdo->prepare("
        SELECT m.nome_material, m.descricao_material, cm.quantidade, cm.unidade
        FROM componente_materiais cm
        JOIN materiais m ON cm.id_material = m.id_material
        WHERE cm.id_componente = :id
    ");
    $stmtMat->execute([':id' => $id_componente]);
    $materiais = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

    $stmtFer = $pdo->prepare("
        SELECT f.nome_ferramenta, f.descricao_ferramenta, cf.dimensoes
        FROM componente_ferramentas cf
        JOIN ferramentas f ON cf.id_ferramenta = f.id_ferramenta
        WHERE cf.id_componente = :id
    ");
    $stmtFer->execute([':id' => $id_componente]);
    $ferramentas = $stmtFer->fetchAll(PDO::FETCH_ASSOC);

    $stmtPasso = $pdo->prepare("SELECT * FROM passo_a_passo WHERE id_componente = :id");
    $stmtPasso->execute([':id' => $id_componente]);
    $passos = $stmtPasso->fetchAll(PDO::FETCH_ASSOC);

    foreach ($passos as &$p) {
        $stmtMat = $pdo->prepare("
            SELECT m.nome_material FROM passo_materiais pm
            JOIN materiais m ON pm.id_material = m.id_material
            WHERE pm.id_passo = :id
        ");
        $stmtMat->execute([':id' => $p['id_passo']]);
        $p['materiais'] = $stmtMat->fetchAll(PDO::FETCH_COLUMN);

        $stmtFer = $pdo->prepare("
            SELECT f.nome_ferramenta FROM passo_ferramentas pf
            JOIN ferramentas f ON pf.id_ferramenta = f.id_ferramenta
            WHERE pf.id_passo = :id
        ");
        $stmtFer->execute([':id' => $p['id_passo']]);
        $p['ferramentas'] = $stmtFer->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Detalhes do Componente</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/detalhar_componente.css">
</head>
<body>
<?php include 'header_dinamico.php'; ?>

<main class="detalhe-comp">
  <h1>Componente: <?= htmlspecialchars($componente['nome_componente'] ?? $componente['nome']) ?></h1>

  <p><strong>Descrição:</strong></p>
  <p><?= nl2br(htmlspecialchars($componente['descricao'])) ?></p>

  <!-- Imagens -->
  <?php if (!empty($componente['imagens'])): ?>
    <div class="midia-bloco">
      <h2>Imagens</h2>
      <?php foreach ($componente['imagens'] as $img): ?>
        <img src="<?= htmlspecialchars($img) ?>" class="imagem-comp" alt="Imagem do componente">
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- Arquivos -->
  <?php if (!empty($componente['arquivos'])): ?>
    <div class="midia-bloco">
      <h2>Arquivos</h2>
      <ul>
        <?php foreach ($componente['arquivos'] as $arq): ?>
          <li><a href="<?= htmlspecialchars($arq) ?>" target="_blank">📁 <?= basename($arq) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Materiais -->
  <div class="midia-bloco">
    <h2>Materiais</h2>
    <?php if (!empty($materiais)): ?>
      <ul>
        <?php foreach ($materiais as $mat): ?>
          <?php if (is_array($mat)): ?>
            <li><?= htmlspecialchars($mat['nome_material']) ?> —
              <?= $mat['quantidade'] ?> <?= $mat['unidade'] ?> <br>
              <em><?= htmlspecialchars($mat['descricao_material']) ?></em>
            </li>
          <?php else: ?>
            <li>ID: <?= $mat ?> (será vinculado ao salvar)</li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>Nenhum material adicionado.</p>
    <?php endif; ?>
  </div>

  <!-- Ferramentas -->
  <div class="midia-bloco">
    <h2>Ferramentas</h2>
    <?php if (!empty($ferramentas)): ?>
      <ul>
        <?php foreach ($ferramentas as $fer): ?>
          <?php if (is_array($fer)): ?>
            <li><?= htmlspecialchars($fer['nome_ferramenta']) ?> —
              <?= htmlspecialchars($fer['dimensoes']) ?><br>
              <em><?= htmlspecialchars($fer['descricao_ferramenta']) ?></em>
            </li>
          <?php else: ?>
            <li>ID: <?= $fer ?> (será vinculado ao salvar)</li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p>Nenhuma ferramenta adicionada.</p>
    <?php endif; ?>
  </div>
<!-- Passo-a-passo -->
<?php if (!empty($componente['passos'])): ?>
  <div class="midia-bloco">
    <h2>Passo-a-Passo</h2>
    <?php
    $num = 1;
    foreach ($componente['passos'] as $passo):
      $materiaisNomes = [];
      $ferramentasNomes = [];

      // Materiais
      if (!empty($passo['materiais'])) {
        foreach ($passo['materiais'] as $id_mat) {
          $nome = getNomeById($pdo, 'materiais', 'id_material', 'nome_material', $id_mat);
          $materiaisNomes[] = $nome;
        }
      }

      // Ferramentas
      if (!empty($passo['ferramentas'])) {
        foreach ($passo['ferramentas'] as $id_fer) {
          $nome = getNomeById($pdo, 'ferramentas', 'id_ferramenta', 'nome_ferramenta', $id_fer);
          $ferramentasNomes[] = $nome;
        }
      }
    ?>
      <div class="passo-bloco">
        <strong>Passo <?= $num++ ?>:</strong>
        <?php if (!empty($materiaisNomes)): ?>
          <em>Materiais: <?= implode(', ', array_map('htmlspecialchars', $materiaisNomes)) ?></em><br>
        <?php endif; ?>
        <?php if (!empty($ferramentasNomes)): ?>
          <em>Ferramentas: <?= implode(', ', array_map('htmlspecialchars', $ferramentasNomes)) ?></em><br>
        <?php endif; ?>
        <small><?= nl2br(htmlspecialchars($passo['descricao'])) ?></small>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>


  <div class="botoes">
    <a href="cadastro_produto.php" class="btn-voltar">← Voltar ao Produto</a>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
