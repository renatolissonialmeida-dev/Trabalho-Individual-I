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
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método de requisição inválido.']);
    exit;
}

// Proteção CSRF
$tokenRecebido = $_POST['csrf_token'] ?? '';
$tokenSessao   = $_SESSION['csrf_token'] ?? '';

if (empty($tokenRecebido) || empty($tokenSessao) || !hash_equals($tokenSessao, $tokenRecebido)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de validação CSRF.']);
    exit;
}

$matricula = trim($_POST['matricula'] ?? '');
$usuarioId = $_SESSION['usuario_id'];

if (empty($matricula)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Matrícula não informada.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("DELETE FROM candidaturas WHERE matricula = ? AND usuario_id = ?");
    $stmt->execute([$matricula, $usuarioId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Ficha removida do Banco de Horas com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ficha não encontrada ou permissão negada.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;