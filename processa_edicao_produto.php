<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    die("Usuário não autenticado.");
}

$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produto     = $_POST['id_produto'] ?? null;
    $nome_produto   = $_POST['nome_produto'] ?? '';
    $descricao      = $_POST['descricao'] ?? '';
    $para_quem      = $_POST['para_quem'] ?? '';
    $por_quem       = $_POST['por_quem'] ?? '';
    $por_que        = $_POST['por_que'] ?? '';
    $para_que       = $_POST['para_que'] ?? '';
    $pre_requisitos = $_POST['pre_requisitos'] ?? '';
    $modo_de_uso    = $_POST['modo_de_uso'] ?? '';

    if (!$id_produto) {
        die("ID do produto não fornecido.");
    }

    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id_produto = :id AND id_usuario = :usuario");
    $stmt->execute([':id' => $id_produto, ':usuario' => $id_usuario]);
    if (!$stmt->fetch()) {
        die("Produto não encontrado ou acesso negado.");
    }

    try {
        $pdo->beginTransaction();

        $uploadDir = "uploads/produtos/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // Upload imagens do produto
        $imagens = [];
        if (!empty($_FILES['imagens']['name'][0])) {
            foreach ($_FILES['imagens']['tmp_name'] as $i => $tmp) {
                if ($_FILES['imagens']['error'][$i] === 0) {
                    $ext = pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION);
                    $file = uniqid("img_") . ".$ext";
                    $path = $uploadDir . $file;
                    if (move_uploaded_file($tmp, $path)) {
                        $imagens[] = $path;
                    }
                }
            }
        }

        // Upload arquivos do produto
        $arquivos = [];
        if (!empty($_FILES['arquivos']['name'][0])) {
            foreach ($_FILES['arquivos']['tmp_name'] as $i => $tmp) {
                if ($_FILES['arquivos']['error'][$i] === 0) {
                    $ext = pathinfo($_FILES['arquivos']['name'][$i], PATHINFO_EXTENSION);
                    $file = uniqid("file_") . ".$ext";
                    $path = $uploadDir . $file;
                    if (move_uploaded_file($tmp, $path)) {
                        $arquivos[] = $path;
                    }
                }
            }
        }

        // Atualiza produto
        $sql = "UPDATE produtos SET 
            nome_produto = :nome_produto,
            descricao = :descricao,
            para_quem = :para_quem,
            por_quem = :por_quem,
            por_que = :por_que,
            para_que = :para_que,
            pre_requisitos = :pre_requisitos,
            modo_de_uso = :modo_de_uso";

        if (!empty($imagens))  $sql .= ", imagens = :imagens";
        if (!empty($arquivos)) $sql .= ", arquivos = :arquivos";

        $sql .= " WHERE id_produto = :id_produto AND id_usuario = :id_usuario";

        $params = [
            ':nome_produto'   => $nome_produto,
            ':descricao'      => $descricao,
            ':para_quem'      => $para_quem,
            ':por_quem'       => $por_quem,
            ':por_que'        => $por_que,
            ':para_que'       => $para_que,
            ':pre_requisitos' => $pre_requisitos,
            ':modo_de_uso'    => $modo_de_uso,
            ':id_produto'     => $id_produto,
            ':id_usuario'     => $id_usuario
        ];
        if (!empty($imagens))  $params[':imagens'] = json_encode($imagens);
        if (!empty($arquivos)) $params[':arquivos'] = json_encode($arquivos);

        $pdo->prepare($sql)->execute($params);

        // Atualiza os componentes vinculados
        if (!empty($_POST['componentes'])) {
            foreach ($_POST['componentes'] as $id_comp => $dados) {
                $nome_comp = $dados['nome'] ?? '';
                $desc_comp = $dados['descricao'] ?? '';
                $materiais = $dados['materiais'] ?? [];
                $ferramentas = $dados['ferramentas'] ?? [];

                // Upload imagens do componente
                $img_comp = [];
                if (!empty($_FILES["componentes_$id_comp"]['name']['imagens'][0])) {
                    foreach ($_FILES["componentes_$id_comp"]['tmp_name']['imagens'] as $i => $tmp) {
                        if ($_FILES["componentes_$id_comp"]['error']['imagens'][$i] === 0) {
                            $ext = pathinfo($_FILES["componentes_$id_comp"]['name']['imagens'][$i], PATHINFO_EXTENSION);
                            $file = uniqid("compimg_") . ".$ext";
                            $path = $uploadDir . $file;
                            move_uploaded_file($tmp, $path);
                            $img_comp[] = $path;
                        }
                    }
                }

                // Upload arquivos do componente
                $arq_comp = [];
                if (!empty($_FILES["componentes_$id_comp"]['name']['arquivos'][0])) {
                    foreach ($_FILES["componentes_$id_comp"]['tmp_name']['arquivos'] as $i => $tmp) {
                        if ($_FILES["componentes_$id_comp"]['error']['arquivos'][$i] === 0) {
                            $ext = pathinfo($_FILES["componentes_$id_comp"]['name']['arquivos'][$i], PATHINFO_EXTENSION);
                            $file = uniqid("compfile_") . ".$ext";
                            $path = $uploadDir . $file;
                            move_uploaded_file($tmp, $path);
                            $arq_comp[] = $path;
                        }
                    }
                }

                $sqlComp = "UPDATE componentes SET 
                    nome_componente = :nome,
                    descricao = :descricao";

                if (!empty($img_comp)) $sqlComp .= ", imagens = :imagens";
                if (!empty($arq_comp)) $sqlComp .= ", arquivos = :arquivos";

                $sqlComp .= " WHERE id_componente = :id AND id_produto = :prod";

                $paramsComp = [
                    ':nome' => $nome_comp,
                    ':descricao' => $desc_comp,
                    ':id' => $id_comp,
                    ':prod' => $id_produto
                ];
                if (!empty($img_comp))  $paramsComp[':imagens'] = json_encode($img_comp);
                if (!empty($arq_comp))  $paramsComp[':arquivos'] = json_encode($arq_comp);

                $pdo->prepare($sqlComp)->execute($paramsComp);

                // Limpa vínculos antigos e insere novos
                $pdo->prepare("DELETE FROM componente_materiais WHERE id_componente = :id")->execute([':id' => $id_comp]);
                foreach ($materiais as $id_mat) {
                    $pdo->prepare("INSERT INTO componente_materiais (id_componente, id_material) VALUES (:comp, :mat)")
                        ->execute([':comp' => $id_comp, ':mat' => $id_mat]);
                }

                $pdo->prepare("DELETE FROM componente_ferramentas WHERE id_componente = :id")->execute([':id' => $id_comp]);
                foreach ($ferramentas as $id_fer) {
                    $pdo->prepare("INSERT INTO componente_ferramentas (id_componente, id_ferramenta) VALUES (:comp, :fer)")
                        ->execute([':comp' => $id_comp, ':fer' => $id_fer]);
                }
            }
        }

        $pdo->commit();
        header("Location: produto_detalhado.php?id=" . $id_produto);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao atualizar produto: " . $e->getMessage());
    }
} else {
    header("Location: perfil_usuario.php");
    exit();
}
