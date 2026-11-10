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
    registrarLog("Falha de segurança: Token CSRF inválido na edição de vaga (Usuário ID: {$_SESSION['usuario_id']}).");
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de validação CSRF.']);
    exit;
}

$matricula   = trim($_POST['matricula'] ?? '');
$usuarioId   = $_SESSION['usuario_id'];
$nome        = trim($_POST['nome'] ?? '');
$email       = trim($_POST['email'] ?? '');
$telefone    = trim($_POST['telefone'] ?? '');
$cargo       = trim($_POST['cargo'] ?? '');
$turno       = trim($_POST['turno'] ?? '');
$experiencia = trim($_POST['experiencia'] ?? '');

if (empty($matricula) || empty($nome) || empty($email) || empty($telefone) || empty($cargo) || empty($turno)) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        UPDATE candidaturas 
        SET nome = ?, email = ?, telefone = ?, cargo = ?, turno = ?, experiencia = ?
        WHERE matricula = ? AND usuario_id = ?
    ");

    $executou = $stmt->execute([
        $nome, $email, $telefone, $cargo, $turno, $experiencia, $matricula, $usuarioId
    ]);

    if ($executou && $stmt->rowCount() > 0) {
        registrarLog("Atualização: Candidatura $matricula atualizada pelo Usuário ID $usuarioId.");
        http_response_code(200);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Ficha de funcionário atualizada com sucesso!']);
    } else {
        http_response_code(200);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhuma alteração foi realizada.']);
    }

} catch (PDOException $e) {
    registrarLog("Erro interno (Editar Vaga): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;