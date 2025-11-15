<?php
session_start();
include("conexao.php");

// Verifica se o professor está logado
if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'professor') {
    header("Location: ../visual/login.html");
    exit;
}

$email = $_SESSION['usuario'];

// Buscar ID do professor
$stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$professor = $res->fetch_assoc();
$id_professor = $professor['id'];

// Se o professor enviou o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $tema = trim($_POST['tema']);

    if (!empty($titulo) && !empty($tema)) {

        // Inserção CORRIGIDA com data_postagem
        $stmt2 = $conn->prepare("
            INSERT INTO atividades (titulo, descricao, disciplina, data_postagem, id_professor) 
            VALUES (?, ?, 'Redação', NOW(), ?)
        ");
        $stmt2->bind_param("ssi", $titulo, $tema, $id_professor);

        if ($stmt2->execute()) {
            header("Location: professor_dashboard.php?msg=tema_redacao_postado");
            exit;
        } else {
            $erro = "Erro ao postar tema: " . $conn->error;
        }
    } else {
        $erro = "Preencha todos os campos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Postar Tema de Redação</title>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(to bottom, #1e88e5, #bbdefb);
        margin: 0;
        padding: 0;
    }
    .container {
        width: 500px;
        margin: 60px auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
    h2 {
        text-align: center;
        color: #0d47a1;
        margin-bottom: 20px;
    }
    label {
        font-weight: 600;
        margin-top: 10px;
        color: #333;
    }
    input, textarea {
        width: 100%;
        padding: 10px;
        border-radius: 8px;
        border: 1px solid #ccc;
        margin-top: 5px;
    }
    textarea {
        resize: vertical;
    }
    button {
        background-color: #0d47a1;
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 8px;
        font-size: 16px;
        margin-top: 20px;
        cursor: pointer;
    }
    button:hover {
        background-color: #08306b;
    }
    .erro {
        color: red;
        text-align: center;
        margin-top: 10px;
    }
    .voltar {
        text-decoration: none;
        display: block;
        margin-top: 15px;
        text-align: center;
        background: #777;
        padding: 10px;
        border-radius: 8px;
        color: white;
    }
</style>

</head>
<body>

<div class="container">
    <h2>Postar Tema de Redação</h2>

    <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>

    <form method="POST">
        <label for="titulo">Título do Tema:</label>
        <input type="text" id="titulo" name="titulo" placeholder="Ex.: Redação sobre Meio Ambiente" required>

        <label for="tema">Tema / Enunciado:</label>
        <textarea id="tema" name="tema" rows="6" placeholder="Digite o tema completo da redação..." required></textarea>

        <button type="submit">Publicar Tema</button>
    </form>

    <a class="voltar" href="professor_dashboard.php">← Voltar</a>
</div>

</body>
</html>
