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

// 1. Valida se o usuário está logado
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
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de validação de segurança (Token CSRF inválido).']);
    exit;
}

// 3. Captura dos dados do formulário
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
    echo json_encode(['sucesso' => false, 'mensagem' => 'Preencha todos os campos obrigatórios da reserva.']);
    exit;
}

// Genera um código de reserva exclusivo
$codigoReserva = 'FZB-' . rand(10000, 99999);

try {
    $pdo = Database::getConnection();

    // Mapeamento idêntico às colunas da imagem do phpMyAdmin
    $stmt = $pdo->prepare("
        INSERT INTO reservas 
        (usuario_id, codigo_reserva, nome_responsavel, email, telefone, pacote, data_reserva, horario, convidados, aniversariante, observacoes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $executou = $stmt->execute([
        $usuarioId,
        $codigoReserva,
        $responsavel,
        $email,
        $telefone,
        $pacote,
        $data,
        $horario,
        $convidados,
        $aniversariante,
        $observacoes
    ]);

    if ($executou) {
        echo json_encode([
            'sucesso'       => true, 
            'codigoReserva' => $codigoReserva,
            'mensagem'      => 'Reserva registrada com sucesso!'
        ]);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao salvar reserva no banco de dados.']);
    }

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;