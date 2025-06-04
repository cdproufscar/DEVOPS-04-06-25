<?php
session_start();
require 'conexao.php';

if (!isset($_GET['id'])) die("Componente não especificado.");

$id_temp = $_GET['id'];
$componentes = $_SESSION['componentes_temp'] ?? [];
$componente = null;

foreach ($componentes as $c) {
    if ($c['id'] == $id_temp) {
        $componente = $c;
        break;
    }
}

if (!$componente) die("Componente não encontrado na sessão.");

function getNomeById($pdo, $tabela, $id_col, $nome_col, $id) {
    $stmt = $pdo->prepare("SELECT $nome_col FROM $tabela WHERE $id_col = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row[$nome_col] : "ID $id";
}
function getDescById($pdo, $tabela, $id_col, $desc_col, $id) {
    $stmt = $pdo->prepare("SELECT $desc_col FROM $tabela WHERE $id_col = :id");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row[$desc_col] : '';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Detalhes Temporários do Componente</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/visualizar_componente_temp.css">
</head>
<body>
<?php include 'header_dinamico.php'; ?>
<main class="temp-comp">
  <h1>Componente: <?= htmlspecialchars($componente['nome']) ?></h1>

  <div class="comp-bloco">
    <h2>Descrição</h2>
    <p><?= nl2br(htmlspecialchars($componente['descricao'])) ?></p>
  </div>

  <?php if (!empty($componente['imagens'])): ?>
    <div class="comp-bloco"><h2>Imagens</h2><div class="midia">
      <?php foreach ($componente['imagens'] as $img): ?>
        <img src="<?= htmlspecialchars($img) ?>" class="comp-img" alt="Imagem">
      <?php endforeach; ?>
    </div></div>
  <?php endif; ?>

  <?php if (!empty($componente['arquivos'])): ?>
    <div class="comp-bloco"><h2>Arquivos</h2><ul>
      <?php foreach ($componente['arquivos'] as $file): ?>
        <li><a href="<?= htmlspecialchars($file) ?>" target="_blank">📁 <?= basename($file) ?></a></li>
      <?php endforeach; ?>
    </ul></div>
  <?php endif; ?>

  <div class="comp-bloco">
    <h2>Materiais Utilizados</h2>
    <?php if (!empty($componente['materiais'])): ?>
      <ul>
        <?php foreach ($componente['materiais'] as $id => $mat): ?>
          <li>
            <strong><?= getNomeById($pdo, 'materiais', 'id_material', 'nome_material', $id) ?></strong>:
            <?= $mat['quantidade'] ?> <?= $mat['unidade'] ?> — <?= getDescById($pdo, 'materiais', 'id_material', 'descricao_material', $id) ?>
            <?php if (!empty($mat['observacao'])): ?><br><em>Obs:</em> <?= htmlspecialchars($mat['observacao']) ?><?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?><p>Nenhum material.</p><?php endif; ?>
  </div>

  <div class="comp-bloco">
    <h2>Ferramentas Utilizadas</h2>
    <?php if (!empty($componente['ferramentas'])): ?>
      <ul>
        <?php foreach ($componente['ferramentas'] as $id => $fer): ?>
          <li>
            <strong><?= getNomeById($pdo, 'ferramentas', 'id_ferramenta', 'nome_ferramenta', $id) ?></strong>:
            <?= $fer['dimensoes'] ?> — <?= getDescById($pdo, 'ferramentas', 'id_ferramenta', 'descricao_ferramenta', $id) ?>
            <?php if (!empty($fer['observacao'])): ?><br><em>Obs:</em> <?= htmlspecialchars($fer['observacao']) ?><?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?><p>Nenhuma ferramenta.</p><?php endif; ?>
  </div>

  <div class="midia-bloco">
    <h2>Passo a Passo</h2>
    <?php if (!empty($componente['passos'])): ?>
      <?php foreach ($componente['passos'] as $i => $p): ?>
        <div class="passo">
          <h3>Passo <?= $i + 1 ?></h3>
          <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($p['descricao'])) ?></p>
          <?php if (!empty($p['materiais'])): ?>
            <p><strong>Materiais:</strong> <?= implode(', ', array_map(fn($id) => getNomeById($pdo, 'materiais', 'id_material', 'nome_material', $id), $p['materiais'])) ?></p>
          <?php endif; ?>
          <?php if (!empty($p['ferramentas'])): ?>
            <p><strong>Ferramentas:</strong> <?= implode(', ', array_map(fn($id) => getNomeById($pdo, 'ferramentas', 'id_ferramenta', 'nome_ferramenta', $id), $p['ferramentas'])) ?></p>
          <?php endif; ?>
          <?php if (!empty($p['imagens'])): ?>
            <div><strong>Imagens:</strong><br>
              <?php foreach ($p['imagens'] as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" class="comp-img" style="max-width:150px;">
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php if (!empty($p['arquivos'])): ?>
            <div><strong>Arquivos:</strong><ul>
              <?php foreach ($p['arquivos'] as $arq): ?>
                <li><a href="<?= htmlspecialchars($arq) ?>" target="_blank"><?= basename($arq) ?></a></li>
              <?php endforeach; ?>
            </ul></div>
          <?php endif; ?>
        </div>
        <hr>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Nenhum passo registrado.</p>
    <?php endif; ?>
  </div>

  <div class="botoes">
    <a href="cadastro_produto.php" class="btn-voltar">← Voltar ao Produto</a>
  </div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
