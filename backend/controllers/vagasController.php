<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/logger.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método inválido.']);
    exit;
}

$tokenRecebido = $_POST['csrf_token'] ?? '';
if (empty($tokenRecebido) || !hash_equals($_SESSION['csrf_token'] ?? '', $tokenRecebido)) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha CSRF.']);
    exit;
}

$usuarioId   = $_SESSION['usuario_id'];
$nome        = trim($_POST['nome'] ?? '');
$email       = trim($_POST['email'] ?? '');
$telefone    = trim($_POST['telefone'] ?? '');
$cargo       = trim($_POST['cargo'] ?? '');
$turno       = trim($_POST['turno'] ?? '');
$experiencia = trim($_POST['experiencia'] ?? 'Sem experiência.');

if (empty($nome) || empty($email) || empty($telefone) || empty($cargo) || empty($turno)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Campos obrigatórios.']);
    exit;
}

$matricula = 'FZ-EMP-' . rand(10000, 99999);

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("INSERT INTO candidaturas (usuario_id, matricula, nome, email, telefone, cargo, turno, experiencia) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$usuarioId, $matricula, $nome, $email, $telefone, $cargo, $turno, $experiencia])) {
        
        registrarLog("Nova entidade: Candidatura $matricula registrada pelo Usuario ID $usuarioId.");
        http_response_code(201);
        echo json_encode(['sucesso' => true, 'matricula' => $matricula, 'mensagem' => 'Registrada com sucesso!']);
    } else {
        http_response_code(500);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar.']);
    }
} catch (PDOException $e) {
    registrarLog("Erro interno (Candidatura): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor.']);
}