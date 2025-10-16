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
  <title>Editar Usuário</title>
</head>
<body>
  <h1>Editar Usuário</h1>
  <form method="POST">
    <input type="text" name="username" value="<?= $user['username'] ?>" required><br>
    <input type="email" name="email" value="<?= $user['email'] ?>" required><br>
    <input type="text" name="role" value="<?= $user['role'] ?>"><br>
    <button type="submit">Atualizar</button>
  </form>
  <a href="../visual/welcome.php">Voltar</a>
</body>
</html>
