<?php
session_start();
include("conexao.php");

// Verifica se o professor está logado
if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'professor') {
    header("Location: ../visual/login.html");
    exit;
}

$email = $_SESSION['usuario'];

// Busca o ID do professor no banco
$stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$professor = $res->fetch_assoc();
$id_professor = $professor['id'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $disciplina = trim($_POST['disciplina']);

    if (!empty($titulo) && !empty($descricao) && !empty($disciplina)) {
        $stmt2 = $conn->prepare("INSERT INTO atividades (titulo, descricao, disciplina, data_postagem, id_professor)
                                 VALUES (?, ?, ?, NOW(), ?)");
        $stmt2->bind_param("sssi", $titulo, $descricao, $disciplina, $id_professor);

        if ($stmt2->execute()) {
            header("Location: professor_dashboard.php?msg=atividade_criada");
            exit;
        } else {
            $erro = "Erro ao salvar atividade: " . $conn->error;
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
    <title>Postar Nova Atividade</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #007bff, #e3f2fd);
            margin: 0;
            padding: 0;
        }
        .container {
            width: 500px;
            margin: 60px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        h2 {
            text-align: center;
            color: #0056b3;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            margin-top: 10px;
            color: #333;
        }
        input[type="text"], textarea, select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
            font-size: 14px;
        }
        textarea {
            resize: vertical;
        }
        button {
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background-color: #003f8a;
        }
        .erro {
            color: red;
            text-align: center;
            margin-top: 10px;
        }
        .voltar {
            display: inline-block;
            text-align: center;
            margin-top: 15px;
            background-color: #6c757d;
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
        }
        .voltar:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📘 Postar Nova Atividade</h2>

        <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>

        <form method="POST">
            <label for="titulo">Título:</label>
            <input type="text" name="titulo" id="titulo" placeholder="Digite o título da atividade" required>

            <label for="disciplina">Disciplina:</label>
            <select name="disciplina" id="disciplina" required>
                <option value="">Selecione...</option>
                <option value="Matemática">Matemática</option>
                <option value="Português">Português</option>
                <option value="Ciências">Ciências</option>
                <option value="História">História</option>
                <option value="Geografia">Geografia</option>
                <option value="Inglês">Inglês</option>
                <option value="Arte">Arte</option>
            </select>

            <label for="descricao">Descrição:</label>
            <textarea name="descricao" id="descricao" rows="5" placeholder="Explique o que os alunos devem fazer..." required></textarea>

            <button type="submit">Publicar Atividade</button>
        </form>

        <a href="professor_dashboard.php" class="voltar">← Voltar ao Painel</a>
    </div>
</body>
</html>
