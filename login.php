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
  <title>Login - Assistiverse</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/login.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main>
  <h1>LOGIN:</h1>
  <p>Informe os seus dados fornecidos durante a etapa de cadastro</p>

  <div class="login-container">
    <form action="processamento_login.php" method="POST">
      
      <label for="email">E-mail:</label>
      <input type="email" id="email" name="email" placeholder="Digite seu e-mail*" required>
      
      <label for="senha">Senha:</label>
      <input type="password" id="senha" name="senha" placeholder="Digite sua senha*" required>

      <button type="submit">Entrar</button>
      <p style="color: gray; font-size: 0.9em">Caso esqueça sua senha, <a href='recuperacao_senha.php'>clique aqui</a></p>


      <p class="cadastro-link">Não tem uma conta? <a href="cadastro_usuario.php">Cadastrar-se</a></p>
    </form>
  </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
