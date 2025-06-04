<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_produto = $_GET['id'];

// Verifica se o produto pertence ao usuário logado
$sql = "SELECT * FROM produtos WHERE id_produto = :id_produto AND id_usuario = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id_produto' => $id_produto,
    ':id_usuario' => $id_usuario
]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die("Produto não encontrado ou acesso negado.");
}

// Carrega componentes vinculados ao produto
$sqlComp = "SELECT * FROM componentes WHERE id_produto = :id_produto";
$stmtComp = $pdo->prepare($sqlComp);
$stmtComp->execute([':id_produto' => $id_produto]);
$componentes = $stmtComp->fetchAll(PDO::FETCH_ASSOC);

// Materiais e ferramentas disponíveis
function getOptions($pdo, $tabela, $id_col, $nome_col, $desc_col) {
    $sql = "SELECT $id_col, $nome_col, $desc_col FROM $tabela ORDER BY $nome_col";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

$materiais = getOptions($pdo, 'materiais', 'id_material', 'nome_material', 'descricao_material');
$ferramentas = getOptions($pdo, 'ferramentas', 'id_ferramenta', 'nome_ferramenta', 'descricao_ferramenta');

function getVinculos($pdo, $tabela, $id_col, $id_componente) {
    $stmt = $pdo->prepare("SELECT $id_col FROM $tabela WHERE id_componente = :id");
    $stmt->execute([':id' => $id_componente]);
    return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), $id_col);
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Produto</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/editar_produto.css">
</head>
<body>
<?php include 'header_dinamico.php'; ?>

<main class="edit-prod">
  <button class="btn-voltar" onclick="history.back()">← Voltar</button>
  <h1>Editar Produto</h1>

  <form action="processa_edicao_produto.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">

    <label>Nome do Produto:</label>
    <input type="text" name="nome_produto" value="<?= htmlspecialchars($produto['nome_produto']) ?>" required>

    <label>Descrição:</label>
    <textarea name="descricao"><?= htmlspecialchars($produto['descricao']) ?></textarea>

    <label>Para Quem:</label>
    <input type="text" name="para_quem" value="<?= htmlspecialchars($produto['para_quem']) ?>">

    <label>Por Quem:</label>
    <input type="text" name="por_quem" value="<?= htmlspecialchars($produto['por_quem']) ?>">

    <label>Por Que:</label>
    <textarea name="por_que"><?= htmlspecialchars($produto['por_que']) ?></textarea>

    <label>Para Que:</label>
    <textarea name="para_que"><?= htmlspecialchars($produto['para_que']) ?></textarea>

    <label>Pré-requisitos:</label>
    <textarea name="pre_requisitos"><?= htmlspecialchars($produto['pre_requisitos']) ?></textarea>

    <label>Modo de Uso:</label>
    <textarea name="modo_de_uso"><?= htmlspecialchars($produto['modo_de_uso']) ?></textarea>

    <hr>
    <h2>Componentes</h2>

    <?php foreach ($componentes as $comp): 
        $vinc_mat = getVinculos($pdo, 'componente_materiais', 'id_material', $comp['id_componente']);
        $vinc_fer = getVinculos($pdo, 'componente_ferramentas', 'id_ferramenta', $comp['id_componente']);
        $imgs = json_decode($comp['imagens'], true) ?? [];
        $arqs = json_decode($comp['arquivos'], true) ?? [];
    ?>
      <fieldset class="componente-edit">
        <legend>Componente: <?= htmlspecialchars($comp['nome_componente']) ?></legend>

        <input type="hidden" name="componentes[<?= $comp['id_componente'] ?>][id]" value="<?= $comp['id_componente'] ?>">

        <label>Nome:</label>
        <input type="text" name="componentes[<?= $comp['id_componente'] ?>][nome]" value="<?= htmlspecialchars($comp['nome_componente']) ?>">

        <label>Descrição:</label>
        <textarea name="componentes[<?= $comp['id_componente'] ?>][descricao]"><?= htmlspecialchars($comp['descricao']) ?></textarea>

        <label>Imagens Existentes:</label>
        <div class="scroll-box">
          <?php foreach ($imgs as $img): ?>
            <a href="<?= htmlspecialchars($img) ?>" target="_blank"><?= basename($img) ?></a><br>
          <?php endforeach; ?>
        </div>

        <label>Arquivos Existentes:</label>
        <div class="scroll-box">
          <?php foreach ($arqs as $arq): ?>
            <a href="<?= htmlspecialchars($arq) ?>" target="_blank"><?= basename($arq) ?></a><br>
          <?php endforeach; ?>
        </div>

        <label>Materiais:</label>
        <div class="scroll-box">
          <?php foreach ($materiais as $m): ?>
            <label class="item-opcao">
              <input type="checkbox" name="componentes[<?= $comp['id_componente'] ?>][materiais][]"
                     value="<?= $m['id_material'] ?>" <?= in_array($m['id_material'], $vinc_mat) ? 'checked' : '' ?>>
              <?= htmlspecialchars($m['nome_material']) ?>
              <div class="tooltip-desc"><?= htmlspecialchars($m['descricao_material']) ?></div>
            </label>
          <?php endforeach; ?>
        </div>

        <label>Ferramentas:</label>
        <div class="scroll-box">
          <?php foreach ($ferramentas as $f): ?>
            <label class="item-opcao">
              <input type="checkbox" name="componentes[<?= $comp['id_componente'] ?>][ferramentas][]"
                     value="<?= $f['id_ferramenta'] ?>" <?= in_array($f['id_ferramenta'], $vinc_fer) ? 'checked' : '' ?>>
              <?= htmlspecialchars($f['nome_ferramenta']) ?>
              <div class="tooltip-desc"><?= htmlspecialchars($f['descricao_ferramenta']) ?></div>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>
    <?php endforeach; ?>

    <br>
    <button type="submit">Salvar Alterações</button>
  </form>
</main>

<?php include 'footer.php'; ?>
</body>
</html>
