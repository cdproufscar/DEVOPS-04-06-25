<?php
require 'conexao.php';
include 'header_dinamico.php';

$mensagem = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome_ferramenta']);
    $descricao = trim($_POST['descricao_ferramenta']);

    if (!empty($nome)) {
        $stmt = $pdo->prepare("INSERT INTO ferramentas (nome_ferramenta, descricao_ferramenta) VALUES (:nome, :descricao)");
        $stmt->execute([':nome' => $nome, ':descricao' => $descricao]);
        $mensagem = "✅ Ferramenta cadastrada com sucesso!";
    } else {
        $mensagem = "⚠️ O nome da ferramenta é obrigatório.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Ferramenta</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/cadastro_ferramenta.css">
</head>
<body>
<main class="cad-ferr">
  <h1>Cadastrar Nova Ferramenta</h1>

  <?php if (!empty($mensagem)): ?>
    <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
  <?php endif; ?>

  <form method="POST">
    <label for="nome_ferramenta">Nome da Ferramenta:</label>
    <input type="text" name="nome_ferramenta" id="nome_ferramenta" required>

    <label for="descricao_ferramenta">Descrição:</label>
    <textarea name="descricao_ferramenta" id="descricao_ferramenta" rows="4"></textarea>

    <button type="submit">Cadastrar Ferramenta</button>
    <a href="javascript:history.back()" class="btn-voltar">← Voltar</a>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
