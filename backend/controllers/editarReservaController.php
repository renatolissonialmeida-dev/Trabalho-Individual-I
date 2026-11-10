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
    registrarLog("Falha de segurança: Token CSRF inválido na edição de reserva (Usuário ID: {$_SESSION['usuario_id']}).");
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de segurança CSRF.']);
    exit;
}

// 3. Captura dos dados
$codigoReserva  = trim($_POST['codigo_reserva'] ?? '');
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

if (empty($codigoReserva) || empty($responsavel) || empty($email) || empty($telefone) || empty($pacote) || empty($data) || empty($horario) || empty($aniversariante) || !$convidados) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        UPDATE reservas 
        SET nome_responsavel = ?, email = ?, telefone = ?, pacote = ?, data_reserva = ?, horario = ?, convidados = ?, aniversariante = ?, observacoes = ?
        WHERE codigo_reserva = ? AND usuario_id = ?
    ");

    $executou = $stmt->execute([
        $responsavel, $email, $telefone, $pacote, $data, $horario, $convidados, $aniversariante, $observacoes, $codigoReserva, $usuarioId
    ]);

    if ($executou && $stmt->rowCount() > 0) {
        registrarLog("Atualização: Reserva $codigoReserva editada pelo Usuário ID $usuarioId.");
        http_response_code(200);
        echo json_encode(['sucesso' => true, 'mensagem' => 'Reserva atualizada com sucesso!']);
    } else {
        http_response_code(200); // 200 porque deu certo, apenas não mudou nada visualmente
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhuma alteração foi realizada.']);
    }

} catch (PDOException $e) {
    registrarLog("Erro interno (Editar Reserva): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;