<?php
session_start();
include("conexao.php");

// Verifica login
if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'professor') {
    header("Location: ../visual/login.html");
    exit;
}

$email = $_SESSION['usuario'];

// Busca dados do professor
$stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$professor = $res->fetch_assoc();
$username = $professor['username'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Professor</title>
    <style>
        body {
            margin: 0;
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(to bottom right, #007bff, #cfe9ff);
        }

        .container {
            width: 80%;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            color: #0056b3;
        }

        .btn {
            display: block;
            width: 260px;
            margin: 12px auto;
            padding: 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            font-size: 17px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .logout {
            background-color: #dc3545;
        }

        .logout:hover {
            background-color: #b52532;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Bem-vindo, Professor <?php echo $username; ?> 👨‍🏫</h2>

    <a href="postar_atividade.php" class="btn">➕ Postar Atividade</a>

    <a href="postar_redacoes.php" class="btn">📝 Postar Redação</a>

    <a href="ver_atividade.php" class="btn">📘 Ver Atividades</a>

    <a href="ver_redacoes.php" class="btn">📕 Ver Redações</a>

    <a href="logout.php" class="btn logout">Sair</a>

</div>

</body>
</html>
