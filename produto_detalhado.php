<?php
session_start();
require 'conexao.php';

if (!isset($_GET['id'])) {
    die("Erro: Produto não encontrado.");
}

$id = $_GET['id'];

$sql = "SELECT * FROM produtos WHERE id_produto = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die("Produto não encontrado.");
}

$imagens = json_decode($produto['imagens'], true) ?? [];
$arquivos = json_decode($produto['arquivos'], true) ?? [];

function getNomeUsuario($pdo, $id_usuario) {
    $stmt = $pdo->prepare("SELECT nome FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id_usuario]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    return $res['nome'] ?? 'Desconhecido';
}

function getComponentesProduto($pdo, $id_produto) {
    $stmt = $pdo->prepare("SELECT * FROM componentes WHERE id_produto = ?");
    $stmt->execute([$id_produto]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($produto['nome_produto']) ?> - Detalhes</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/produto_detalhado.css">
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main class="produto-container">
  <button onclick="history.back()" class="btn-voltar">← Voltar</button>
  <h1 class="produto-nome"><?= htmlspecialchars($produto['nome_produto']) ?></h1>

  <div class="produto-detalhes">
    <div class="produto-imagens">
      <?php if (!empty($imagens)): ?>
        <?php foreach ($imagens as $img): ?>
          <img src="<?= htmlspecialchars(file_exists($img) ? $img : 'img/sem-imagem.png') ?>" class="produto-img">
        <?php endforeach; ?>
      <?php else: ?>
        <img src="img/sem-imagem.png" class="produto-img">
      <?php endif; ?>
    </div>

    <div class="produto-info">
      <p><strong>Responsável:</strong> <?= getNomeUsuario($pdo, $produto['id_usuario']) ?></p>
      <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
      <p><strong>Para Quem:</strong> <?= nl2br(htmlspecialchars($produto['para_quem'])) ?></p>
      <p><strong>Por Quem:</strong> <?= nl2br(htmlspecialchars($produto['por_quem'])) ?></p>
      <p><strong>Testado Por:</strong> <?= nl2br(htmlspecialchars($produto['testado_por'])) ?></p>
      <p><strong>Por Que:</strong> <?= nl2br(htmlspecialchars($produto['por_que'])) ?></p>
      <p><strong>Para Que:</strong> <?= nl2br(htmlspecialchars($produto['para_que'])) ?></p>
      <p><strong>Pré-requisitos:</strong> <?= nl2br(htmlspecialchars($produto['pre_requisitos'])) ?></p>
      <p><strong>Modo de Uso:</strong> <?= nl2br(htmlspecialchars($produto['modo_de_uso'])) ?></p>
    </div>
  </div>

  <h2>Arquivos do Produto</h2>
  <ul class="arquivos-lista">
    <?php if (!empty($arquivos)): ?>
      <?php foreach ($arquivos as $arq): ?>
        <li><a href="<?= htmlspecialchars($arq) ?>" target="_blank">📁 <?= basename($arq) ?></a></li>
      <?php endforeach; ?>
    <?php else: ?>
      <li>Nenhum arquivo enviado.</li>
    <?php endif; ?>
  </ul>

  <hr>
  <h2>Componentes</h2>
  <?php
    $componentes = getComponentesProduto($pdo, $id);
    foreach ($componentes as $comp) {
        include 'exibir_componente_detalhado.php';
        exibirComponenteDetalhado($pdo, $comp);
    }
  ?>

  <hr>
  <section class="processo-montagem">
    <h2>Processo de Montagem</h2>

    <?php
    $sqlMontagem = "SELECT * FROM processo_montagem WHERE id_produto = :id_produto";
    $stmtMontagem = $pdo->prepare($sqlMontagem);
    $stmtMontagem->execute([':id_produto' => $id]);
    $processos = $stmtMontagem->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if ($processos): ?>
      <?php foreach ($processos as $index => $proc): 
        $desc = nl2br(htmlspecialchars($proc['descricao']));
        $mids = json_decode($proc['midias'], true) ?? [];
        $arqs = json_decode($proc['arquivos'], true) ?? [];

        $sqlPC = "SELECT c.nome_componente 
                  FROM processo_componentes pc
                  JOIN componentes c ON c.id_componente = pc.id_componente
                  WHERE pc.id_processo = ?";
        $stmtPC = $pdo->prepare($sqlPC);
        $stmtPC->execute([$proc['id_processo']]);
        $usados = $stmtPC->fetchAll(PDO::FETCH_COLUMN);
      ?>
        <div class="componente-bloco">
          <h3>Etapa <?= $index + 1 ?></h3>
          <p><strong>Descrição:</strong> <?= $desc ?></p>

          <?php if ($usados): ?>
            <p><strong>Componentes Utilizados:</strong> <?= htmlspecialchars(implode(", ", $usados)) ?></p>
          <?php endif; ?>

          <?php if (!empty($mids)): ?>
            <div class="produto-imagens">
              <?php foreach ($mids as $img): ?>
                <img src="<?= htmlspecialchars($img) ?>" class="produto-img" alt="Imagem do processo">
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if (!empty($arqs)): ?>
            <div class="midia-bloco">
              <h4>Arquivos</h4>
              <ul>
                <?php foreach ($arqs as $file): ?>
                  <li><a href="<?= htmlspecialchars($file) ?>" target="_blank">📂 <?= basename($file) ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>
        <hr>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Nenhum processo de montagem registrado.</p>
    <?php endif; ?>
  </section>

</main>

<?php include 'footer.php'; ?>
</body>
</html>
