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

// TRAVA DE SEGURANÇA: Apenas administradores podem acessar
$perfilAtual =$_SESSION['perfil'] ?? '';
if ($perfilAtual !== 'admin') {
    header("Location: home.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$candidaturas = [];$erroBanco = null;

try {
    if (file_exists('backend/config/database.php')) {
        require_once 'backend/config/database.php';
    } elseif (file_exists('../backend/config/database.php')) {
        require_once '../backend/config/database.php';
    }
    
    $pdo = Database::getConnection();
    // Busca apenas as candidaturas pendentes de análise
    $stmt =$pdo->query("SELECT * FROM candidaturas WHERE status = 'pendente' OR status IS NULL ORDER BY id DESC");
    $candidaturas =$stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {$erroBanco = "Erro ao carregar a lista de candidatos do RH.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Painel do RH | Fazbear Corporate</title>
    <link rel="stylesheet" href="assets/geral.css"/>
    <link rel="stylesheet" href="assets/banco_horas.css">
</head>

<body>
    <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

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
            $perfilNav = isset($_SESSION['perfil']) ?$_SESSION['perfil'] : 'visitante';

            if ($perfilAtual === 'visitante' || $perfilAtual === 'cliente') {
                echo '<a href="reserva.php" style="text-align: center; line-height: 1.2;">Reservar festa</a>';
                echo '<a href="vagas.php" style="text-align: center; line-height: 1.2;">Contratação</a>';
            }

            if (isset($_SESSION['usuario_id'])) {
                if ($perfilNav === 'cliente') {
                    echo '<a href="minhas_reservas.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Minhas Reservas</a>';
                } elseif ($perfilNav === 'funcionario') { echo '<a href="banco_horas.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Banco de Horas</a>'; } elseif ($perfilNav === 'admin') {
                    echo '<a href="painel_rh.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Painel do RH</a>';
                    echo '<a href="arquivos_confidenciais.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Arquivos Confidenciais</a>';
                }
                
                $nomeCompleto = isset($_SESSION['usuario_nome']) ?$_SESSION['usuario_nome'] : 'Visitante';
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
        <section class="resumo-painel">
            <h2>👔 Painel de Recrutamento & Seleção (RH)</h2>
            <p class="subtitulo-painel">Gestão de Candidaturas Pendentes do Mega Pizzaplex.</p>

            <div id="container-candidaturas">
                <?php if (empty($candidaturas)): ?>
                    <div class="card-vazio" id="mensagem-vazio">
                        <div class="icone-festa">📂</div>
                        <h3>Nenhuma candidatura pendente no momento.</h3>
                    </div>
                <?php else: ?>
                    <div class="grid-candidaturas" id="grid-cards">
                        <?php foreach ($candidaturas as$cand): ?>
                            <article class="card-cracha" id="cand-card-<?php echo $cand['id']; ?>">
                                <header class="card-cracha-header">
                                    <div class="matricula-badge">
                                        <small>Matrícula Gerada</small>
                                        <strong><?php echo htmlspecialchars($cand['matricula']); ?></strong>
                                    </div>
                                    <span class="status-badge status-ativo">Pendente</span>
                                </header>

                                <div class="card-cracha-body">
                                    <h4>💼 <?php echo htmlspecialchars($cand['cargo']); ?></h4>
                                    <p><strong>Nome:</strong> <?php echo htmlspecialchars($cand['nome']); ?></p>
                                    <p><strong>E-mail Pessoal:</strong> <?php echo htmlspecialchars($cand['email']); ?></p>
                                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($cand['telefone']); ?></p>
                                    <p><strong>Turno Pretendido:</strong> <?php echo htmlspecialchars($cand['turno']); ?></p>
                                    <div class="bloco-experiencia">
                                        <strong>Experiência Informada:</strong>
                                        <p><?php echo nl2br(htmlspecialchars($cand['experiencia'])); ?></p>
                                    </div>
                                </div>

                                <footer class="card-cracha-footer">
                                    <small>Enviado em <?php echo date('d/m/Y', strtotime($cand['criado_em'])); ?></small>
                                    <div class="acoes-card" style="display: flex; gap: 10px; margin-top: 10px;">
                                        <button class="btn-acao" style="background-color: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;" onclick="processarCandidato(<?php echo $cand['id']; ?>, 'aprovar', '<?php echo htmlspecialchars($cand['nome']); ?>')">
                                            ✅ Efetivar
                                        </button>
                                        <button class="btn-acao" style="background-color: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer;" onclick="processarCandidato(<?php echo $cand['id']; ?>, 'recusar', '<?php echo htmlspecialchars($cand['nome']); ?>')">
                                            ❌ Recusar
                                        </button>
                                    </div>
                                </footer>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <script>
    async function processarCandidato(candidaturaId, acao, nome) {
        const mensagemConfirmacao = acao === 'aprovar' 
            ? `Deseja aprovar a contratação de ${nome}? Isso gerará a conta corporativa @fazbear.com.`
            : `Tem certeza que deseja recusar a candidatura de ${nome}?`;

        if (!confirm(mensagemConfirmacao)) return;

        const tokenCsrf = document.getElementById('csrf_token').value;
        const formData = new FormData();
        formData.append('csrf_token', tokenCsrf);
        formData.append('candidatura_id', candidaturaId);
        formData.append('acao', acao);

        try {
            const resposta = await fetch('../backend/controllers/aprovarCandidaturaController.php', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            });

            const resultado = await resposta.json();

            if (resultado.sucesso) {
                if (resultado.acao === 'aprovar') {
                    alert(`Contratação efetuada com sucesso!\n\nE-mail Corporativo: ${resultado.email_corporativo}\nSenha Temporária: ${resultado.senha_temp}`);
                } else {
                    alert(`Candidatura de ${nome} foi recusada.`);
                }

                // Remove o card da tela imediatamente
                const card = document.getElementById(`cand-card-${candidaturaId}`);
                if (card) {
                    card.remove();
                }

                // Exibe o estado vazio se não houver mais nenhum candidato na lista
                const grid = document.getElementById('grid-cards');
                if (grid && grid.children.length === 0) {
                    document.getElementById('container-candidaturas').innerHTML = `
                        <div class="card-vazio">
                            <div class="icone-festa">📂</div>
                            <h3>Nenhuma candidatura pendente no momento.</h3>
                        </div>`;
                }
            } else {
                alert("Erro: " + resultado.mensagem);
            }
        } catch (e) {
            alert("Erro ao conectar com o servidor.");
        }
    }
    </script>
</body>
</html>