<?php
session_start();
include("conexao.php");

// Verifica se é professor
if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'professor') {
    header("Location: ../visual/login.html");
    exit;
}

$email = $_SESSION['usuario'];

// Buscar dados do professor
$stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
$professor = $res->fetch_assoc();
$id_professor = $professor['id'];

// Buscar apenas redações enviadas para atividades de Redação do professor
$sql = "
    SELECT r.*, a.titulo AS atividade_titulo, u.username AS aluno_nome
    FROM redacoes r
    JOIN atividades a ON r.atividade_id = a.id
    JOIN users u ON r.aluno_id = u.id
    WHERE a.id_professor = ? AND a.disciplina = 'Redação'
    ORDER BY r.data_envio DESC
";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $id_professor);
$stmt2->execute();
$redacoes = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Redações Enviadas</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to bottom right, #007bff, #e3f2fd);
    margin: 0;
    padding: 0;
}
.container {
    width: 90%;
    margin: 30px auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
h2 {
    text-align: center;
    color: #0056b3;
    margin-bottom: 20px;
}
.card {
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 15px;
    background: #fafafa;
}
.card strong {
    display: inline-block;
    width: 120px;
}
input, textarea {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
}
textarea {
    resize: vertical;
}
button {
    margin-top: 10px;
    padding: 10px;
    background: #0056b3;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
button:hover {
    background-color: #003f8a;
}
.voltar {
    display: block;
    text-align: center;
    margin-top: 20px;
    background-color: #6c757d;
    color: white;
    padding: 10px;
    border-radius: 8px;
    text-decoration: none;
}
.voltar:hover {
    background-color: #5a6268;
}
</style>
</head>
<body>
<div class="container">
    <h2>📝 Redações Enviadas</h2>

    <?php if ($redacoes->num_rows === 0): ?>
        <p>Ainda não há redações enviadas pelos alunos.</p>
    <?php endif; ?>

    <?php while ($r = $redacoes->fetch_assoc()): ?>
        <div class="card">
            <p><strong>Aluno:</strong> <?= htmlspecialchars($r['aluno_nome'] ?? '') ?></p>
            <p><strong>Atividade:</strong> <?= htmlspecialchars($r['atividade_titulo'] ?? '') ?></p>
            <p><strong>Conteúdo:</strong><br><?= nl2br(htmlspecialchars($r['conteudo'] ?? '')) ?></p>

            <form method="POST" action="salvar_nota.php">
                <input type="hidden" name="id_redacao" value="<?= $r['id'] ?>">
                
                <label>Nota:</label>
                <input type="number" name="nota" step="0.01" min="0" max="100" value="<?= htmlspecialchars($r['nota'] ?? '') ?>">

                <label>Comentário:</label>
                <textarea name="comentario"><?= htmlspecialchars($r['comentario'] ?? '') ?></textarea>

                <button type="submit">Salvar Correção</button>
            </form>
        </div>
    <?php endwhile; ?>

    <a href="professor_dashboard.php" class="voltar">← Voltar ao Painel</a>
</div>
</body>
</html>
