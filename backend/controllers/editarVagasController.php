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

$matricula   = trim($_POST['matricula'] ?? '');
$usuarioId   = $_SESSION['usuario_id'];
$nome        = trim($_POST['nome'] ?? '');
$email       = trim($_POST['email'] ?? '');
$telefone    = trim($_POST['telefone'] ?? '');
$cargo       = trim($_POST['cargo'] ?? '');
$turno       = trim($_POST['turno'] ?? '');
$experiencia = trim($_POST['experiencia'] ?? '');

if (empty($matricula) || empty($nome) || empty($email) || empty($telefone) || empty($cargo) || empty($turno)) {
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
        $nome,
        $email,
        $telefone,
        $cargo,
        $turno,
        $experiencia,
        $matricula,
        $usuarioId
    ]);

    if ($executou) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Ficha de funcionário atualizada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhuma alteração foi realizada.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;