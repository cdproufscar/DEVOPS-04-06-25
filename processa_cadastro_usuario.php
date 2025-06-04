<?php
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = htmlspecialchars($_POST['nome']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    $secoes = $_POST['secoes'] ?? [];

    if ($senha !== $confirmar_senha) {
        echo "<script>
            alert('As senhas não coincidem!');
            window.location='cadastro_usuario.php';
        </script>";
        exit;
    }

    // Verifica duplicidade de e-mail
    $verifica = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
    $verifica->execute([':email' => $email]);
    if ($verifica->fetchColumn() > 0) {
        echo "<script>
            alert('Este e-mail já está cadastrado. Faça login ou use outro.');
            window.location='cadastro_usuario.php';
        </script>";
        exit;
    }

    $senha_hashed = password_hash($senha, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        $tipo_usuario = count($secoes) > 0 ? $secoes[0] : 'FAMILIAR';

        $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo_usuario)
                               VALUES (:nome, :email, :senha, :tipo_usuario)");
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha_hashed);
        $stmt->bindParam(':tipo_usuario', $tipo_usuario);
        $stmt->execute();

        $id_usuario = $pdo->lastInsertId();

        // Insere perfil/classificações
        foreach ($secoes as $secao) {
            $stmtPerfil = $pdo->prepare("INSERT INTO perfil_usuario (id_usuario, categoria, descricao)
                                         VALUES (:id_usuario, :categoria, '')");
            $stmtPerfil->execute([
                ':id_usuario' => $id_usuario,
                ':categoria' => $secao
            ]);
        }

        // Dados por seção
        if (in_array("FAMILIAR", $secoes)) {
            $relacao = $_POST['familiar_relacao'] ?? '';
            $tipo_deficiencia = $_POST['familiar_deficiencia'] ?? '';
            $descricao = $_POST['descricao_deficiencia_familiar'] ?? '';

            $stmtFam = $pdo->prepare("INSERT INTO dados_familiares (id_usuario, relacao, tipo_deficiencia, descricao)
                                      VALUES (:id, :relacao, :tipo, :descricao)");
            $stmtFam->execute([
                ':id' => $id_usuario,
                ':relacao' => $relacao,
                ':tipo' => $tipo_deficiencia,
                ':descricao' => $descricao
            ]);
        }

        if (in_array("PCD", $secoes)) {
            $deficiencia = $_POST['pcd_deficiencia'] ?? '';
            $limitacoes = $_POST['pcd_limitacoes'] ?? ''; // corrigido aqui

            $stmtPCD = $pdo->prepare("INSERT INTO dados_pcd (id_usuario, deficiencia, limitacoes)
                                      VALUES (:id, :deficiencia, :limitacoes)");
            $stmtPCD->execute([
                ':id' => $id_usuario,
                ':deficiencia' => $deficiencia,
                ':limitacoes' => $limitacoes
            ]);
        }

        if (in_array("MAKER", $secoes)) {
            $formacao = $_POST['maker_projetista_formacao'] ?? '';

            $stmtMaker = $pdo->prepare("INSERT INTO dados_maker (id_usuario, formacao)
                                        VALUES (:id, :formacao)");
            $stmtMaker->execute([
                ':id' => $id_usuario,
                ':formacao' => $formacao
            ]);
        }

        if (in_array("ESPECIALISTA DA SAÚDE", $secoes)) {
            $formacao = $_POST['especialista_saude_formacao'] ?? '';

            $stmtEsp = $pdo->prepare("INSERT INTO dados_especialista (id_usuario, formacao)
                                      VALUES (:id, :formacao)");
            $stmtEsp->execute([
                ':id' => $id_usuario,
                ':formacao' => $formacao
            ]);
        }

        if (in_array("FORNECEDOR", $secoes)) {
            $atuacao = $_POST['fornecedor_atuacao'] ?? '';

            $stmtForn = $pdo->prepare("INSERT INTO dados_fornecedor (id_usuario, atuacao)
                                       VALUES (:id, :atuacao)");
            $stmtForn->execute([
                ':id' => $id_usuario,
                ':atuacao' => $atuacao
            ]);
        }

        $pdo->commit();
        echo "<script>
            alert('Cadastro realizado com sucesso! Faça login.');
            window.location='login.php';
        </script>";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>
            alert('Erro ao cadastrar: " . addslashes($e->getMessage()) . "' );
            window.location='cadastro_usuario.php';
        </script>";
    }
}
?>
