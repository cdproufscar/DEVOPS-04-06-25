<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexao.php';

$token = $_GET['token'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM recuperacao_senhas WHERE token = :token AND expira_em >= NOW()");
$stmt->execute([':token' => $token]);

if ($stmt->rowCount() === 0) {
    echo "<script>alert('Link inválido ou expirado.'); window.location='login.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Redefinir Senha</title>
  <link rel="stylesheet" href="css/global.css">
</head>
<body>
<main>
  <h1>Redefina sua senha</h1>
  <form action="php/processa_redefinicao.php" method="POST">
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
    <label>Nova senha:</label>
    <input type="password" name="nova_senha" required>
    <label>Confirmar nova senha:</label>
    <input type="password" name="confirmar_senha" required>
    <button type="submit">Redefinir</button>
  </form>
</main>
</body>
</html>
