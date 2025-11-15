<?php
include("conexao.php");

$id = $_GET['id'] ?? null;
if(!$id) exit("ID inválido");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: ../visual/welcome.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Excluir Usuário - EstudaEnem</title>
  <style>
    body {
      font-family: Arial;
      background: #f1f1f1;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      width: 400px;
    }
    h1 {
      color: #d93025;
      margin-bottom: 15px;
    }
    p {
      color: #333;
      margin-bottom: 25px;
    }
    button {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 15px;
    }
    .confirm {
      background: #d93025;
      color: #fff;
    }
    .confirm:hover {
      background: #b0251c;
    }
    .cancel {
      background: #ccc;
      margin-left: 10px;
    }
    .cancel:hover {
      background: #aaa;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Excluir Usuário</h1>
    <p>Tem certeza que deseja excluir este usuário?</p>
    <form method="POST">
      <button type="submit" class="confirm">Sim, excluir</button>
      <a href="../visual/welcome.php"><button type="button" class="cancel">Cancelar</button></a>
    </form>
  </div>
</body>
</html>
