<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/logger.php'; // <--- LOGGER IMPORTADO

// 1. Validação de autenticação
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método de requisição inválido.']);
    exit;
}

// 2. Proteção CSRF
$tokenRecebido = $_POST['csrf_token'] ?? '';
$tokenSessao   = $_SESSION['csrf_token'] ?? '';

if (empty($tokenRecebido) || empty($tokenSessao) || !hash_equals($tokenSessao, $tokenRecebido)) {
    registrarLog("Falha de segurança: Token CSRF inválido ao tentar deletar reserva (Usuário ID: {$_SESSION['usuario_id']}).");
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de segurança CSRF.']);
    exit;
}

$codigoReserva = trim($_POST['codigo_reserva'] ?? '');
$usuarioId     = $_SESSION['usuario_id'];

if (empty($codigoReserva)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Código da reserva não informado.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    // Garante que o usuário só pode deletar as suas PRÓPRIAS reservas
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE codigo_reserva = ? AND usuario_id = ?");
    $stmt->execute([$codigoReserva, $usuarioId]);

    if ($stmt->rowCount() > 0) {
        registrarLog("Exclusão: Reserva $codigoReserva cancelada pelo Usuário ID $usuarioId.");
        http_response_code(200);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Reserva cancelada com sucesso!']);
    } else {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Reserva não encontrada ou você não possui permissão.']);
    }

} catch (PDOException $e) {
    registrarLog("Erro interno (Deletar Reserva): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;