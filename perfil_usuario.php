<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require 'conexao.php';
$id_usuario = $_SESSION['id_usuario'];

// Dados do usuário
$sql = "SELECT nome, email, foto FROM usuarios WHERE id_usuario = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_usuario' => $id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Classificações
$sqlPerfis = "SELECT categoria FROM perfil_usuario WHERE id_usuario = :id";
$stmtPerfis = $pdo->prepare($sqlPerfis);
$stmtPerfis->execute([':id' => $id_usuario]);
$categorias = $stmtPerfis->fetchAll(PDO::FETCH_COLUMN);

// Dados adicionais por classificação
function buscarDados($pdo, $tabela, $id_usuario) {
    $stmt = $pdo->prepare("SELECT * FROM $tabela WHERE id_usuario = :id");
    $stmt->execute([':id' => $id_usuario]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}
$dadosPCD = buscarDados($pdo, 'dados_pcd', $id_usuario);
$dadosMaker = buscarDados($pdo, 'dados_maker', $id_usuario);
$dadosFamiliares = buscarDados($pdo, 'dados_familiares', $id_usuario);
$dadosEsp = buscarDados($pdo, 'dados_especialista', $id_usuario);
$dadosForn = buscarDados($pdo, 'dados_fornecedor', $id_usuario);

// Produtos
$sqlProdutos = "SELECT id_produto, nome_produto, descricao, imagens FROM produtos WHERE id_usuario = :id_usuario";
$stmtProdutos = $pdo->prepare($sqlProdutos);
$stmtProdutos->execute([':id_usuario' => $id_usuario]);
$produtos = $stmtProdutos->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Perfil do Usuário</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/perfil_usuario.css">
  <link rel="stylesheet" href="css/perfil_usuario_modal.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="js/perfil_usuario_modal.js" defer></script>
</head>
<body>
<?php include 'header_dinamico.php'; ?>

<main>
  <h1>Perfil do Usuário</h1>
  <section class="perfil">
    <div class="perfil-foto">
      <?php if (!empty($usuario['foto']) && file_exists("uploads/" . $usuario['foto'])): ?>
        <img src="uploads/<?= htmlspecialchars($usuario['foto']) ?>" alt="Foto de Perfil" class="perfil-img">
      <?php else: ?>
        <img src="img/user_nulo.png" class="perfil-img" alt="Foto Padrão">
      <?php endif; ?>
    </div>

    <p><strong>Nome:</strong> <?= htmlspecialchars($usuario['nome']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
    <p><strong>Classificações:</strong> <?= implode(', ', $categorias) ?></p>
    <button class="btn-familiar" onclick="abrirModalEdicao()">Editar Perfil Completo</button>
  </section>

  <section class="meus-produtos">
    <h2>Meus Produtos</h2>
    <?php if ($produtos): ?>
      <div class="catalogo-container">
        <?php foreach ($produtos as $produto):
          $imagens = json_decode($produto['imagens'], true);
          $img = !empty($imagens[0]) && file_exists($imagens[0]) ? $imagens[0] : "img/sem-imagem.png";
        ?>
          <div class="produto-card">
            <img src="<?= htmlspecialchars($img) ?>" alt="Imagem do Produto">
            <h3><?= htmlspecialchars($produto['nome_produto']) ?></h3>
            <p><?= htmlspecialchars($produto['descricao']) ?></p>
            <div class="produto-actions">
              <a href="editar_produto.php?id=<?= $produto['id_produto'] ?>" class="btn-editar">Editar</a>
              <button onclick="confirmarExclusao(<?= $produto['id_produto'] ?>)" class="btn-excluir">Excluir</button>
            </div>
            <a href="produto_detalhado.php?id=<?= $produto['id_produto'] ?>" class="btn-detalhes">Ver Detalhes</a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>Você ainda não cadastrou produtos.</p>
    <?php endif; ?>
  </section>

  <!-- MODAL DE EDIÇÃO -->
  <div id="modal-edicao" class="modal">
    <div class="modal-content">
      <span class="fechar" onclick="fecharModalEdicao()">&times;</span>
      <h3>Editar Perfil</h3>
      <form id="form-edicao" enctype="multipart/form-data"
        data-pcd_deficiencia="<?= htmlspecialchars($dadosPCD['deficiencia'] ?? '') ?>"
        data-pcd_limitacoes="<?= htmlspecialchars($dadosPCD['limitacoes'] ?? '') ?>"
        data-maker_formacao="<?= htmlspecialchars($dadosMaker['formacao'] ?? '') ?>"
        data-familiar_relacao="<?= htmlspecialchars($dadosFamiliares['relacao'] ?? '') ?>"
        data-familiar_deficiencia="<?= htmlspecialchars($dadosFamiliares['tipo_deficiencia'] ?? '') ?>"
        data-familiar_descricao="<?= htmlspecialchars($dadosFamiliares['descricao'] ?? '') ?>"
        data-especialista_formacao="<?= htmlspecialchars($dadosEsp['formacao'] ?? '') ?>"
        data-fornecedor_atuacao="<?= htmlspecialchars($dadosForn['atuacao'] ?? '') ?>"
      >
        <label for="foto">Foto de Perfil:</label>
        <input type="file" name="foto"><br>

        <label>Nome:</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>">

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>">

        <label>Nova Senha:</label>
        <input type="password" name="senha" placeholder="Deixe em branco para não alterar">

        <fieldset>
          <legend>Classificações:</legend>
          <?php
            $todas = ["PCD", "MAKER", "FAMILIAR", "ESPECIALISTA DA SAÚDE", "FORNECEDOR"];
            foreach ($todas as $c) {
              $checked = in_array($c, $categorias) ? "checked" : "";
              echo "<label><input type='checkbox' name='secoes[]' value='$c' $checked> $c</label>";
            }
          ?>
        </fieldset>

        <div class="tabs-container">
          <div class="tabs" id="tabs-nav"></div>
          <div class="tabs-content" id="tabs-content"></div>
        </div>

        <br><button type="submit">Salvar Alterações</button>
      </form>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>


<script>
function confirmarExclusao(produtoId) {
  Swal.fire({
    title: "Você tem certeza?",
    text: "Essa ação não poderá ser desfeita!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Sim, excluir!"
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `excluir_produto.php?id=${produtoId}`;
    }
  });
}
</script>

</body>
</html>
