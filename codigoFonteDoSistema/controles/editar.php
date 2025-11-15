<?php
include("conexao.php");

$id = $_GET['id'] ?? null;
if(!$id) exit("ID inválido");

// Buscar dados existentes
$stmt = $conn->prepare("SELECT username, email, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
    $stmt->bind_param("sssi", $username, $email, $role, $id);
    $stmt->execute();
    header("Location: ../visual/welcome.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuário - EstudaEnem</title>
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
    <h1>Editar Usuário</h1>
    <form method="POST">
      <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
      <input type="text" name="role" value="<?= htmlspecialchars($user['role']) ?>">
      <button type="submit">Atualizar</button>
    </form>
    <a href="../visual/welcome.php">← Voltar</a>
  </div>
</body>
</html>
