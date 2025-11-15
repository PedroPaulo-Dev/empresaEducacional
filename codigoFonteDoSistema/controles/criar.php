<?php
include("conexao.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = trim($_POST['role']);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        header("Location: ../visual/welcome.php"); // volta para a lista de usuários
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
  <title>Criar Usuário - EstudaEnem</title>
  <style>
    body {
      font-family: Arial;
      background: #f1f1f1;
      padding: 20px;
    }
    .container {
      max-width: 500px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    h1 {
      color: #1a73e8;
      text-align: center;
      margin-bottom: 20px;
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    input {
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }
    button {
      background: #1a73e8;
      color: #fff;
      border: none;
      padding: 10px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background: #1666c1;
    }
    a {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      color: #1a73e8;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Criar Usuário</h1>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Senha" required>
      <input type="text" name="role" placeholder="Role (opcional)">
      <button type="submit">Criar</button>
    </form>
    <a href="../visual/welcome.php">← Voltar</a>
  </div>
</body>
</html>
