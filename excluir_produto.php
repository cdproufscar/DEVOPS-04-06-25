<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$id_produto = $_GET['id'] ?? null;

if (!$id_produto) {
    die("ID do produto não fornecido.");
}

// Verifica se o produto pertence ao usuário
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE id_produto = :id_produto AND id_usuario = :id_usuario");
$stmt->execute([
    ':id_produto' => $id_produto,
    ':id_usuario' => $id_usuario
]);

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    die("Produto não encontrado ou você não tem permissão para excluí-lo.");
}

try {
    // Transação
    $pdo->beginTransaction();

    // Excluir componentes (deletados em cascata via FK)
    $pdo->prepare("DELETE FROM produtos WHERE id_produto = :id_produto")
        ->execute([':id_produto' => $id_produto]);

    $pdo->commit();
    header("Location: perfil_usuario.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    die("Erro ao excluir produto: " . $e->getMessage());
}
?>
