<?php
session_start();
header('Content-Type: application/json');
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não autenticado.']);
    exit;
}

try {
    $id = $_SESSION['id_usuario'];
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = !empty($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null;
    $secoes = $_POST['secoes'] ?? [];

    $tipo_usuario = count($secoes) > 0 ? $secoes[0] : 'FAMILIAR';

    $pdo->beginTransaction();

    // 📷 Upload da foto (com nome único)
    if (!empty($_FILES['foto']['name'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nomeUnico = 'user_' . $id . '_' . time() . '.' . $ext;
        $destino = "uploads/" . $nomeUnico;
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $stmtFoto = $pdo->prepare("UPDATE usuarios SET foto = :foto WHERE id_usuario = :id");
            $stmtFoto->execute([':foto' => $nomeUnico, ':id' => $id]);
        }
    }

    // 👤 Atualizar usuário
    if ($senha) {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, senha = :senha, tipo_usuario = :tipo WHERE id_usuario = :id");
        $stmt->bindParam(':senha', $senha);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, tipo_usuario = :tipo WHERE id_usuario = :id");
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':tipo', $tipo_usuario);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Classificações
    $pdo->prepare("DELETE FROM perfil_usuario WHERE id_usuario = :id")->execute([':id' => $id]);
    foreach ($secoes as $secao) {
        $pdo->prepare("INSERT INTO perfil_usuario (id_usuario, categoria, descricao) VALUES (:id, :cat, '')")
            ->execute([':id' => $id, ':cat' => $secao]);
    }

    // Seções extras
    if (in_array("FAMILIAR", $secoes)) {
        $relacao = $_POST['familiar_relacao'] ?? '';
        $tipo = $_POST['familiar_deficiencia'] ?? '';
        $descricao = $_POST['descricao_deficiencia_familiar'] ?? '';

        $check = $pdo->prepare("SELECT id FROM dados_familiares WHERE id_usuario = :id");
        $check->execute([':id' => $id]);

        $sql = $check->rowCount()
            ? "UPDATE dados_familiares SET relacao = :r, tipo_deficiencia = :t, descricao = :d WHERE id_usuario = :id"
            : "INSERT INTO dados_familiares (relacao, tipo_deficiencia, descricao, id_usuario) VALUES (:r, :t, :d, :id)";
        $pdo->prepare($sql)->execute([
            ':r' => $relacao,
            ':t' => $tipo,
            ':d' => $descricao,
            ':id' => $id
        ]);
    }

    if (in_array("PCD", $secoes)) {
        // ⚠️ Corrigir para 'pcd_limitações' com acento, pois o <textarea> está com acento no name
        $deficiencia = $_POST['pcd_deficiencia'] ?? '';
        $limitacoes  = $_POST['pcd_limitações'] ?? ''; // com acento, para bater com o form
    
        $check = $pdo->prepare("SELECT id FROM dados_pcd WHERE id_usuario = :id");
        $check->execute([':id' => $id]);
    
        $sql = $check->rowCount()
            ? "UPDATE dados_pcd SET deficiencia = :def, limitacoes = :lim WHERE id_usuario = :id"
            : "INSERT INTO dados_pcd (id_usuario, deficiencia, limitacoes) VALUES (:id, :def, :lim)";
        $pdo->prepare($sql)->execute([
            ':id' => $id,
            ':def' => $deficiencia,
            ':lim' => $limitacoes
        ]);
    }
    

    if (in_array("MAKER", $secoes)) {
        $formacao = $_POST['maker_projetista_formacao'] ?? '';
        $check = $pdo->prepare("SELECT id FROM dados_maker WHERE id_usuario = :id");
        $check->execute([':id' => $id]);

        $sql = $check->rowCount()
            ? "UPDATE dados_maker SET formacao = :f WHERE id_usuario = :id"
            : "INSERT INTO dados_maker (id_usuario, formacao) VALUES (:id, :f)";
        $pdo->prepare($sql)->execute([':id' => $id, ':f' => $formacao]);
    }

    if (in_array("ESPECIALISTA DA SAÚDE", $secoes)) {
        $formacao = $_POST['especialista_saude_formacao'] ?? '';
        $check = $pdo->prepare("SELECT id FROM dados_especialista WHERE id_usuario = :id");
        $check->execute([':id' => $id]);

        $sql = $check->rowCount()
            ? "UPDATE dados_especialista SET formacao = :f WHERE id_usuario = :id"
            : "INSERT INTO dados_especialista (id_usuario, formacao) VALUES (:id, :f)";
        $pdo->prepare($sql)->execute([':id' => $id, ':f' => $formacao]);
    }

    if (in_array("FORNECEDOR", $secoes)) {
        $atuacao = $_POST['fornecedor_atuacao'] ?? '';
        $check = $pdo->prepare("SELECT id FROM dados_fornecedor WHERE id_usuario = :id");
        $check->execute([':id' => $id]);

        $sql = $check->rowCount()
            ? "UPDATE dados_fornecedor SET atuacao = :a WHERE id_usuario = :id"
            : "INSERT INTO dados_fornecedor (id_usuario, atuacao) VALUES (:id, :a)";
        $pdo->prepare($sql)->execute([':id' => $id, ':a' => $atuacao]);
    }

    $pdo->commit();
    echo json_encode(['status' => 'ok', 'mensagem' => 'Perfil atualizado com sucesso!']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao atualizar: ' . $e->getMessage()]);
}
