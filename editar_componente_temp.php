<?php
session_start();
require 'conexao.php';

if (!isset($_GET['id'])) {
    die("Componente não especificado.");
}

$id = $_GET['id'];
$componentes = $_SESSION['componentes_temp'] ?? [];
$componente = null;

foreach ($componentes as $c) {
    if ($c['id'] == $id) {
        $componente = $c;
        break;
    }
}

if (!$componente) {
    die("Componente não encontrado.");
}

function getNome($pdo, $tabela, $id_col, $nome_col, $id) {
    $stmt = $pdo->prepare("SELECT $nome_col FROM $tabela WHERE $id_col = :id");
    $stmt->execute([':id' => $id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    return $r ? $r[$nome_col] : "Desconhecido";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Componente Temporário</title>
  <link rel="stylesheet" href="css/global.css">
  <link rel="stylesheet" href="css/editar_componente_temp.css">
</head>
<body>
<?php include 'header_dinamico.php'; ?>
<main class="edit-comp">
  <h1>Editando: <?= htmlspecialchars($componente['nome']) ?></h1>

  <form action="salvar_edicao_componente_temp.php" method="POST">
    <input type="hidden" name="id" value="<?= $componente['id'] ?>">

    <!-- Informações gerais -->
    <fieldset>
      <legend>Informações Gerais</legend>
      <label for="nome">Nome:</label>
      <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($componente['nome']) ?>" required>

      <label for="descricao">Descrição:</label>
      <textarea id="descricao" name="descricao" rows="4"><?= htmlspecialchars($componente['descricao']) ?></textarea>
    </fieldset>

    <!-- Materiais -->
    <h2>Materiais</h2>
    <?php foreach ($componente['materiais'] as $id => $m): ?>
      <fieldset>
        <legend><?= getNome($pdo, 'materiais', 'id_material', 'nome_material', $id) ?></legend>
        <input type="hidden" name="materiais[<?= $id ?>][id]" value="<?= $id ?>">

        <label>Quantidade:</label>
        <input type="text" name="materiais[<?= $id ?>][quantidade]" value="<?= htmlspecialchars($m['quantidade']) ?>">

        <label>Unidade:</label>
        <input type="text" name="materiais[<?= $id ?>][unidade]" value="<?= htmlspecialchars($m['unidade']) ?>">

        <label>Observação:</label>
        <input type="text" name="materiais[<?= $id ?>][observacao]" value="<?= htmlspecialchars($m['observacao']) ?>">
      </fieldset>
    <?php endforeach; ?>

    <!-- Ferramentas -->
    <h2>Ferramentas</h2>
    <?php foreach ($componente['ferramentas'] as $id => $f): ?>
      <fieldset>
        <legend><?= getNome($pdo, 'ferramentas', 'id_ferramenta', 'nome_ferramenta', $id) ?></legend>
        <input type="hidden" name="ferramentas[<?= $id ?>][id]" value="<?= $id ?>">

        <label>Dimensões:</label>
        <input type="text" name="ferramentas[<?= $id ?>][dimensoes]" value="<?= htmlspecialchars($f['dimensoes']) ?>">

        <label>Observação:</label>
        <input type="text" name="ferramentas[<?= $id ?>][observacao]" value="<?= htmlspecialchars($f['observacao']) ?>">
      </fieldset>
    <?php endforeach; ?>

    <!-- Passo a Passo -->
    <h2>Passo a Passo</h2>
    <?php foreach ($componente['passos'] as $i => $p): ?>
      <fieldset>
        <legend>Passo <?= $i + 1 ?></legend>

        <label>Descrição:</label>
        <textarea name="passos[<?= $i ?>][descricao]" rows="3"><?= htmlspecialchars($p['descricao']) ?></textarea>

        <label>Materiais (IDs separados por vírgula):</label>
        <input type="text" name="passos[<?= $i ?>][materiais]" value="<?= htmlspecialchars(implode(',', $p['materiais'] ?? [])) ?>">

        <label>Ferramentas (IDs separados por vírgula):</label>
        <input type="text" name="passos[<?= $i ?>][ferramentas]" value="<?= htmlspecialchars(implode(',', $p['ferramentas'] ?? [])) ?>">

        <label>Imagens (caminhos separados por vírgula):</label>
        <input type="text" name="passos[<?= $i ?>][imagens]" value="<?= htmlspecialchars(implode(',', $p['imagens'] ?? [])) ?>">

        <label>Arquivos (caminhos separados por vírgula):</label>
        <input type="text" name="passos[<?= $i ?>][arquivos]" value="<?= htmlspecialchars(implode(',', $p['arquivos'] ?? [])) ?>">
      </fieldset>
    <?php endforeach; ?>

    <button type="submit">Salvar Alterações</button>
  </form>

  <div class="botoes">
    <a href="visualizar_componente_temp.php?id=<?= $componente['id'] ?>" class="btn-voltar">← Cancelar</a>
  </div>
</main>

<?php include 'footer.php'; ?>
<script src="js/editar_componente_temp.js"></script>
</body>
</html>
