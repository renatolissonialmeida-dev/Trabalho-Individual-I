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

// Apenas administradores podem contratar ou recusar
if (!isset($_SESSION['usuario_id']) || ($_SESSION['perfil'] ?? '') !== 'admin') {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso negado. Apenas administradores.']);
    exit;
}

$tokenRecebido = $_POST['csrf_token'] ?? '';
$tokenSessao   = $_SESSION['csrf_token'] ?? '';

if (empty($tokenRecebido) || !hash_equals($tokenSessao, $tokenRecebido)) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Falha de validação CSRF.']);
    exit;
}

$candidaturaId = intval($_POST['candidatura_id'] ?? 0);
$acao          = $_POST['acao'] ?? ''; // 'aprovar' ou 'recusar'

if ($candidaturaId <= 0 || !in_array($acao, ['aprovar', 'recusar'])) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Parâmetros inválidos.']);
    exit;
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("SELECT * FROM candidaturas WHERE id = ?");
    $stmt->execute([$candidaturaId]);
    $candidato = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$candidato) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Candidato não encontrado.']);
        exit;
    }

    // --- AÇÃO 1: RECUSAR CANDIDATURA ---
    if ($acao === 'recusar') {
        $stmtRecusa = $pdo->prepare("UPDATE candidaturas SET status = 'recusado' WHERE id = ?");
        $stmtRecusa->execute([$candidaturaId]);

        echo json_encode([
            'sucesso'  => true,
            'acao'     => 'recusar',
            'mensagem' => 'Candidatura recusada com sucesso.'
        ]);
        exit;
    }

    // --- AÇÃO 2: APROVAR CANDIDATURA ---
    
    // 1. Pega o e-mail pessoal (ex: renatolissoni@gmail.com) e isola o nome de usuário (renatolissoni)
    $usuarioEmailPessoal = explode('@', strtolower(trim($candidato['email'])))[0];
    $usuarioEmailPessoal = preg_replace('/[^a-z0-9._-]/', '', $usuarioEmailPessoal);
    
    $emailCorporativo = $usuarioEmailPessoal . '@fazbear.com';

    // 2. Define a senha temporária para o novo funcionário
    $senhaTemporaria  = 'SenhaForte@123';
    $senhaHash        = password_hash($senhaTemporaria, PASSWORD_DEFAULT);

    // 3. Cadastra a nova conta corporativa na tabela de 'usuarios' com perfil 'funcionario'
    $stmtUser = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, perfil) VALUES (?, ?, ?, 'funcionario')");
    $stmtUser->execute([$candidato['nome'], $emailCorporativo, $senhaHash]);
    $novoUsuarioId = $pdo->lastInsertId();

    // 4. Atualiza o status da candidatura no banco para 'aprovado' e vincula o ID do novo usuário
    $stmtUpdate = $pdo->prepare("UPDATE candidaturas SET usuario_id = ?, status = 'aprovado' WHERE id = ?");
    $stmtUpdate->execute([$novoUsuarioId, $candidaturaId]);

    echo json_encode([
        'sucesso'           => true,
        'acao'              => 'aprovar',
        'email_corporativo' => $emailCorporativo,
        'senha_temp'        => $senhaTemporaria,
        'mensagem'          => 'Contratação efetuada com sucesso!'
    ]);

} catch (PDOException $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro no servidor: ' . $e->getMessage()]);
}
exit;