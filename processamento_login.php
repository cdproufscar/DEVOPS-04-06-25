<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    try {
        $stmt = $pdo->prepare("SELECT id_usuario, nome, email, senha, tipo_usuario, foto FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {
            session_regenerate_id(true);

            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
            $_SESSION['foto'] = $user['foto'] ?? 'user_nulo.png';

            // Opcional: Atualiza último login
            $log = $pdo->prepare("UPDATE usuarios SET data_cadastro = NOW() WHERE id_usuario = :id");
            $log->execute([':id' => $user['id_usuario']]);

            header("Location: perfil_usuario.php");
            exit;
        } else {
            echo "<script>
                alert('Email ou senha incorretos!');
                window.location='login.php';
            </script>";
            exit;
        }

    } catch (Exception $e) {
        echo "<script>
            alert('Erro interno ao tentar login: " . addslashes($e->getMessage()) . "');
            window.location='login.php';
        </script>";
        exit;
    }
}
?>
