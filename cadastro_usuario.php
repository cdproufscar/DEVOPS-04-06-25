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
  <title>Cadastro de Usuário - Assistiverse</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/cadastro_usuario.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include 'header_dinamico.php'; ?>

<main class="CAD-USER">
  <h1>Cadastro de Usuário</h1>
  <form id="formCadastro" action="processa_cadastro_usuario.php" method="POST">
    
    <label for="nome"><h3>Nome:</h3></label>
    <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>
    
    <label for="email"><h3>E-mail:</h3></label>
    <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>
    
    <label for="confirmar-email"><h3>Confirmar Email:</h3></label>
    <input type="email" id="confirmar-email" name="confirmar_email" placeholder="Confirme seu e-mail" required>
    
    <label for="senha"><h3>Senha:</h3></label>
    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
    
    <label for="confirmar-senha"><h3>Confirmar Senha:</h3></label>
    <input type="password" id="confirmar-senha" name="confirmar_senha" placeholder="Confirme sua senha" required>
    
    <!-- Grupo de Classificação -->
    <fieldset>
      <legend><h3>Como você se classifica?</h3></legend>
      <div class="checkbox-group">
        <label class="form-check"><input type="checkbox" id="pcd" name="secoes[]" value="PCD"> PCD</label>
        <label class="form-check"><input type="checkbox" id="maker" name="secoes[]" value="MAKER"> Maker</label>
        <label class="form-check"><input type="checkbox" id="familiar" name="secoes[]" value="FAMILIAR"> Familiar</label>
        <label class="form-check"><input type="checkbox" id="especialista_saude" name="secoes[]" value="ESPECIALISTA DA SAÚDE"> Especialista da Saúde</label>
        <label class="form-check"><input type="checkbox" id="fornecedor" name="secoes[]" value="FORNECEDOR"> Fornecedor</label>
      </div>
    </fieldset>

    <!-- Seções dinâmicas -->
    <div id="seção-pcd" class="section">
      <h3>Seção PCD</h3>
      <label for="deficiencia">Tipo de Deficiência:</label>
      <select id="deficiencia" name="pcd_deficiencia">
        <option value="mobilidade">Mobilidade</option>
        <option value="visual">Visual</option>
        <option value="auditiva">Auditiva</option>
        <option value="intelectual">Intelectual</option>
      </select><br><br>
      <label for="limitações">Descrição das limitações:</label>
      <textarea id="limitações" name="pcd_limitações"></textarea>
    </div>

    <div id="seção-maker" class="section">
      <h3>Seção Maker</h3>
      <label for="formacao_maker">Formação Acadêmica:</label>
      <input type="text" id="formacao_maker" name="maker_projetista_formacao">
    </div>

    <div id="seção-familiar" class="section">
      <h3>Seção Familiar</h3>
      <label for="relacao">Relação com a Pessoa com Deficiência:</label>
      <input type="text" id="relacao" name="familiar_relacao" placeholder="Ex: Mãe, Irmão, Cuidador(a)..." data-original-required="true">


      <label for="familiar_deficiencia">Qual a Deficiência da Pessoa?</label>
      <select id="familiar_deficiencia" name="familiar_deficiencia">
        <option value="">Selecione...</option>
        <option value="mobilidade">Mobilidade</option>
        <option value="visual">Visual</option>
        <option value="auditiva">Auditiva</option>
        <option value="intelectual">Intelectual</option>
        <option value="múltipla">Múltipla</option>
        <option value="outro">Outro</option>
      </select>

      <label for="descricao_deficiencia_familiar">Descreva, se quiser, a condição:</label>
      <textarea id="descricao_deficiencia_familiar" name="descricao_deficiencia_familiar" placeholder="Detalhe se desejar..."></textarea>
    </div>

    <div id="seção-especialista_saude" class="section">
      <h3>Seção Especialista da Saúde</h3>
      <label for="formacao_especialista_saude">Formação Acadêmica:</label>
      <input type="text" id="formacao_especialista_saude" name="especialista_saude_formacao">
    </div>

    <div id="seção-fornecedor" class="section">
      <h3>Seção Fornecedor</h3>
      <label for="atuacao_fornecedor">Áreas de Atuação:</label>
      <input type="text" id="atuacao_fornecedor" name="fornecedor_atuacao">
    </div>

    <button id="btn" type="submit">Cadastrar-se</button>
  </form>
</main>

<?php include 'footer.php'; ?>

<script src="js/cadastro_usuario.js"></script>
</body>
</html>
