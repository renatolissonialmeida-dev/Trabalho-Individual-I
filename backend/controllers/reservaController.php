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

$usuarioId      = $_SESSION['usuario_id'];
$responsavel    = trim($_POST['responsavel'] ?? '');
$email          = trim($_POST['email'] ?? '');
$telefone       = trim($_POST['telefone'] ?? '');
$pacote         = trim($_POST['pacote'] ?? '');
$data           = trim($_POST['data'] ?? '');
$horario        = trim($_POST['horario'] ?? '');
$convidados     = filter_var($_POST['convidados'] ?? 0, FILTER_VALIDATE_INT);
$aniversariante = trim($_POST['aniversariante'] ?? '');
$observacoes    = trim($_POST['observacoes'] ?? '');

if (empty($responsavel) || empty($email) || empty($telefone) || empty($pacote) || empty($data) || empty($horario) || empty($aniversariante) || !$convidados) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha os campos.']);
    exit;
}

$codigoReserva = 'FZB-' . rand(10000, 99999);

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("INSERT INTO reservas (usuario_id, codigo_reserva, nome_responsavel, email, telefone, pacote, data_reserva, horario, convidados, aniversariante, observacoes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$usuarioId, $codigoReserva, $responsavel, $email, $telefone, $pacote, $data, $horario, $convidados, $aniversariante, $observacoes])) {
        
        registrarLog("Nova entidade: Reserva $codigoReserva criada pelo Usuario ID $usuarioId.");
        http_response_code(201);
        echo json_encode(['sucesso' => true, 'codigoReserva' => $codigoReserva, 'mensagem' => 'Registrada com sucesso!']);
    } else {
        http_response_code(500);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar.']);
    }
} catch (PDOException $e) {
    registrarLog("Erro interno (Reserva): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor.']);
}