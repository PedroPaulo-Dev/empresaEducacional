<?php
include("conexao.php");

if ($conn->connect_error) {
    echo "Falha na conexão: " . $conn->connect_error;
} else {
    echo "Conexão funcionando!";
}
?>
