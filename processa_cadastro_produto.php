<?php
session_start();
require 'conexao.php';

if (!isset($_SESSION['id_usuario'])) {
    die("Usuário não autenticado.");
}

$id_usuario = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();

    try {
        // Dados do produto
        $nome         = trim($_POST['nome_produto'] ?? '');
        $descricao    = trim($_POST['descricao'] ?? '');
        $para_quem    = trim($_POST['para_quem'] ?? '');
        $por_quem     = trim($_POST['por_quem'] ?? '');
        $testado_por  = trim($_POST['testado_por'] ?? null);
        $por_que      = trim($_POST['por_que'] ?? '');
        $para_que     = trim($_POST['para_que'] ?? '');
        $pre_requisitos = trim($_POST['pre_requisitos'] ?? '');
        $modo_de_uso  = trim($_POST['modo_de_uso'] ?? '');

        $uploadDir = "uploads/produtos/";
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // Imagens do produto
        $imagens = [];
        if (!empty($_FILES['imagens']['name'][0])) {
            foreach ($_FILES['imagens']['tmp_name'] as $i => $tmp) {
                if ($_FILES['imagens']['error'][$i] === 0 && is_uploaded_file($tmp)) {
                    $ext = pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION);
                    $nomeArq = uniqid('img_') . ".$ext";
                    $dest = $uploadDir . $nomeArq;
                    if (move_uploaded_file($tmp, $dest)) {
                        $imagens[] = $dest;
                    }
                }
            }
        }

        // Arquivos diversos
        $arquivos = [];
        if (!empty($_FILES['arquivos']['name'][0])) {
            foreach ($_FILES['arquivos']['tmp_name'] as $i => $tmp) {
                if ($_FILES['arquivos']['error'][$i] === 0 && is_uploaded_file($tmp)) {
                    $ext = pathinfo($_FILES['arquivos']['name'][$i], PATHINFO_EXTENSION);
                    $nomeArq = uniqid('file_') . ".$ext";
                    $dest = $uploadDir . $nomeArq;
                    if (move_uploaded_file($tmp, $dest)) {
                        $arquivos[] = $dest;
                    }
                }
            }
        }

        // Insere produto
        $stmt = $pdo->prepare("INSERT INTO produtos (
            id_usuario, nome_produto, descricao, para_quem, por_quem, testado_por,
            por_que, para_que, pre_requisitos, modo_de_uso, imagens, arquivos
        ) VALUES (
            :id_usuario, :nome, :descricao, :para_quem, :por_quem, :testado_por,
            :por_que, :para_que, :pre_requisitos, :modo_de_uso, :imagens, :arquivos
        )");

        $stmt->execute([
            ':id_usuario'     => $id_usuario,
            ':nome'           => $nome,
            ':descricao'      => $descricao,
            ':para_quem'      => $para_quem,
            ':por_quem'       => $por_quem,
            ':testado_por'    => $testado_por,
            ':por_que'        => $por_que,
            ':para_que'       => $para_que,
            ':pre_requisitos' => $pre_requisitos,
            ':modo_de_uso'    => $modo_de_uso,
            ':imagens'        => json_encode($imagens),
            ':arquivos'       => json_encode($arquivos)
        ]);

        $id_produto = $pdo->lastInsertId();

        // Componentes e suas dependências
        foreach ($_SESSION['componentes_temp'] ?? [] as $componente) {
            $stmtComp = $pdo->prepare("INSERT INTO componentes (
                id_produto, nome_componente, descricao, imagens, arquivos
            ) VALUES (?, ?, ?, ?, ?)");
            $stmtComp->execute([
                $id_produto,
                $componente['nome'],
                $componente['descricao'],
                json_encode($componente['imagens']),
                json_encode($componente['arquivos'])
            ]);
            $id_componente = $pdo->lastInsertId();

            // Materiais
            foreach ($componente['materiais'] ?? [] as $id_material => $m) {
                $check = $pdo->prepare("SELECT COUNT(*) FROM materiais WHERE id_material = ?");
                $check->execute([$id_material]);
                if ($check->fetchColumn() == 0) {
                    throw new Exception("Material ID $id_material não encontrado.");
                }

                $pdo->prepare("INSERT INTO componente_materiais (
                    id_componente, id_material, quantidade, unidade, observacao
                ) VALUES (?, ?, ?, ?, ?)")->execute([
                    $id_componente,
                    $id_material,
                    $m['quantidade'],
                    $m['unidade'],
                    $m['observacao'] ?? ''
                ]);
            }

            // Ferramentas
            foreach ($componente['ferramentas'] ?? [] as $id_ferramenta => $f) {
                $check = $pdo->prepare("SELECT COUNT(*) FROM ferramentas WHERE id_ferramenta = ?");
                $check->execute([$id_ferramenta]);
                if ($check->fetchColumn() == 0) {
                    throw new Exception("Ferramenta ID $id_ferramenta não encontrada.");
                }

                $pdo->prepare("INSERT INTO componente_ferramentas (
                    id_componente, id_ferramenta, dimensoes, observacao
                ) VALUES (?, ?, ?, ?)")->execute([
                    $id_componente,
                    $id_ferramenta,
                    $f['dimensoes'] ?? '',
                    $f['observacao'] ?? ''
                ]);
            }

            // Passo a passo
            foreach ($componente['passos'] ?? [] as $passo) {
                $stmtPasso = $pdo->prepare("INSERT INTO passo_a_passo (id_componente, descricao) VALUES (?, ?)");
                $stmtPasso->execute([$id_componente, $passo['descricao']]);
                $id_passo = $pdo->lastInsertId();

                foreach ($passo['materiais'] ?? [] as $id_mat) {
                    $check = $pdo->prepare("SELECT COUNT(*) FROM materiais WHERE id_material = ?");
                    $check->execute([$id_mat]);
                    if ($check->fetchColumn() == 0) {
                        throw new Exception("Material do passo ID $id_mat não encontrado.");
                    }

                    $pdo->prepare("INSERT INTO passo_materiais (id_passo, id_material) VALUES (?, ?)")
                        ->execute([$id_passo, $id_mat]);
                }

                foreach ($passo['ferramentas'] ?? [] as $id_fer) {
                    $check = $pdo->prepare("SELECT COUNT(*) FROM ferramentas WHERE id_ferramenta = ?");
                    $check->execute([$id_fer]);
                    if ($check->fetchColumn() == 0) {
                        throw new Exception("Ferramenta do passo ID $id_fer não encontrada.");
                    }

                    $pdo->prepare("INSERT INTO passo_ferramentas (id_passo, id_ferramenta) VALUES (?, ?)")
                        ->execute([$id_passo, $id_fer]);
                }
            }
        }

        // Processos de montagem
        foreach ($_POST['montagem'] ?? [] as $idHtml => $processo) {
            $descricao = trim($processo['descricao'] ?? '');
            $componentes = $processo['componentes'] ?? [];
            $midias = [];
            $arquivosProc = [];
            $prefixo = "montagem_{$idHtml}";

            // Midias
            if (!empty($_FILES[$prefixo]['name']['midias'][0])) {
                foreach ($_FILES[$prefixo]['tmp_name']['midias'] as $i => $tmp) {
                    if ($_FILES[$prefixo]['error']['midias'][$i] === 0 && is_uploaded_file($tmp)) {
                        $ext = pathinfo($_FILES[$prefixo]['name']['midias'][$i], PATHINFO_EXTENSION);
                        $nomeArq = uniqid("mid_") . ".$ext";
                        $dest = $uploadDir . $nomeArq;
                        if (move_uploaded_file($tmp, $dest)) $midias[] = $dest;
                    }
                }
            }

            // Arquivos
            if (!empty($_FILES[$prefixo]['name']['arquivos'][0])) {
                foreach ($_FILES[$prefixo]['tmp_name']['arquivos'] as $i => $tmp) {
                    if ($_FILES[$prefixo]['error']['arquivos'][$i] === 0 && is_uploaded_file($tmp)) {
                        $ext = pathinfo($_FILES[$prefixo]['name']['arquivos'][$i], PATHINFO_EXTENSION);
                        $nomeArq = uniqid("doc_") . ".$ext";
                        $dest = $uploadDir . $nomeArq;
                        if (move_uploaded_file($tmp, $dest)) $arquivosProc[] = $dest;
                    }
                }
            }

            $stmtProc = $pdo->prepare("INSERT INTO processos_montagem (id_produto, descricao, imagens, arquivos)
                                       VALUES (?, ?, ?, ?)");
            $stmtProc->execute([
                $id_produto,
                $descricao,
                json_encode($midias),
                json_encode($arquivosProc)
            ]);

            $id_processo = $pdo->lastInsertId();

            foreach ($componentes as $id_comp_usado) {
                $pdo->prepare("INSERT INTO processo_componentes (id_processo, id_componente)
                               VALUES (?, ?)")->execute([$id_processo, $id_comp_usado]);
            }
        }

        $pdo->commit();
        unset($_SESSION['componentes_temp']);
        header("Location: produto.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Erro ao cadastrar: " . $e->getMessage());
    }
}
