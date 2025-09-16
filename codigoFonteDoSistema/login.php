<?php
session_start();
include("conexao.php"); // conecta ao MySQL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);         // pega o email do form
    $senha = $_POST['password'];            // pega a senha do form (antes era 'senha')

    // Consulta no banco para ver se existe usuário com esse email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        // Comparação de senha em texto puro
        if ($senha === $user['password']) {
            $_SESSION['usuario'] = $user['email'];
            header("location:welcome.html"); // login ok → vai pra home
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
