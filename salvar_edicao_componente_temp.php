<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    die("Requisição inválida.");
}

$id = $_POST['id'];
$componentes = $_SESSION['componentes_temp'] ?? [];
$indice = null;

// Localiza componente por ID
foreach ($componentes as $i => $comp) {
    if ($comp['id'] == $id) {
        $indice = $i;
        break;
    }
}

if ($indice === null) {
    die("Componente não encontrado na sessão.");
}

// Atualiza nome e descrição
$componentes[$indice]['nome'] = trim($_POST['nome'] ?? $componentes[$indice]['nome']);
$componentes[$indice]['descricao'] = trim($_POST['descricao'] ?? $componentes[$indice]['descricao']);

// Atualiza materiais
$componentes[$indice]['materiais'] = [];
if (!empty($_POST['materiais'])) {
    foreach ($_POST['materiais'] as $id => $m) {
        $componentes[$indice]['materiais'][$id] = [
            'quantidade'  => trim($m['quantidade'] ?? ''),
            'unidade'     => trim($m['unidade'] ?? ''),
            'observacao'  => trim($m['observacao'] ?? '')
        ];
    }
}

// Atualiza ferramentas
$componentes[$indice]['ferramentas'] = [];
if (!empty($_POST['ferramentas'])) {
    foreach ($_POST['ferramentas'] as $id => $f) {
        $componentes[$indice]['ferramentas'][$id] = [
            'dimensoes'   => trim($f['dimensoes'] ?? ''),
            'observacao'  => trim($f['observacao'] ?? '')
        ];
    }
}

// Atualiza passos
$componentes[$indice]['passos'] = [];
if (!empty($_POST['passos'])) {
    foreach ($_POST['passos'] as $p) {
        $componentes[$indice]['passos'][] = [
            'descricao'   => trim($p['descricao'] ?? ''),
            'materiais'   => array_filter(array_map('trim', explode(',', $p['materiais'] ?? ''))),
            'ferramentas' => array_filter(array_map('trim', explode(',', $p['ferramentas'] ?? ''))),
            'imagens'     => array_filter(array_map('trim', explode(',', $p['imagens'] ?? ''))),
            'arquivos'    => array_filter(array_map('trim', explode(',', $p['arquivos'] ?? '')))
        ];
    }
}

// Regrava na sessão
$_SESSION['componentes_temp'] = array_values($componentes);
$_SESSION['sucesso_componente'] = "Componente atualizado com sucesso!";
header("Location: visualizar_componente_temp.php?id=$id");
exit;
