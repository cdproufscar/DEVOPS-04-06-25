<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Se não for POST, redireciona de volta
    header("Location: cadastro_componente.php");
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');

if ($nome === '' || $descricao === '') {
    $_SESSION['erro_componente'] = "Nome e descrição são obrigatórios!";
    header("Location: cadastro_componente.php");
    exit;
}

// Diretório de upload para componentes
$uploadDir = 'uploads/componentes/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ===== 1) Processa upload de imagens do componente =====
$imagens = [];
if (!empty($_FILES['imagens']['name'][0])) {
    foreach ($_FILES['imagens']['tmp_name'] as $i => $tmp) {
        if (isset($_FILES['imagens']['error'][$i]) && $_FILES['imagens']['error'][$i] === 0) {
            $ext = pathinfo($_FILES['imagens']['name'][$i], PATHINFO_EXTENSION);
            $nomeArq = uniqid('img_') . ".$ext";
            $caminho = $uploadDir . $nomeArq;
            if (move_uploaded_file($tmp, $caminho)) {
                $imagens[] = $caminho;
            }
        }
    }
}

// ===== 2) Processa upload de arquivos gerais do componente =====
$arquivos = [];
if (!empty($_FILES['arquivos']['name'][0])) {
    foreach ($_FILES['arquivos']['tmp_name'] as $i => $tmp) {
        if (isset($_FILES['arquivos']['error'][$i]) && $_FILES['arquivos']['error'][$i] === 0) {
            $ext = pathinfo($_FILES['arquivos']['name'][$i], PATHINFO_EXTENSION);
            $nomeArq = uniqid('file_') . ".$ext";
            $caminho = $uploadDir . $nomeArq;
            if (move_uploaded_file($tmp, $caminho)) {
                $arquivos[] = $caminho;
            }
        }
    }
}

// ===== 3) Monta lista de Materiais =====
// Cada entrada de $_POST['materiais'] deve ter: ['id'], ['quantidade'], ['unidade'], ['observacao']
$materiais = [];
foreach ($_POST['materiais'] ?? [] as $m) {
    $id_mat = (int)($m['id'] ?? 0);
    if ($id_mat <= 0) {
        // pula entradas inválidas ou vazias
        continue;
    }
    $materiais[] = [
        'id'         => $id_mat,
        'quantidade' => trim($m['quantidade'] ?? ''),
        'unidade'    => trim($m['unidade'] ?? ''),
        'observacao' => trim($m['observacao'] ?? '')
    ];
}

// ===== 4) Monta lista de Ferramentas =====
// Cada entrada de $_POST['ferramentas'] deve ter: ['id'], ['observacao'], ['dimensoes'] (opcional)
$ferramentas = [];
foreach ($_POST['ferramentas'] ?? [] as $f) {
    $id_fer = (int)($f['id'] ?? 0);
    if ($id_fer <= 0) {
        continue;
    }
    $ferramentas[] = [
        'id'        => $id_fer,
        'observacao'=> trim($f['observacao'] ?? ''),
        // se precisar de dimensões, pode adicionar aqui: 'dimensoes' => trim($f['dimensoes'] ?? '')
    ];
}

// ===== 5) Monta lista de Passos =====
// Verifica $_POST['passos'] usando null coalescing para evitar “undefined index”
$passos = [];
foreach ($_POST['passos'] ?? [] as $idPasso => $dados) {
    // Para cada $dados, esperamos: ['descricao'], ['materiais'] (array), ['ferramentas'] (array)
    $descricao_passo = trim($dados['descricao'] ?? '');
    if ($descricao_passo === '') {
        // passo sem descrição => pula
        continue;
    }

    $passo = [
        'descricao'  => $descricao_passo,
        'materiais'  => array_filter(array_map('intval', $dados['materiais']  ?? [])),
        'ferramentas'=> array_filter(array_map('intval', $dados['ferramentas'] ?? [])),
        'imagens'    => [],
        'arquivos'   => []
    ];

    // prefixo usado pelo JS para nomear inputs de arquivo: “passos_${idPasso}[imagens][]” e “passos_${idPasso}[arquivos][]”
    $keyPrefix = "passos_{$idPasso}";

    // Processa imagens deste passo
    if (!empty($_FILES[$keyPrefix]['name']['imagens'][0] ?? [])) {
        foreach ($_FILES[$keyPrefix]['tmp_name']['imagens'] as $i => $tmp) {
            if (isset($_FILES[$keyPrefix]['error']['imagens'][$i]) 
                && $_FILES[$keyPrefix]['error']['imagens'][$i] === 0
            ) {
                $ext = pathinfo($_FILES[$keyPrefix]['name']['imagens'][$i], PATHINFO_EXTENSION);
                $nomeArq = uniqid("passo_img_") . ".$ext";
                $caminho = $uploadDir . $nomeArq;
                if (move_uploaded_file($tmp, $caminho)) {
                    $passo['imagens'][] = $caminho;
                }
            }
        }
    }

    // Processa arquivos deste passo
    if (!empty($_FILES[$keyPrefix]['name']['arquivos'][0] ?? [])) {
        foreach ($_FILES[$keyPrefix]['tmp_name']['arquivos'] as $i => $tmp) {
            if (isset($_FILES[$keyPrefix]['error']['arquivos'][$i]) 
                && $_FILES[$keyPrefix]['error']['arquivos'][$i] === 0
            ) {
                $ext = pathinfo($_FILES[$keyPrefix]['name']['arquivos'][$i], PATHINFO_EXTENSION);
                $nomeArq = uniqid("passo_file_") . ".$ext";
                $caminho = $uploadDir . $nomeArq;
                if (move_uploaded_file($tmp, $caminho)) {
                    $passo['arquivos'][] = $caminho;
                }
            }
        }
    }

    $passos[] = $passo;
}

// ===== 6) Agora salva tudo em sessão =====
$_SESSION['componentes_temp'][] = [
    'id'          => count($_SESSION['componentes_temp'] ?? []) + 1,
    'nome'        => $nome,
    'descricao'   => $descricao,
    'imagens'     => $imagens,
    'arquivos'    => $arquivos,
    'materiais'   => $materiais,
    'ferramentas' => $ferramentas,
    'passos'      => $passos
];

$_SESSION['sucesso_componente'] = "Componente cadastrado com sucesso!";
header("Location: cadastro_produto.php");
exit;
