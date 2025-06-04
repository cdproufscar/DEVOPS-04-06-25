<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

require 'conexao.php';
$usuario_id = $_SESSION['id_usuario'];
$nome = trim($_POST['nome']);
$email = trim($_POST['email']);
$senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
$secoes = $_POST['secoes'] ?? [];

// FOTO
if (!empty($_FILES['foto']['name'])) {
    $diretorio = "uploads/";
    $foto = basename($_FILES["foto"]["name"]);
    $caminhoArquivo = $diretorio . $foto;

    if (!is_dir($diretorio)) mkdir($diretorio, 0777, true);

    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $caminhoArquivo)) {
        $sql = "UPDATE usuarios SET foto = :foto WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':id_usuario', $usuario_id);
        $stmt->execute();
    }
}

// ATUALIZA NOME/EMAIL/SENHA
if ($senha) {
    $sql = "UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id_usuario = :id_usuario";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':senha', $senha);
} else {
    $sql = "UPDATE usuarios SET nome = :nome, email = :email WHERE id_usuario = :id_usuario";
    $stmt = $pdo->prepare($sql);
}
$stmt->bindParam(':nome', $nome);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':id_usuario', $usuario_id);
$stmt->execute();

// CLASSIFICAÇÕES
$pdo->prepare("DELETE FROM perfil_usuario WHERE id_usuario = :id")->execute([':id' => $usuario_id]);
foreach ($secoes as $secao) {
    $pdo->prepare("INSERT INTO perfil_usuario (id_usuario, categoria, descricao) VALUES (:id, :categoria, '')")
        ->execute([':id' => $usuario_id, ':categoria' => $secao]);
}

// DADOS FAMILIARES
if (in_array("FAMILIAR", $secoes)) {
    $relacao = $_POST['familiar_relacao'] ?? '';
    $tipo_deficiencia = $_POST['familiar_deficiencia'] ?? '';
    $descricao = $_POST['descricao_deficiencia_familiar'] ?? '';

    $stmtCheck = $pdo->prepare("SELECT id FROM dados_familiares WHERE id_usuario = :id");
    $stmtCheck->execute([':id' => $usuario_id]);

    if ($stmtCheck->rowCount()) {
        $stmtUpdate = $pdo->prepare("UPDATE dados_familiares SET relacao = :rel, tipo_deficiencia = :tipo, descricao = :desc WHERE id_usuario = :id");
        $stmtUpdate->execute([
            ':rel' => $relacao,
            ':tipo' => $tipo_deficiencia,
            ':desc' => $descricao,
            ':id' => $usuario_id
        ]);
    } else {
        $stmtInsert = $pdo->prepare("INSERT INTO dados_familiares (id_usuario, relacao, tipo_deficiencia, descricao) VALUES (:id, :rel, :tipo, :desc)");
        $stmtInsert->execute([
            ':id' => $usuario_id,
            ':rel' => $relacao,
            ':tipo' => $tipo_deficiencia,
            ':desc' => $descricao
        ]);
    }
}

header("Location: perfil_usuario.php?sucesso=1");
exit();
