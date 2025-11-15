<?php
// ⚠️ Sempre no topo
session_start();

// Mostra erros (útil enquanto ajusta)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("conexao.php"); // mesma pasta

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $senha = $_POST['password'];

    // Busca usuário no banco
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();

        // Verifica a senha criptografada
        if (password_verify($senha, $user['password'])) {

            // Cria sessão
            $_SESSION['usuario'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];

            // Redireciona conforme o tipo de conta
            if ($user['role'] === 'aluno') {
                header("Location: ../visual/aluno_dashboard.php");
                exit();
            } elseif ($user['role'] === 'professor') {
                // 👇 Corrigido para o seu caso
                header("Location: professor_dashboard.php");
                exit();
            } elseif ($user['role'] === 'admin') {
                header("Location: ../visual/welcome.php");
                exit();
            } else {
                echo "<p style='color:red; text-align:center;'>Tipo de usuário desconhecido!</p>";
            }

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
