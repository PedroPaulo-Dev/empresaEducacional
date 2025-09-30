<?php
include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = trim($_POST['role']);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if($stmt->execute()){
        header("Location: welcome.php"); // volta para a lista de usuários
        exit;
    } else {
        echo "Erro ao criar usuário: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Criar Usuário</title>
</head>
<body>
  <h1>Criar Usuário</h1>
  <!-- Adicione action="" para deixar explícito que o form envia para esta página -->
  <form method="POST" action="">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Senha" required><br>
    <input type="text" name="role" placeholder="Role"><br>
    <button type="submit">Criar</button>
  </form>
  <a href="welcome.php">Voltar</a>
</body>
</html>
