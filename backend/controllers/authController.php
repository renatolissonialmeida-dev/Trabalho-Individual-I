<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once '../config/database.php';

$acao = $_POST['acao'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($acao)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Requisição inválida.']);
    exit;
}

$pdo = Database::getConnection();

// ========================================================
// 1. CADASTRO (Com identificação automática de e-mail corporativo)
// ========================================================
if ($acao === 'cadastrar') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Este e-mail já está cadastrado.']);
        exit;
    }

    // REGRA DO E-MAIL: Se tiver @fazbear.com vira funcionário, senão vira cliente
    $perfil = (strpos(strtolower($email), '@fazbear.com') !== false) ? 'funcionario' : 'cliente';

    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    $stmtInsert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, ?)");
    if ($stmtInsert->execute([$nome, $email, $senhaHash, $perfil])) {
        $_SESSION['usuario_id']   = $pdo->lastInsertId();
        $_SESSION['usuario_nome'] = $nome;
        $_SESSION['perfil']       = $perfil;

        echo json_encode([
            'sucesso' => true,
            'mensagem' => "Conta criada com sucesso! Perfil atribuído: " . strtoupper($perfil)
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar no banco.']);
    }
    exit;
}

// ========================================================
// 2. LOGIN REAL
// ========================================================
if ($acao === 'login') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Informe o e-mail e a senha.']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id']   = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['perfil']       = $usuario['perfil'];

        echo json_encode([
            'sucesso'  => true,
            'mensagem' => "Autenticado com sucesso! Bem-vindo(a), {$usuario['nome']}.",
            'perfil'   => $usuario['perfil']
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'E-mail ou senha incorretos.']);
    }
    exit;
}