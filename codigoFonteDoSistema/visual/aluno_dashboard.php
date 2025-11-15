<?php
session_start();
include(__DIR__ . "/../controles/conexao.php");

// Verifica login
if (!isset($_SESSION['usuario']) || $_SESSION['role'] !== 'aluno') {
    header("Location: login.html");
    exit();
}

$email = $_SESSION['usuario'];

// Buscar informações do aluno
$stmt = $conn->prepare("SELECT id, username FROM users WHERE email=? AND role='aluno'");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows != 1) die("Acesso negado!");
$aluno = $res->fetch_assoc();
$aluno_id = $aluno['id'];

// --- Pegar atividades (não incluir redações) ---
$atividades_tab = $conn->query("SELECT * FROM atividades WHERE titulo NOT LIKE '%Redação%' ORDER BY data_postagem DESC");

// --- Pegar redações enviadas pelo aluno ---
$redacoes_tab = $conn->query("
    SELECT r.*, a.titulo AS atividade_titulo, a.disciplina 
    FROM redacoes r
    JOIN atividades a ON r.atividade_id = a.id
    WHERE r.aluno_id = $aluno_id
    ORDER BY r.data_envio DESC
");

// --- Pegar todas atividades de redação disponíveis ---
$redacoes_disponiveis = $conn->query("
    SELECT * FROM atividades 
    WHERE titulo LIKE '%Redação%' 
    ORDER BY data_postagem DESC
");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Painel do Aluno</title>
<style>
:root {
    --azul: #1a73e8;
    --azul-escuro: #0d47a1;
    --cinza-claro: #f4f6fb;
    --verde: #2ecc71;
    --amarelo: #f1c40f;
    --vermelho: #e74c3c;
}
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #d0e6ff, #f4f6fb);
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 30px;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    position: relative;
}
.logout-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    background: var(--vermelho);
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}
.logout-btn:hover { background: #c0392b; }
h1 {
    text-align: center;
    color: var(--azul);
    margin-bottom: 25px;
}
.tab-btns {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
}
.tab-btns button {
    background-color: var(--azul);
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: bold;
    font-size: 15px;
    transition: 0.3s;
}
.tab-btns button:hover { background-color: var(--azul-escuro); }
.tab {
    display: none;
}
.tab.active { display: block; }
/* Cards de Atividades e Redações */
.activity, .redacao, .redacao-form {
    background-color: #f9fbff;
    border-left: 6px solid var(--azul);
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}
.redacao, .redacao-form { border-left-color: var(--verde); }
/* Status */
.status {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 8px;
    font-weight: bold;
    color: white;
}
.status.pendente { background: var(--amarelo); }
.status.corrigida { background: var(--verde); }
/* Formulario de Redação */
textarea, select, input[type=number] {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    margin-top: 8px;
}
button.submit-redacao {
    margin-top: 12px;
    padding: 12px 18px;
    border: none;
    border-radius: 8px;
    background-color: var(--azul);
    color: white;
    cursor: pointer;
    font-weight: bold;
    transition: 0.2s;
}
button.submit-redacao:hover { background-color: var(--azul-escuro); }
</style>
<script>
function showTab(tabName) {
    document.querySelectorAll(".tab").forEach(tab => tab.classList.remove("active"));
    document.getElementById(tabName).classList.add("active");
}
</script>
</head>
<body>
<div class="container">
    <form action="../controles/logout.php" method="post">
        <button class="logout-btn" type="submit">Sair</button>
    </form>

    <h1>Bem-vindo, <?php echo htmlspecialchars($aluno['username']); ?> 👋</h1>

    <div class="tab-btns">
        <button onclick="showTab('atividades')">📚 Atividades</button>
        <button onclick="showTab('redacoes')">📝 Redações</button>
    </div>

    <!-- ABA ATIVIDADES -->
    <div id="atividades" class="tab active">
        <h2>Atividades Disponíveis</h2>
        <?php if ($atividades_tab->num_rows > 0): ?>
            <?php while($a = $atividades_tab->fetch_assoc()): ?>
                <div class="activity">
                    <h3><?php echo htmlspecialchars($a['titulo']); ?> (<?php echo htmlspecialchars($a['disciplina']); ?>)</h3>
                    <p><?php echo nl2br(htmlspecialchars($a['descricao'])); ?></p>
                    <p><em>Data de postagem: <?php echo $a['data_postagem']; ?></em></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma atividade disponível.</p>
        <?php endif; ?>
    </div>

    <!-- ABA REDAÇÕES -->
    <div id="redacoes" class="tab">
        <h2>Minhas Redações</h2>

        <!-- Redações enviadas -->
        <?php if ($redacoes_tab->num_rows > 0): ?>
            <?php while($r = $redacoes_tab->fetch_assoc()): 
                $statusClass = strtolower($r['status'] ?? 'pendente');
            ?>
                <div class="redacao">
                    <h3><?php echo htmlspecialchars($r['atividade_titulo']); ?> (<?php echo htmlspecialchars($r['disciplina']); ?>)</h3>
                    <p><strong>Status:</strong> <span class="status <?php echo $statusClass; ?>"><?php echo ucfirst($r['status'] ?? 'Pendente'); ?></span></p>
                    <p><strong>Nota:</strong> <?php echo $r['nota'] ?? '—'; ?></p>
                    <p><em>Enviada em <?php echo $r['data_envio']; ?></em></p>
                    <p><strong>Comentário do professor:</strong> <?php echo htmlspecialchars($r['comentario'] ?? '—'); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Nenhuma redação enviada ainda.</p>
        <?php endif; ?>

        <!-- Formulário para enviar novas redações -->
        <h2>Enviar Nova Redação</h2>
        <?php if ($redacoes_disponiveis->num_rows > 0): ?>
            <?php while($ra = $redacoes_disponiveis->fetch_assoc()): ?>
                <div class="redacao-form">
                    <h3><?php echo htmlspecialchars($ra['titulo']); ?> (<?php echo htmlspecialchars($ra['disciplina']); ?>)</h3>
                    <p><?php echo nl2br(htmlspecialchars($ra['descricao'])); ?></p>
                    <form method="post" action="../controles/enviar_redacao.php">
                        <input type="hidden" name="atividade_id" value="<?php echo $ra['id']; ?>">
                        <textarea name="conteudo" placeholder="Escreva sua redação aqui" required></textarea>
                        <button type="submit" class="submit-redacao">Enviar Redação</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Não há redações disponíveis para envio.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>
