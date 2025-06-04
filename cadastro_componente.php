<?php
session_start();
require 'conexao.php';
include 'header_dinamico.php';

function getOptions($pdo, $tabela, $id_col, $nome_col, $desc_col) {
    $stmt = $pdo->query("SELECT $id_col, $nome_col, $desc_col FROM $tabela ORDER BY $nome_col");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$materiais = getOptions($pdo, 'materiais', 'id_material', 'nome_material', 'descricao_material');
$ferramentas = getOptions($pdo, 'ferramentas', 'id_ferramenta', 'nome_ferramenta', 'descricao_ferramenta');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Componente</title>       
  <link rel="stylesheet" href="css/global.css">   
  <link rel="stylesheet" href="css/cadastro_componente.css">
  <link rel="stylesheet" href="css/modais.css">
</head>
<body>
<main class="cad-comp">
  <h1>Cadastrar Componente</h1> 

  <form  action="processa_cadastro_componente.php" method="POST" enctype="multipart/form-data">
    <label>Nome:</label>
    <input type="text" name="nome" required>

    <label>Descrição:</label>
    <textarea name="descricao" rows="4"></textarea>

    <label>Imagens:</label>
    <input type="file" name="imagens[]" multiple accept="image/*">

    <label>Arquivos:</label>
    <input type="file" name="arquivos[]" multiple accept=".sql,.dwg,.cad,.txt,.zip,.rar,.mp4,.avi,.pdf">

    <!-- Materiais -->
    <div class="section-header">
      <label>Materiais utilizados:</label>
    </div>
    <table class="tabela-material">
      <thead>
        <tr style="background: #fff59d;">
          <th>Material</th><th>Nome</th><th>Qtd.</th><th>Unidade</th><th>Detalhes</th><th>Ação</th>
        </tr>
      </thead>
      <tbody id="listaMateriais"></tbody>
    </table>
    <div style="text-align:right;">
      <button type="button" class="btn-add" onclick="abrirModal('modalMaterial')">➕ Adicionar Material</button>
    </div>

    <!-- Ferramentas -->
    <div class="section-header">
      <label>Ferramentas utilizadas:</label>
    </div>
    <table class="tabela-ferramenta">
      <thead>
        <tr style="background: #fff59d;">
          <th>Ferramenta</th><th>Nome</th><th>Descrição</th><th>Detalhes</th><th>Ação</th>
        </tr>
      </thead>
      <tbody id="listaFerramentas"></tbody>
    </table>
    <div style="text-align:right;">
      <button type="button" class="btn-add" onclick="abrirModal('modalFerramenta')">➕ Adicionar Ferramenta</button>
    </div>

    <!-- Passo-a-passo -->
    <h2>Passo-a-passo</h2>
    <table class="tabela-passo">
       <thead>
    <tr style="background: #fff59d;">
      <th>Passo</th>
      <th>Materiais</th>
      <th>Ferramentas</th>
      <th>Descrição</th>
      <th>Ação</th>
    </tr>
  </thead>
  <tbody id="listaPassos"></tbody>
    </table>
    <div style="text-align:right;">
      <button type="button" class="btn-add" onclick="abrirModal('modalPasso')">➕ Adicionar Passo</button>
    </div>

    <br>
    <button type="submit">Cadastrar Componente</button>
  </form>
</main>

<?php include 'modais_componentes.php'; ?>
<?php include 'footer.php'; ?>

<script src="js/cadastro_componente.js"></script>
<script>
  window.onload = function() {
    atualizarOpcoesPasso();
  };
</script>
</body>
</html>

