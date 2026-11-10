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
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    // UPDATE no MySQL garantindo a posse do usuario_id
    $stmt = $pdo->prepare("
        UPDATE reservas 
        SET nome_responsavel = ?, email = ?, telefone = ?, pacote = ?, data_reserva = ?, horario = ?, convidados = ?, aniversariante = ?, observacoes = ?
        WHERE codigo_reserva = ? AND usuario_id = ?
    ");

    $executou = $stmt->execute([
        $responsavel,
        $email,
        $telefone,
        $pacote,
        $data,
        $horario,
        $convidados,
        $aniversariante,
        $observacoes,
        $codigoReserva,
        $usuarioId
    ]);

    if ($executou) {
        echo json_encode(['sucesso' => true, 'mensagem' => 'Reserva atualizada com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Nenhuma alteração foi realizada.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;