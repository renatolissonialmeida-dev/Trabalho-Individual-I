<?php
// Configurações Iniciais
$baseUrl = "http://localhost/SITE/backend"; 
$cookieFile = __DIR__ . '/cookie_sessao.txt';

if (file_exists($cookieFile)) {
    unlink($cookieFile);
}

// ------------------------------------------------------------------
// FUNÇÃO AUXILIAR PARA REQUISIÇÕES cURL
// ------------------------------------------------------------------
function fazerRequisicao($endpoint, $metodo = 'GET', $dados = null, $usarSessao = false, $salvarSessao = false) {
    global $baseUrl, $cookieFile;
    
    $ch = curl_init($baseUrl . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    if ($metodo === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($dados) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dados));
        }
    }

    if ($usarSessao) {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    }
    
    if ($salvarSessao) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    }

    $resposta = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return ['codigo' => $httpCode, 'corpo' => $resposta];
}

function relatarTeste($nome, $codigoEsperado, $codigoRecebido) {
    if ($codigoEsperado === $codigoRecebido) {
        echo "\033[32m[PASSOU]\033[0m {$nome} (HTTP {$codigoRecebido})\n";
    } else {
        echo "\033[31m[FALHOU]\033[0m {$nome} - Esperado: {$codigoEsperado}, Recebido: {$codigoRecebido}\n";
    }
}

echo "=== INICIANDO BATERIA DE TESTES (Fazbear Pizzaplex) ===\n\n";

// ==================================================================
// TESTE 1: Falha de Autenticação (Login Inválido)
// ==================================================================
$dadosLoginInvalido = ['acao' => 'login', 'email' => 'funcionario@fazbear.com', 'senha' => 'SenhaErrada@123'];
$resT1 = fazerRequisicao('/controllers/authController.php', 'POST', $dadosLoginInvalido);
relatarTeste("Teste 1: Falha de Autenticação", 401, $resT1['codigo']);

// ==================================================================
// TESTE 2: Sucesso de Autenticação (Login Válido)
// ==================================================================
// ATENÇÃO: Substitua pela senha real que está no seu banco de dados, se não for SenhaForte@123
$dadosLoginValido = ['acao' => 'login', 'email' => 'funcionario@fazbear.com', 'senha' => 'SenhaForte@123'];
$resT2 = fazerRequisicao('/controllers/authController.php', 'POST', $dadosLoginValido, false, true);
relatarTeste("Teste 2: Sucesso de Autenticação", 200, $resT2['codigo']);

// Capturando o token gerado no login
$csrfToken = 'token_simulado'; 
$jsonLogin = json_decode($resT2['corpo'], true);
if (isset($jsonLogin['csrf_token'])) {
    $csrfToken = $jsonLogin['csrf_token'];
}

// ==================================================================
// TESTE 3: Criação de Reserva com Sucesso
// ==================================================================
$dadosReservaCorreta = [
    'responsavel'    => 'William Afton',
    'email'          => 'william@fazbear.com',
    'telefone'       => '11999999999',
    'pacote'         => 'Mega Fazbear Party',
    'data'           => '2027-11-14',
    'horario'        => '18:00',
    'convidados'     => 15,
    'aniversariante' => 'Michael',
    'observacoes'    => 'Sem animatrônicos no salão principal, por favor.',
    'csrf_token'     => $csrfToken
];

$resT3 = fazerRequisicao('/controllers/reservaController.php', 'POST', $dadosReservaCorreta, true);
relatarTeste("Teste 3: Criação de Reserva (Logado)", 201, $resT3['codigo']); 

// ==================================================================
// TESTE 4: Reserva com Dados Incompletos (Falta 'responsavel')
// ==================================================================
$dadosReservaIncompleta = $dadosReservaCorreta;
$dadosReservaIncompleta['responsavel'] = ''; 

$resT4 = fazerRequisicao('/controllers/reservaController.php', 'POST', $dadosReservaIncompleta, true);
relatarTeste("Teste 4: Reserva com Dados Inválidos", 400, $resT4['codigo']);

// ==================================================================
// TESTE 4.1 (EXTRA): Falha de CSRF
// ==================================================================
$dadosSemCsrf = $dadosReservaCorreta;
$dadosSemCsrf['csrf_token'] = 'token_invalido';
$resT4_1 = fazerRequisicao('/controllers/reservaController.php', 'POST', $dadosSemCsrf, true);
relatarTeste("Teste 4.1: Falha no CSRF Token", 401, $resT4_1['codigo']);

// ==================================================================
// TESTE 5: Bloqueio de Acesso a Rota Restrita (RBAC - Sem Sessão)
// ==================================================================
if (file_exists($cookieFile)) {
    unlink($cookieFile); 
}

$resT5 = fazerRequisicao('/controllers/reservaController.php', 'POST', $dadosReservaCorreta, false);
relatarTeste("Teste 5: Bloqueio de Acesso s/ Sessão", 401, $resT5['codigo']);

echo "\n=== BATERIA DE TESTES CONCLUÍDA ===\n";