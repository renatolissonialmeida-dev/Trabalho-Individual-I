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

// 1. Validação de autenticação
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Método de requisição inválido.']);
    exit;
}

// 2. Proteção CSRF
$tokenRecebido = $_POST['csrf_token'] ?? '';
$tokenSessao   = $_SESSION['csrf_token'] ?? '';

if (empty($tokenRecebido) || empty($tokenSessao) || !hash_equals($tokenSessao, $tokenRecebido)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de segurança CSRF.']);
    exit;
}

$codigoReserva = trim($_POST['codigo_reserva'] ?? '');
$usuarioId     = $_SESSION['usuario_id'];

if (empty($codigoReserva)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Código da reserva não informado.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    // Garante que o usuário só pode deletar as suas PRÓPRIAS reservas
    $stmt = $pdo->prepare("DELETE FROM reservas WHERE codigo_reserva = ? AND usuario_id = ?");
    $stmt->execute([$codigoReserva, $usuarioId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Reserva cancelada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Reserva não encontrada ou você não possui permissão.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;