<?php
session_start();

if (!isset($_GET['id'])) {
    die("ID inválido.");
}

$id = $_GET['id'];
$componentes = $_SESSION['componentes_temp'] ?? [];

foreach ($componentes as $i => $comp) {
    if ($comp['id'] == $id) {
        unset($componentes[$i]);
        $_SESSION['componentes_temp'] = array_values($componentes); // reorganiza os índices
        header("Location: cadastro_produto.php");
        exit;
    }
}

die("Componente não encontrado.");
