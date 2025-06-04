<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);

    if ($stmt->rowCount() > 0) {
        $token = bin2hex(random_bytes(32));
        $expira_em = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $insert = $pdo->prepare("INSERT INTO recuperacao_senhas (email, token, expira_em)
                                 VALUES (:email, :token, :expira_em)");
        $insert->execute([
            ':email' => $email,
            ':token' => $token,
            ':expira_em' => $expira_em
        ]);

        $link = "https://seusite.com/redefinir_senha.php?token=$token";

        // Aqui você pode usar mail() ou um serviço de e-mail (PHPMailer, SMTP etc.)
        // Exemplo básico:
        mail($email, "Redefinição de senha - Assistiverse", 
            "Clique no link abaixo para redefinir sua senha:\n\n$link\n\nEste link expira em 1 hora.",
            "From: no-reply@seusite.com");

        echo "<script>alert('E-mail enviado com sucesso. Verifique sua caixa de entrada.'); window.location='../login.php';</script>";
    } else {
        echo "<script>alert('E-mail não encontrado!'); window.location='../recuperacao_senha.php';</script>";
    }
}
?>
