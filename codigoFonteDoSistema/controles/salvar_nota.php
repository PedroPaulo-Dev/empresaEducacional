<?php
session_start();
include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ver_redacoes.php");
    exit;
}

$id_redacao = $_POST['id_redacao'];
$nota = $_POST['nota'];
$comentario = $_POST['comentario'];

$stmt = $conn->prepare("UPDATE redacoes SET nota = ?, comentario = ? WHERE id = ?");
$stmt->bind_param("dsi", $nota, $comentario, $id_redacao);

if ($stmt->execute()) {
    header("Location: ver_redacoes.php?ok=1");
} else {
    echo "Erro ao salvar a nota.";
}
?>
