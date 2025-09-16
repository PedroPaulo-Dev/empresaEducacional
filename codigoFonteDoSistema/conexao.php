<?php
$servername = "localhost";
$username   = "root";
$password   = ""; // se tiver senha no MySQL, coloca aqui
$dbname     = "empresa_educacional";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>
