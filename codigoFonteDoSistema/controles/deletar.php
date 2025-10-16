<?php
include("conexao.php");

$id = $_GET['id'] ?? null;
if($id){
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}
header("Location: ../visual/welcome.php");
exit;
?>
