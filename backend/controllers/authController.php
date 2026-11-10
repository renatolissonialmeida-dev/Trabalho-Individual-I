<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/logger.php'; // <--- LOGGER IMPORTADO

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método de requisição inválido.']);
    exit;
}

$tokenRecebido = $_POST['csrf_token'] ?? '';
if (empty($tokenRecebido) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $tokenRecebido)) {
    http_response_code(401);
    registrarLog("Falha de segurança: Token CSRF inválido no Auth.");
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de validação CSRF.']);
    exit;
}

$acao = $_POST['acao'] ?? '';
if (empty($acao)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Ação não informada.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    if ($acao === 'cadastrar') {
        $nome  = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($nome) || empty($email) || empty($senha)) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Este e-mail já está cadastrado.']);
            exit;
        }

        $emailLower = strtolower($email);
        if (strpos($emailLower, '@admin.com') !== false) { $perfil = 'admin'; } 
        elseif (strpos($emailLower, '@fazbear.com') !== false) { $perfil = 'funcionario'; } 
        else { $perfil = 'cliente'; }

        $senhaHash = password_hash($senha, PASSWORD_BCRYPT);
        $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
        
        if ($stmtInsert->execute([$nome, $email, $senhaHash, $perfil])) {
            session_regenerate_id(true);
            $novoId = $pdo->lastInsertId();
            $_SESSION['usuario_id']   = $novoId;
            $_SESSION['usuario_nome'] = $nome;
            $_SESSION['perfil']       = $perfil;

            registrarLog("Nova entidade: Usuario ID $novoId criado ($perfil).");
            http_response_code(201);
            echo json_encode(['sucesso' => true, 'mensagem' => "Conta criada com sucesso!"]);
        } else {
            http_response_code(500);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar no banco.']);
        }
        exit;
    }

    if ($acao === 'login') {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if (empty($email) || empty($senha)) {
            http_response_code(400);
            echo json_encode(['sucesso' => false, 'mensagem' => 'Informe e-mail e senha.']);
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            session_regenerate_id(true);
            $_SESSION['usuario_id']   = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['perfil']       = $usuario['perfil'];

            registrarLog("Login bem-sucedido: Usuario ID {$usuario['id']} logou.");
            http_response_code(200);
            echo json_encode(['sucesso' => true, 'mensagem' => "Bem-vindo!", 'perfil' => $usuario['perfil']]);
        } else {
            registrarLog("Falha de autenticação: Tentativa falha para o email $email.");
            http_response_code(401);
            echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail ou senha incorretos.']);
        }
        exit;
    }
} catch (PDOException $e) {
    registrarLog("Erro interno (Auth): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno do servidor.']);
}