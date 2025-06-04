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
  <title>Assistiverse - Início</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/telainicial.css">
</head>
<body>

<?php include 'php/header_dinamico.php'; ?>

<main>
  <section class="hero">
    <h1>Bem-vindo ao Assistiverse</h1>
    <p>Uma plataforma dedicada à acessibilidade e inovação assistiva.</p>
    <a href="projetos.html" class="btn">Explorar Projetos</a>
  </section>
</main>

<?php include 'php/footer.php'; ?>

</body>
</html>
