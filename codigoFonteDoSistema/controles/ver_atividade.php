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

// Busca apenas atividades normais (não Redação)
$sql = "SELECT * FROM atividades WHERE disciplina != 'Redação' AND id_professor = ? ORDER BY data_postagem DESC";
$stmt2 = $conn->prepare($sql);
$stmt2->bind_param("i", $id_professor);
$stmt2->execute();
$result = $stmt2->get_result();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Ver Atividades</title>
<style>
body { font-family: Arial, sans-serif; background: #e3f2fd; margin: 0; padding: 0; }
.container { width: 90%; margin: 30px auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,0.2); }
h2 { color: #0056b3; text-align: center; }
.atividade { border: 1px solid #ddd; padding: 15px; border-radius: 10px; margin-bottom: 15px; background: #fafafa; }
label { font-weight: bold; }
input[type="number"] { width: 80px; padding: 5px; margin-right: 10px; }
button { padding: 5px 10px; background: #0056b3; color: white; border: none; border-radius: 6px; cursor: pointer; }
button:hover { background: #003f8a; }
.voltar { display: inline-block; margin-top: 20px; background: #777; color: white; padding: 10px; border-radius: 8px; text-decoration: none; }
.voltar:hover { background: #555; }
</style>
</head>
<body>
<div class="container">
<h2>📘 Atividades Postadas</h2>

<?php if ($result->num_rows === 0): ?>
    <p>Nenhuma atividade encontrada.</p>
<?php endif; ?>

<?php while ($a = $result->fetch_assoc()): ?>
    <div class="atividade">
        <h3><?= htmlspecialchars($a['titulo']) ?></h3>
        <p><strong>Disciplina:</strong> <?= htmlspecialchars($a['disciplina']) ?></p>
        <p><strong>Descrição:</strong> <?= nl2br(htmlspecialchars($a['descricao'])) ?></p>
        <p><strong>Data:</strong> <?= $a['data_postagem'] ?></p>

        <!-- Formulário para dar nota -->
        <form method="POST" action="salvar_nota.php">
            <input type="hidden" name="atividade_id" value="<?= $a['id'] ?>">
            <label>Nota:</label>
            <input type="number" name="nota" step="0.01" min="0" max="100" required>
            <button type="submit">Salvar Nota</button>
        </form>
    </div>
<?php endwhile; ?>

<a href="professor_dashboard.php" class="voltar">← Voltar ao Painel</a>
</div>
</body>
</html>
