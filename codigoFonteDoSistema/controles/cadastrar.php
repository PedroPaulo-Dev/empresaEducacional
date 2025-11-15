<?php
// ===============================
// 📘 ESTUDAENEM - CADASTRO DE USUÁRIOS (Versão Corrigida)
// ===============================
// ✅ Corrigido para garantir que o campo "role" seja sempre salvo corretamente.
// ✅ Mantém verificação de e-mail duplicado e senha criptografada.
// ✅ Redireciona conforme o tipo de usuário.
// ===============================

include("../controles/conexao.php"); // conexão com banco

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // --- 1️⃣ Recebe e valida os dados do formulário ---
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $role     = isset($_POST['role']) ? trim($_POST['role']) : ''; // garante que exista

    // Verifica campos obrigatórios
    if (empty($username) || empty($email) || empty($password_raw)) {
        mostrarMensagem("❌ Por favor, preencha todos os campos obrigatórios.");
        exit;
    }

    // Se o campo role não for informado, define padrão "aluno"
    if (empty($role)) {
        $role = 'aluno';
    }

    // --- 2️⃣ Verifica se o e-mail já está cadastrado ---
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $check->close();
        mostrarMensagem("⚠️ Este e-mail já está cadastrado. <a href='../visual/login.html'>Fazer login</a>");
        exit;
    }
    $check->close();

    // --- 3️⃣ Criptografa a senha e insere o novo usuário ---
    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);

    if ($stmt->execute()) {
        // --- 4️⃣ Redireciona conforme o tipo de usuário ---
        if ($role === 'aluno') {
            header("Location: ../visual/aluno_dashboard.php");
            exit;
        } elseif ($role === 'professor') {
            header("Location: ../controles/professor_dashboard.php");
            exit;
        } elseif ($role === 'admin') {
            header("Location: ../visual/welcome.php");
            exit;
        } else {
            mostrarMensagem("✅ Cadastro realizado com sucesso! <a href='../visual/login.html'>Fazer login</a>");
        }
    } else {
        mostrarMensagem("❌ Erro ao cadastrar: " . $stmt->error);
    }

    $stmt->close();
}
$conn->close();


// ===============================
// 💬 Função para exibir mensagens estilizadas
// ===============================
function mostrarMensagem($mensagem) {
    echo "
    <!DOCTYPE html>
    <html lang='pt-BR'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Cadastro - EstudaEnem</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f1f1f1;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
            }
            .mensagem-box {
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                padding: 40px;
                text-align: center;
                width: 350px;
            }
            .mensagem-box h3, .mensagem-box p {
                color: #1a73e8;
                margin: 0 0 10px 0;
            }
            .mensagem-box a {
                color: #1a73e8;
                text-decoration: none;
                font-weight: bold;
            }
            .mensagem-box a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class='mensagem-box'>
            <h3>$mensagem</h3>
        </div>
    </body>
    </html>";
}
?>
