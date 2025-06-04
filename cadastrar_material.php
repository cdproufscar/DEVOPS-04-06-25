<?php
require 'conexao.php';
include 'header_dinamico.php';

$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome_material']);
    $descricao = trim($_POST['descricao_material']);

    if (!empty($nome)) {
        $stmt = $pdo->prepare("INSERT INTO materiais (nome_material, descricao_material) VALUES (:nome, :descricao)");
        $stmt->execute([':nome' => $nome, ':descricao' => $descricao]);
        $mensagem = "✅ Material cadastrado com sucesso!";
    } else {
        $mensagem = "⚠️ O nome do material é obrigatório.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Material</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/cadastro_material.css">
</head>
<body>
<main class="cad-mat">
  <h1>Cadastrar Novo Material</h1>

  <?php if (!empty($mensagem)): ?>
    <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
  <?php endif; ?>

  <form method="POST">
    <label for="nome_material">Nome do Material:</label>
    <input type="text" name="nome_material" id="nome_material" required>

    <label for="descricao_material">Descrição:</label>
    <textarea name="descricao_material" id="descricao_material" rows="4"></textarea>

    <button type="submit">Cadastrar Material</button>
    <a href="javascript:history.back()" class="btn-voltar">← Voltar</a>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
