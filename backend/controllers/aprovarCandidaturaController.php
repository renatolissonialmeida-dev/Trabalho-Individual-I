<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/logger.php'; // <--- LOGGER IMPORTADO

if (!isset($_SESSION['usuario_id']) || ($_SESSION['perfil'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado.']);
    exit;
}

$tokenRecebido = $_POST['csrf_token'] ?? '';
if (empty($tokenRecebido) || !hash_equals($_SESSION['csrf_token'] ?? '', $tokenRecebido)) {
    http_response_code(401);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha CSRF.']);
    exit;
}

$candidaturaId = intval($_POST['candidatura_id'] ?? 0);
$acao          = $_POST['acao'] ?? '';

if ($candidaturaId <= 0 || !in_array($acao, ['aprovar', 'recusar'])) {
    http_response_code(400);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Parâmetros inválidos.']);
    exit;
}

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT * FROM candidaturas WHERE id = ?");
    $stmt->execute([$candidaturaId]);
    $candidato = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$candidato) {
        http_response_code(404);
        echo json_encode(['sucesso' => false, 'mensagem' => 'Candidato não encontrado.']);
        exit;
    }

    if ($acao === 'recusar') {
        $stmtRecusa = $pdo->prepare("UPDATE candidaturas SET status = 'recusado' WHERE id = ?");
        $stmtRecusa->execute([$candidaturaId]);
        registrarLog("RH: Candidatura ID $candidaturaId recusada pelo Admin {$_SESSION['usuario_id']}.");
        http_response_code(200);
        echo json_encode(['sucesso' => true, 'acao' => 'recusar', 'mensagem' => 'Recusada com sucesso.']);
        exit;
    }

    $usuarioEmailPessoal = explode('@', strtolower(trim($candidato['email'])))[0];
    $usuarioEmailPessoal = preg_replace('/[^a-z0-9._-]/', '', $usuarioEmailPessoal);
    $emailCorporativo = $usuarioEmailPessoal . '@fazbear.com';

    $senhaTemporaria  = 'Fazbear123!';
    $senhaHash        = password_hash($senhaTemporaria, PASSWORD_DEFAULT);

    $stmtUser = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, 'funcionario')");
    $stmtUser->execute([$candidato['nome'], $emailCorporativo, $senhaHash]);
    $novoUsuarioId = $pdo->lastInsertId();

    $stmtUpdate = $pdo->prepare("UPDATE candidaturas SET usuario_id = ?, status = 'aprovado' WHERE id = ?");
    $stmtUpdate->execute([$novoUsuarioId, $candidaturaId]);

    registrarLog("Nova entidade: Candidatura ID $candidaturaId aprovada. Conta gerada para $emailCorporativo.");
    
    http_response_code(201);
    echo json_encode([
        'sucesso' => true, 'acao' => 'aprovar', 
        'email_corporativo' => $emailCorporativo, 'senha_temp' => $senhaTemporaria, 
        'mensagem' => 'Contratação efetuada com sucesso!'
    ]);

} catch (PDOException $e) {
    registrarLog("Erro interno (RH): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor.']);
}
exit;