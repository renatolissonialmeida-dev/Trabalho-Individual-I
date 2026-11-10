<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/logger.php'; // <--- LOGGER IMPORTADO

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

// Proteção CSRF
$tokenRecebido = $_POST['csrf_token'] ?? '';
$tokenSessao   = $_SESSION['csrf_token'] ?? '';

if (empty($tokenRecebido) || empty($tokenSessao) || !hash_equals($tokenSessao, $tokenRecebido)) {
    registrarLog("Falha de segurança: Token CSRF inválido ao tentar deletar vaga (Usuário ID: {$_SESSION['usuario_id']}).");
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de validação CSRF.']);
    exit;
}

$matricula = trim($_POST['matricula'] ?? '');
$usuarioId = $_SESSION['usuario_id'];

if (empty($matricula)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Matrícula não informada.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("DELETE FROM candidaturas WHERE matricula = ? AND usuario_id = ?");
    $stmt->execute([$matricula, $usuarioId]);

    if ($stmt->rowCount() > 0) {
        registrarLog("Exclusão: Candidatura $matricula removida pelo Usuário ID $usuarioId.");
        http_response_code(200);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Ficha removida do Banco de Horas com sucesso!']);
    } else {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Ficha não encontrada ou permissão negada.']);
    }

} catch (PDOException $e) {
    registrarLog("Erro interno (Deletar Vaga): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;