<?php
require '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $senha = $_POST['nova_senha'] ?? '';
    $confirmar = $_POST['confirmar_senha'] ?? '';

    if ($senha !== $confirmar) {
        echo "<script>alert('As senhas não coincidem!'); history.back();</script>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT email FROM recuperacao_senhas WHERE token = :token AND expira_em >= NOW()");
    $stmt->execute([':token' => $token]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($registro) {
        $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE email = :email");
        $update->execute([
            ':senha' => $senha_hashed,
            ':email' => $registro['email']
        ]);

        // Remove token após uso
        $delete = $pdo->prepare("DELETE FROM recuperacao_senhas WHERE token = :token");
        $delete->execute([':token' => $token]);

        echo "<script>alert('Senha redefinida com sucesso! Faça login.'); window.location='../login.php';</script>";
    } else {
        echo "<script>alert('Token inválido ou expirado.'); window.location='../recuperacao_senha.php';</script>";
    }
}
?>
