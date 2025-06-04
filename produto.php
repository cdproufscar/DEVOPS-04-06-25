<?php
require 'conexao.php';

$sql = "SELECT p.id_produto, p.nome_produto, p.descricao, p.imagens, u.nome AS nome_usuario
        FROM produtos p
        JOIN usuarios u ON p.id_usuario = u.id_usuario
        ORDER BY p.data_criacao DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Catálogo de Produtos</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/produto.css">
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main>
  <h1 class="titulo-catalogo">Catálogo de Produtos</h1>

  <?php if (!empty($produtos)): ?>
    <div class="catalogo-container">
      <?php foreach ($produtos as $produto): 
        $imagens = json_decode($produto['imagens'], true);
        $imagem = (!empty($imagens[0]) && file_exists($imagens[0])) ? $imagens[0] : 'img/sem-imagem.png';
      ?>
        <div class="produto-card">
          <div class="produto-imagem-container">
            <img src="<?= htmlspecialchars($imagem) ?>" alt="Imagem do Produto" class="produto-img">
          </div>
          <div class="produto-info">
            <h2><?= htmlspecialchars($produto['nome_produto']) ?></h2>
            <p class="criador">👤 <strong>Criado por:</strong> <?= htmlspecialchars($produto['nome_usuario']) ?></p>
            <p class="descricao"><?= nl2br(htmlspecialchars(substr($produto['descricao'], 0, 100))) ?>...</p>
            <a href="produto_detalhado.php?id=<?= $produto['id_produto'] ?>" class="btn-detalhes">Ver Detalhes</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="sem-produto">Nenhum produto cadastrado ainda.</p>
  <?php endif; ?>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
