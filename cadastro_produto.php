<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$componentes_temp = $_SESSION['componentes_temp'] ?? [];

$mensagem_sucesso = '';
if (isset($_SESSION['sucesso_componente'])) {
    $mensagem_sucesso = $_SESSION['sucesso_componente'];
    unset($_SESSION['sucesso_componente']);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Produto</title>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/cadastro_produto.css">
  <link rel="stylesheet" href="css/modais.css">
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main class="cad-prod">
  <?php if (!empty($mensagem_sucesso)): ?>
    <script>alert("<?= htmlspecialchars($mensagem_sucesso) ?>");</script>
  <?php endif; ?>

  <h1>Cadastro de Produto</h1>

  <form action="processa_cadastro_produto.php" method="POST" enctype="multipart/form-data">
    <label for="nome_produto">Nome do Produto:</label>
    <input type="text" name="nome_produto" id="nome_produto" required placeholder="Nome claro que identifique o produto.">

    <label for="descricao">Descrição:</label>
    <textarea name="descricao" id="descricao" placeholder="Resumo geral do dispositivo e sua função."></textarea>

    <label for="para_quem">Qual o público alvo do seu dispositivo?</label>
    <input type="text" name="para_quem" id="para_quem" placeholder="Use esse campo para apontar as possíveis patologias atendidas pelo dispositivo.">

    <label for="por_quem">Quem desenvolveu o dispositivo?</label>
    <input type="text" name="por_quem" id="por_quem" placeholder="Cite a profissão do responsável pelo produto ou da equipe.">

    <label for="testado_por">Esse produto foi testado? Se sim, por quem?</label>
    <input type="text" name="testado_por" id="testado_por" placeholder="Escreva a categoria do profissional que o testou.">

    <label for="por_que">Quais as motivações para a criação do dispositivo?</label>
    <textarea name="por_que" id="por_que" placeholder="Cite as demandas que levaram à criação do dispositivo."></textarea>

    <label for="para_que">Para que? Em quais contextos/Quais demandas ele visa atender?</label>
    <textarea name="para_que" id="para_que" placeholder="Explique em quais situações o dispositivo pode ser utilizado."></textarea>

    <label for="pre_requisitos">Pré-requisitos:</label>
    <textarea name="pre_requisitos" id="pre_requisitos" placeholder="Existem limitações ou requisitos específicos para uso do dispositivo?"></textarea>

    <label for="modo_de_uso">Modo de Uso:</label>
    <textarea name="modo_de_uso" id="modo_de_uso" placeholder="Descreva de forma detalhada as instruções de uso para o público alvo."></textarea>

    <label for="imagens[]">Imagens do Produto:</label>
    <input type="file" name="imagens[]" multiple accept="image/*">

    <label for="arquivos[]">Arquivos Diversos:</label>
    <input type="file" name="arquivos[]" multiple accept=".sql,.dwg,.mp4,.avi,.zip,.rar,.pdf">

    <h2>Componentes</h2>
    <button type="button" class="btn-componente" onclick="abrirCadastroComponente()">➕ Adicionar Componente</button>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Nome</th>
          <th>Descrição</th>
          <th>Visualizar</th>
          <th>Editar</th>
          <th>Excluir</th>
        </tr>
      </thead>
      <tbody id="componentes-lista"></tbody>
    </table>

    <h2>Processo de Montagem</h2>
    <button type="button" class="btn-componente" onclick="abrirModalMontagem()">➕ Adicionar Processo</button>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Componentes</th>
          <th>Descrição</th>
          <th>Mídias/Arquivos</th>
          <th>Excluir</th>
        </tr>
      </thead>
      <tbody id="listaMontagem"></tbody>
    </table>

    <input type="hidden" name="componentes_selecionados" id="componentes_selecionados">
    <input type="hidden" id="componentes_temp_php" value='<?= json_encode($componentes_temp) ?>'>

    <button type="submit">Cadastrar Produto</button>
  </form>
</main>

<?php include 'processo_montagem_modal.php'; ?>
<?php include 'footer.php'; ?>

<script src="js/cadastro_produto.js" defer></script>
</body>
</html>
