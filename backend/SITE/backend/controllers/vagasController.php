<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

header('Content-Type: application/json');
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
    exit;
}

// Validação CSRF
$tokenRecebido = $_POST['csrf_token'] ?? '';
$tokenSessao   = $_SESSION['csrf_token'] ?? '';

if (empty($tokenRecebido) || empty($tokenSessao) || !hash_equals($tokenSessao, $tokenRecebido)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de validação de segurança (CSRF).']);
    exit;
}

$usuarioId   = $_SESSION['usuario_id'];
$nome        = trim($_POST['nome'] ?? '');
$email       = trim($_POST['email'] ?? '');
$telefone    = trim($_POST['telefone'] ?? '');
$cargo       = trim($_POST['cargo'] ?? '');
$turno       = trim($_POST['turno'] ?? '');
$experiencia = trim($_POST['experiencia'] ?? 'Sem experiência prévia informada.');

if (empty($nome) || empty($email) || empty($telefone) || empty($cargo) || empty($turno)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

// Gera a matrícula no formato corporativo FZ-EMP-XXXXX
$matricula = 'FZ-EMP-' . rand(10000, 99999);

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        INSERT INTO candidaturas 
        (usuario_id, matricula, nome, email, telefone, cargo, turno, experiencia) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $executou = $stmt->execute([
        $usuarioId,
        $matricula,
        $nome,
        $email,
        $telefone,
        $cargo,
        $turno,
        $experiencia
    ]);

    if ($executou) {
        echo json_encode([
            'sucesso'   => true,
            'matricula' => $matricula,
            'mensagem'  => 'Candidatura registrada com sucesso!'
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar candidatura no banco de dados.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;