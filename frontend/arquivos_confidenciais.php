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

// TRAVA DE SEGURANÇA: Apenas administradores (William / Henry)
if (!isset($_SESSION['usuario_id']) || ($_SESSION['perfil'] ?? '') !== 'admin') {
    header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Arquivos Confidenciais | Nível 5 Admin</title>
    <link rel="stylesheet" href="assets/geral.css"/>
    <style>
        .terminal-container {
            max-width: 900px;
            margin: 40px auto;
            background-color: #050505;
            border: 2px solid #9e1b1b;
            box-shadow: 0 0 15px rgba(158, 27, 27, 0.4);
            padding: 30px;
            font-family: 'Courier New', Courier, monospace;
            color: #00ff00;
            border-radius: 8px;
        }
        .terminal-header {
            border-bottom: 1px solid #9e1b1b;
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: #9e1b1b;
        }
        .relatorio-item {
            background-color: #111;
            border-left: 3px solid #d4af37;
            padding: 15px;
            margin-bottom: 15px;
            color: #ddd;
        }
        .relatorio-item h4 {
            color: #d4af37;
            margin-top: 0;
        }
    </style>
</head>

<body>
    <header>
        <a href="home.php" class="logo-link">
            <figure><img src="imagens/logo_branca.webp" alt="Logo" width="100"></figure>
            <h1>Fazbear Entertainment</h1>
        </a>
        <nav>
            <a href="home.php" style="text-align: center; line-height: 1.2;">Home</a>
            <a href="sobre.php" style="text-align: center; line-height: 1.2;">Sobre a empresa</a>
            
            <!-- Menu Dropdown -->
            <div class="dropdown" style="text-align: center;">
                <button class="dropbtn" style="text-align: center; line-height: 1.2;">Franquias</button>
                <div class="dropdown-content" style="text-align: left;">
                    <a href="family_dinner.php">Fredbear's Family Diner</a>
                    <a href="freddy_1985.php">Freddy Fazbear's Pizza (1983-1985)</a>
                    <a href="freddy_1987.php">Freddy Fazbear's Pizza (1987)</a>
                    <a href="freddy_1990.php">Freddy Fazbear's Pizza (anos 90)</a>
                </div>
            </div>

            <a href="novidade.php" style="text-align: center; line-height: 1.2;">O Mega Pizzaplex</a>

            <?php
            $perfilNav = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : 'visitante';

            if ($perfilNav === 'visitante' || $perfilNav === 'cliente') {
                echo '<a href="reserva.php" style="text-align: center; line-height: 1.2;">Reservar festa</a>';
                echo '<a href="vagas.php" style="text-align: center; line-height: 1.2;">Contratação</a>';
            }

            if (isset($_SESSION['usuario_id'])) {
                if ($perfilNav === 'cliente') {
                    echo '<a href="minhas_reservas.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Minhas Reservas</a>';
                } elseif ($perfilNav === 'funcionario') {
                    echo '<a href="banco_horas.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Banco de Horas</a>';
                } elseif ($perfilNav === 'admin') {
                    echo '<a href="painel_rh.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Painel do RH</a>';
                    echo '<a href="arquivos_confidenciais.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Arquivos Confidenciais</a>';
                }
                
                $nomeCompleto = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Visitante';
                $primeiroNome = explode(' ', trim($nomeCompleto))[0]; 

                echo '<div class="user-logout-container" style="display: flex; flex-direction: column; align-items: center; gap: 4px; margin-left: 15px;">';
                echo '<span style="font-size: 0.85rem; color: var(--cor-dourada, #e6b800); font-weight: bold;">Olá, ' . htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') . '</span>';
                
                echo '<style>
                        .btn-sair-vermelho { background-color: #b5382a !important; color: #ffffff !important; }
                        .btn-sair-vermelho:hover { background-color: var(--cor-dourada, #e6b800) !important; color: #000000 !important; }
                    </style>';
                
                echo '<a href="../backend/controllers/logoutController.php" class="btn-login btn-sair-vermelho" style="text-align: center; margin: 0; padding: 6px 16px; line-height: 1.2;">Sair</a>';
                echo '</div>';

            } else {
                echo '<a href="login.php" class="btn-login" style="text-align: center; line-height: 1.2;">Login</a>';
            }
            ?>
        </nav>
    </header>

    <main>
        <div class="terminal-container">
            <div class="terminal-header">
                <h2>🔒 DIRETORIA EXECUTIVA — DOCUMENTOS CONFIDENCIAIS</h2>
                <p>AUTENTICADO COMO: <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong> [CREDENCIAL NIVEL 5]</p>
            </div>

            <div class="relatorio-item">
                <h4>📄 REGISTRO #001 — PROTOCOLO DOS ANIMATRÔNICOS GLAMROCK</h4>
                <p>Todos os modelos Glamrock devem passar por manutenção de rotina à meia-noite. Relatos de comportamentos agressivos em áreas não monitoradas devem ser ignorados e atribuídos a falhas na Inteligência Artificial.</p>
            </div>

            <div class="relatorio-item">
                <h4>📄 REGISTRO #002 — INCIDENTE NAS SALAS SUBTERRÂNEAS</h4>
                <p>Acesso bloqueado aos níveis inferiores do Mega Pizzaplex. Qualquer escavação sem autorização prévia do Sr. Afton resultará em rescisão sumária de contrato.</p>
            </div>

            <div class="relatorio-item">
                <h4>📄 REGISTRO #003 — ISENÇÃO DE RESPONSABILIDADE RH</h4>
                <p>Lembrete corporativo: O termo de isenção assinado pelos funcionários nos previne contra processos civis. Manter o treinamento de segurança em dia.</p>
            </div>
        </div>
    </main>
</body>
</html>