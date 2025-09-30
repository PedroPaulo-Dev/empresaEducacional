<?php
include("conexao.php");

$email = "dias@gmail.com";
$senha_nova = "12345"; // senha atual

// Criptografa a senha
$senha_cript = password_hash($senha_nova, PASSWORD_DEFAULT);

// Atualiza no banco
$stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
$stmt->bind_param("ss", $senha_cript, $email);

if($stmt->execute()){
    echo "Senha criptografada atualizada com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
