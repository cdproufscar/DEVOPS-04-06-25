<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page = basename($_SERVER['PHP_SELF']); // Nome da página atual
?>

<header class="site-header">
  <!-- Meta responsiva -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Estilos e scripts -->
  <link rel="stylesheet" href="css/header.css">
  <script src="js/header.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <div class="container-header">
    <!-- Logo -->
    <a href="telainicial.php" class="logo">
      <img class="logo-image" src="img/Assistiverse.png" alt="Logo do Assistiverse">
    </a>

    <!-- Menu de Navegação -->
    <nav class="menu">
      <a href="sobre_nos.php">Sobre Nós</a>
      <a href="produto.php">Projetos</a>
      <a href="contato.php">Contato</a>
      <?php if (isset($_SESSION['id_usuario'])): ?>
        <a href="cadastro_produto.php">Criar+</a>
      <?php endif; ?>
    </nav>

    <!-- Área Direita: Pesquisa + Perfil/Login/Logout -->
    <div class="direito">
      <!-- Barra de Pesquisa (não aparece no login ou cadastro) -->
      <div class="barra-busca">
        <?php if (!in_array($page, ['login.php', 'cadastro_usuario.php'])): ?>
          <form action="pesquisar.php" method="GET" class="search-container">
            <input type="text" name="q" placeholder="Pesquisar..." class="search-box">
            <button type="submit" class="search-button">
              <span>|</span>
              <img src="img/search-icon.png" alt="Pesquisar" class="search-icon">
            </button>
          </form>
        <?php endif; ?>
      </div>

      <!-- Ações do usuário -->
      <div class="user-actions">
        <?php if (isset($_SESSION['id_usuario'])): ?>
          <a href="perfil_usuario.php" class="user-profile">
            <img src="img/user-icon.png" alt="Perfil" class="user-icon">
          </a>
          <button class="btn-login" id="logoutBtn">Logout</button>
        <?php else: ?>
          <a href="login.php" class="btn-login">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>
