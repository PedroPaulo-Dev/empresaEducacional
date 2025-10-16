<?php
session_start();
include("conexao.php"); // conexão com banco

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['password'];

    // busca usuário pelo email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // verifica senha criptografada
        if (password_verify($senha, $user['password'])) {
            $_SESSION['usuario'] = $user['email'];
            header("Location: ../visual/welcome.php"); // redireciona
            exit();
        } else {
            echo "<p style='color:red; text-align:center;'>Senha incorreta!</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>Usuário não encontrado!</p>";
    }

    $stmt->close();
}
$conn->close();
?>
