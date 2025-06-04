<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperação de Senha - Assistiverse</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/recuperacao_senha.css">
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main>
  <h1>Recuperação de Senha</h1>
  <p>Digite seu email cadastrado para receber um link de redefinição de senha.</p>
  <form action="processa_recuperacao.php" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    
    <button type="submit">Enviar Link de Redefinição</button>
  </form>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
