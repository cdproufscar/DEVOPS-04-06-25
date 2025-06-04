<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php include 'header_dinamico.php'; ?>


<main class="home-content">
    <div class="init">
        <!-- Mensagem Inicial -->
        <h2 class="titulo" id="in">ENCONTRE SEU</h2>
        <h1 class="titulo-principal" id="in">DISPOSITIVO</h1>
        <a href="produto.php" class="cta-button">CLIQUE ABAIXO</a>
    </div>
 

     <!-- Desenvolvidos -->
    <div class="grid-container">
        <div class="grid-item">
            <img src="img\scoot.png"
             alt="Scoot">
            <div class="overlay"><span>Scoot</span></div>
        </div>
        <div class="grid-item">
            <img src="img\botao.png"  alt="Botões">
            <div class="overlay"><span>Botões</span></div>
        </div>
        <div class="grid-item">
            <img src="img\parapodium.png"  alt="Parapodium">
            <div class="overlay"><span>Parapodium</span></div>
        </div>
        <div class="grid-item">
            <img src="img\carrinho.png"  alt="Carrinho">
            <div class="overlay"><span>Carrinho</span></div>
        </div>
    </div>
    <!-- Campanha de Arrecadação -->
    <div style="margin-top: 5%;"  class="section-promocional">
        <div class="texto-promocional">
          <h1 class="titulo-principal">CAMPANHA</h1>
          <h7 class="titulo">DE ARRECADAÇÃO</h7><br>
          <img src="img/campanha.png" class="imagem-promocional">
        </div>
        <div class="imgqr">
          <img src="img/qrcode.png" alt="QR Code" class="qr-code">
        </div>
    </div>

    <!-- CDPRO -->
    <div style="margin-top: 5%;" class="section-promocional">
        <div class="texto-promocional">
          <h7 class="titulo">CONHEÇA NOSSO</h7>
          <h1 class="titulo-principal">LABORATÓRIO <br> MAKER</h1>
          
          <p class="descricao" id="BDA">Clique aqui</p>
        </div>
        <div>
          <img src="img/cdpro.png" class="cdpro_img">
        </div>
    </div>

    <!-- Mensagem Final -->
    <div style="margin-top: 5%;" class="section_text">
        <h7 class="titulo" style="text-align: center;"> TRANSFORMANDO IDEIAS EM REALIDADE, <br><br> CONSTRUINDO UM FUTURO MAIS ACESSÍVEL ATRAVÉS <br><br> DA ENGENHARIA E SAÚDE. </h7>     
    </div>

</main>

<link rel="stylesheet" href="css/global.css">

<link rel="stylesheet" href="css/telainicial.css">

<link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">

 <meta name="viewport" content="width=device-width, initial-scale=1.0">





<?php include 'footer.php'; ?>

