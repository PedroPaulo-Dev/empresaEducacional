<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: login.html");
    exit();
}

include(__DIR__ . "/../controles/conexao.php");



// Buscar dados do usuário logado
$email = $_SESSION['usuario'];
$stmt = $conn->prepare("SELECT username FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Buscar todos os usuários
$users = $conn->query("SELECT id, username, email, role FROM users");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Bem-vindo - EstudaEnem</title>
<style>
body { font-family: Arial; background:#f1f1f1; padding:20px; }
.container { max-width:800px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
h1 { color:#1a73e8; }
table { width:100%; border-collapse:collapse; margin-top:20px; }
table, th, td { border:1px solid #ccc; }
th, td { padding:10px; text-align:left; }
a { text-decoration:none; color:#1a73e8; margin-right:10px; }
a.button { background:#1a73e8; color:#fff; padding:5px 10px; border-radius:4px; }
a.button:hover { background:#1666c1; }
</style>
</head>
<body>
<div class="container">
  <h1>Bem-vindo(a), <?= htmlspecialchars($user['username']) ?>!</h1>
  <a href="../controles/logout.php" class="button">Sair</a>
  <a href="../controles/criar.php" class="button">Adicionar Usuário</a>

  <h2>Todos os usuários</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Role</th>
      <th>Ações</th>
    </tr>
    <?php while($row = $users->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= htmlspecialchars($row['username']) ?></td>
      <td><?= htmlspecialchars($row['email']) ?></td>
      <td><?= htmlspecialchars($row['role']) ?></td>
      <td>
        <a href="../controles/editar.php?id=<?= $row['id'] ?>">Editar</a>
        <a href="../controles/deletar.php?id=<?= $row['id'] ?>" onclick="return confirm('Tem certeza?')">Deletar</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
</body>
</html>
