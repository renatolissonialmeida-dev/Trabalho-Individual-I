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

// 1. Garante que apenas usuários logados acessem a página
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Trava de perfil: Apenas clientes podem acessar esta página
$perfilAtual = $_SESSION['perfil'] ?? '';
if ($perfilAtual !== 'cliente') {
    header("Location: home.php");
    exit;
}

// Gera o token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Tenta carregar a conexão com o banco
$reservas = [];
$erroBanco = null;

try {
    if (file_exists('backend/config/database.php')) {
        require_once 'backend/config/database.php';
    } elseif (file_exists('../backend/config/database.php')) {
        require_once '../backend/config/database.php';
    }
    
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM reservas 
        WHERE usuario_id = ? 
        ORDER BY data_reserva DESC, horario DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $erroBanco = "Não foi possível carregar o histórico de reservas no momento.";
}

// Função para formatar os nomes dos pacotes
function formatarPacote($pacote) {
    $mapa = [
        'bronze' => 'Pacote Bronze (Classic)',
        'gold'   => 'Pacote Glamrock Gold',
        'vip'    => 'Pacote VIP Superstar'
    ];
    $chave = strtolower(trim($pacote));
    return isset($mapa[$chave]) ? $mapa[$chave] : $pacote;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Consulte o histórico e comprovantes de suas reservas no Fazbear Entertainment Mega Pizzaplex.">
    <title>Minhas Reservas | Fazbear Entertainment</title>
    <link rel="stylesheet" href="assets/geral.css"/>
    <link rel="stylesheet" href="assets/minhas_reservas.css">
</head>

<body>
    <!-- Campo oculto contendo o token CSRF para uso no JS -->
    <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    <header>
        <a href="home.php" class="logo-link">
            <figure>
                <img src="imagens/logo_branca.webp" alt="Logo empresarial" width="100">
            </figure>
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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $perfilAtual = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : 'visitante';

            // Oculta as abas "Reservar festa" e "Contratação" de funcionários e admins
            if ($perfilAtual === 'visitante' || $perfilAtual === 'cliente') {
                echo '<a href="reserva.php" style="text-align: center; line-height: 1.2;">Reservar festa</a>';
                echo '<a href="vagas.php" style="text-align: center; line-height: 1.2;">Contratação</a>';
            }

            if (isset($_SESSION['usuario_id'])) {
                
                // Renderiza links específicos por perfil
                if ($perfilAtual === 'cliente') {
                    echo '<a href="minhas_reservas.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Minhas Reservas</a>';
                } elseif ($perfilAtual === 'funcionario') {
                    echo '<a href="banco_horas.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Banco de Horas</a>';
                } elseif ($perfilAtual === 'admin') {
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
        <section class="resumo-painel">
            <h2>Minhas Reservas</h2>
            <p class="subtitulo-painel">Acompanhe, edite ou cancele as festas agendadas para a sua família no Mega Pizzaplex.</p>

            <?php if ($erroBanco): ?>
                <aside class="aviso-erro">
                    <h3>⚠️ Erro do Sistema</h3>
                    <p><?php echo htmlspecialchars($erroBanco); ?></p>
                </aside>
            <?php elseif (empty($reservas)): ?>
                <div class="card-vazio">
                    <div class="icone-festa">🎈</div>
                    <h3>Você ainda não possui nenhuma festa reservada!</h3>
                    <p>Garanta um dia inesquecível com os Glamrocks para o seu aniversário.</p>
                    <a href="reserva.php" class="btn-agendar-nova">Agendar uma Festa Agora</a>
                </div>
            <?php else: ?>
                <div class="grid-reservas">
                    <?php foreach ($reservas as$res): ?>
                        <?php 
                            $dataFmt = date('d/m/Y', strtotime($res['data_reserva']));$criadoFmt = date('d/m/Y \à\s H:i', strtotime($res['criado_em']));
                            $codRes = htmlspecialchars($res['codigo_reserva']);
                        ?>
                        <article class="card-reserva" id="card-<?php echo $codRes; ?>">
                            <header class="card-reserva-header">
                                <div class="codigo-badge">
                                    <small>Código do Comprovante</small>
                                    <strong><?php echo $codRes; ?></strong>
                                </div>
                                <span class="status-badge status-confirmada">✓ Registrada</span>
                            </header>

                            <div class="card-reserva-body">
                                <div class="info-grupo destaque-festa">
                                    <h4>🎉 <?php echo htmlspecialchars(formatarPacote($res['pacote'])); ?></h4>
                                    <p class="data-hora">
                                        📅 <strong><?php echo $dataFmt; ?></strong> às ⏰ <strong><?php echo htmlspecialchars($res['horario']); ?></strong>
                                    </p>
                                </div>

                                <hr class="card-divisor" />

                                <div class="detalhes-grid">
                                    <div class="detalhe-item">
                                        <span class="label">Responsável:</span>
                                        <span class="valor"><?php echo htmlspecialchars($res['nome_responsavel']); ?></span>
                                    </div>
                                    <div class="detalhe-item">
                                        <span class="label">E-mail:</span>
                                        <span class="valor"><?php echo htmlspecialchars($res['email']); ?></span>
                                    </div>
                                    <div class="detalhe-item">
                                        <span class="label">Telefone:</span>
                                        <span class="valor"><?php echo htmlspecialchars($res['telefone']); ?></span>
                                    </div>
                                    <div class="detalhe-item">
                                        <span class="label">Aniversariante:</span>
                                        <span class="valor"><?php echo htmlspecialchars($res['aniversariante']); ?></span>
                                    </div>
                                    <div class="detalhe-item">
                                        <span class="label">Convidados Estimados:</span>
                                        <span class="valor"><?php echo htmlspecialchars($res['convidados']); ?> pessoas</span>
                                    </div>
                                </div>

                                <?php if (!empty($res['observacoes'])): ?>
                                    <div class="bloco-observacoes">
                                        <strong>Observações / Restrições:</strong>
                                        <p><?php echo nl2br(htmlspecialchars($res['observacoes'])); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <footer class="card-reserva-footer">
                                <small>Registrado em <?php echo $criadoFmt; ?></small>
                                
                                <div class="acoes-card">
                                    <button class="btn-acao btn-editar" onclick="carregarModalEdicao('<?php echo $codRes; ?>')">✏️ Editar</button>
                                    <button class="btn-acao btn-cancelar" onclick="confirmarCancelamento('<?php echo $codRes; ?>')">🗑️ Cancelar</button>
                                </div>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <a href="home.php" class="footer-top logo-container logo-link">
            <img src="imagens/logo_branca.webp" alt="Logo Fazbear" class="footer-icon">
            <h2 class="footer-logo-text">Fazbear<br>Entertainment</h2>
        </a>

        <hr class="footer-divider" />

        <div class="footer-grid">
            <div class="footer-col">
                <h3>Nossa Localização</h3>
                <p>1987 Hurricane, Utah<br>
                Mega Pizzaplex Center<br>
                (1-800) FAZ-BEAR</p>

                <h3 class="margin-top">Horários</h3>
                <p><b>Seg - Qui:</b> 11:00 AM - 21:00 PM<br>
                <b>Sex:</b> 11:00 AM - 00:00 PM<br>
                <b>Sáb:</b> 10:00 AM - 00:00 PM<br>
                <b>Dom:</b> 11:00 AM - 21:00 PM</p>
            </div>

            <div class="footer-col">
                <h3>Acesso Rápido</h3>
                <ul>
                    <li><a href="home.php">Home</a></li>
                    <li><a href="sobre.php">Sobre a Empresa</a></li>
                    <li><a href="novidade.php">O Mega Pizzaplex</a></li>
                    <li><a href="reserva.php">Reservar Festa</a></li>
                    <li><a href="vagas.php">Contratação</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="family_dinner.php">Fredbear's Family Diner</a></li>
                    <li><a href="freddy_1985.php">Freddy Fazbear's Pizza (1983-1985)</a></li>
                    <li><a href="freddy_1987.php">Freddy Fazbear's Pizza (1987)</a></li>
                    <li><a href="freddy_1990.php">Freddy Fazbear's Pizza (anos 90)</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Sobre o Projeto</h3>
                <ul>
                    <li><b>Autor:</b> Renato Lissoni de Almeida</li>
                    <li><b>Disciplina:</b> Sistemas Computacionais</li>
                    <li><b>Data:</b> 22 de setembro de 2026</li>
                    <li><b>Contato RH:</b> rl.almeida@unesp.br</li>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Fontes e Referências</h3>
                <ul>
                    <li><a href="https://store.steampowered.com/app/732690/FIVE_NIGHTS_AT_FREDDYS_HELP_WANTED/" target="_blank">FNAF no Steam</a></li>
                    <li><a href="https://freddy-fazbears-pizza.fandom.com/wiki/Freddy_Fazbear%27s_Pizza_Wiki" target="_blank">FNAF Wiki</a></li>
                    <li><a href="https://commons.wikimedia.org/" target="_blank">Wikimedia Commons</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p><small>&copy; 2026 Fazbear Entertainment. O desaparecimento de clientes dentro do shopping não é de nossa responsabilidade.</small></p>
        </div>
    </footer>

    <script src="assets/script.js"></script>

    <script>
    const listaReservas = <?php echo json_encode($reservas); ?>;

    function carregarModalEdicao(codigoReserva) {
        const reserva = listaReservas.find(r => r.codigo_reserva === codigoReserva);
        if (reserva) {
            abrirModalEditar(reserva);
        } else {
            alert("Dados da reserva não encontrados.");
        }
    }

    function confirmarCancelamento(codigoReserva) {
        const modalExistente = document.querySelector(".modal-overlay-fazbear");
        if (modalExistente) modalExistente.remove();

        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";

        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear" style="max-width: 450px; text-align: center;">
                <div class="modal-header-fazbear">
                    <h3>🗑️ CANCELAR RESERVA</h3>
                    <p>Tem certeza que deseja cancelar a reserva <strong>${codigoReserva}</strong>? Esta ação não poderá ser desfeita.</p>
                </div>
                <div style="display: flex; gap: 15px; justify-content: center; margin-top: 20px;">
                    <button type="button" id="btn-nao-cancelar" class="btn-acao" style="background-color: #6c757d; color: white; padding: 10px 20px;">Voltar</button>
                    <button type="button" id="btn-sim-cancelar" class="btn-acao btn-cancelar" style="padding: 10px 20px;">Confirmar Cancelamento</button>
                </div>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        document.getElementById("btn-nao-cancelar").addEventListener("click", () => modalOverlay.remove());

        document.getElementById("btn-sim-cancelar").addEventListener("click", async () => {
            const tokenCsrf = document.getElementById('csrf_token').value;
            const formData = new FormData();
            formData.append('csrf_token', tokenCsrf);
            formData.append('codigo_reserva', codigoReserva);

            try {
                const resposta = await fetch('../backend/controllers/deletarReservaController.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    alert(resultado.mensagem);
                    location.reload();
                } else {
                    alert("Erro ao cancelar: " + resultado.mensagem);
                }
            } catch (erro) {
                alert("Erro ao conectar com o servidor.");
            }
        });
    }

    function abrirModalEditar(reserva) {
        const modalExistente = document.querySelector(".modal-overlay-fazbear");
        if (modalExistente) modalExistente.remove();

        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";

        const resp = reserva.nome_responsavel ? reserva.nome_responsavel : '';
        const em   = reserva.email ? reserva.email : '';
        const tel  = reserva.telefone ? reserva.telefone : '';
        const dat  = reserva.data_reserva ? reserva.data_reserva : '';
        const hor  = reserva.horario ? reserva.horario : '';
        const pac  = String(reserva.pacote).toLowerCase();
        const conv = reserva.convidados ? reserva.convidados : '';
        const aniv = reserva.aniversariante ? reserva.aniversariante : '';
        const obs  = reserva.observacoes ? reserva.observacoes : '';

        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear" style="max-width: 550px; text-align: left;">
                <div class="modal-header-fazbear">
                    <h3>✏️ EDITAR RESERVA ${reserva.codigo_reserva}</h3>
                    <p>Atualize as informações da sua festa no Mega Pizzaplex</p>
                </div>
                <form id="form-editar-reserva" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                    <input type="hidden" id="edit-codigo" value="${reserva.codigo_reserva}">
                    
                    <label><strong>Responsável:</strong>
                        <input type="text" id="edit-responsavel" value="${resp}" required style="width: 100%; padding: 8px; margin-top: 4px;">
                    </label>

                    <div style="display: flex; gap: 10px;">
                        <label style="flex: 1;"><strong>E-mail:</strong>
                            <input type="email" id="edit-email" value="${em}" required style="width: 100%; padding: 8px; margin-top: 4px;">
                        </label>
                        <label style="flex: 1;"><strong>Telefone:</strong>
                            <input type="text" id="edit-telefone" value="${tel}" required style="width: 100%; padding: 8px; margin-top: 4px;">
                        </label>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <label style="flex: 1;"><strong>Data da Festa:</strong>
                            <input type="date" id="edit-data" value="${dat}" required style="width: 100%; padding: 8px; margin-top: 4px;">
                        </label>
                        <label style="flex: 1;"><strong>Horário:</strong>
                            <select id="edit-horario" required style="width: 100%; padding: 8px; margin-top: 4px;">
                                <option value="12:00" ${hor === '12:00' ? 'selected' : ''}>12:00 às 15:00</option>
                                <option value="16:00" ${hor === '16:00' ? 'selected' : ''}>16:00 às 19:00</option>
                                <option value="20:00" ${hor === '20:00' ? 'selected' : ''}>20:00 às 23:00</option>
                            </select>
                        </label>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <label style="flex: 1;"><strong>Pacote:</strong>
                            <select id="edit-pacote" required style="width: 100%; padding: 8px; margin-top: 4px;">
                                <option value="bronze" ${pac.indexOf('bronze') !== -1 ? 'selected' : ''}>Pacote Bronze (Classic)</option>
                                <option value="gold" ${pac.indexOf('gold') !== -1 ? 'selected' : ''}>Pacote Glamrock Gold</option>
                                <option value="vip" ${pac.indexOf('vip') !== -1 ? 'selected' : ''}>Pacote VIP Superstar</option>
                            </select>
                        </label>
                        <label style="flex: 1;"><strong>Qtd. Convidados:</strong>
                            <input type="number" id="edit-convidados" min="1" max="50" value="${conv}" required style="width: 100%; padding: 8px; margin-top: 4px;">
                        </label>
                    </div>

                    <label><strong>Aniversariante:</strong>
                        <input type="text" id="edit-aniversariante" value="${aniv}" required style="width: 100%; padding: 8px; margin-top: 4px;">
                    </label>

                    <label><strong>Observações / Restrições:</strong>
                        <textarea id="edit-observacoes" rows="2" style="width: 100%; padding: 8px; margin-top: 4px;">${obs}</textarea>
                    </label>

                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 15px;">
                        <button type="button" id="btn-fechar-editar" style="background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Cancelar</button>
                        <button type="submit" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        document.getElementById("btn-fechar-editar").addEventListener("click", () => modalOverlay.remove());

        document.getElementById("form-editar-reserva").addEventListener("submit", async (e) => {
            e.preventDefault();

            const tokenCsrf = document.getElementById('csrf_token').value;
            const formData = new FormData();
            formData.append('csrf_token', tokenCsrf);
            formData.append('codigo_reserva', document.getElementById('edit-codigo').value);
            formData.append('responsavel', document.getElementById('edit-responsavel').value);
            formData.append('email', document.getElementById('edit-email').value);
            formData.append('telefone', document.getElementById('edit-telefone').value);
            formData.append('pacote', document.getElementById('edit-pacote').value);
            formData.append('data', document.getElementById('edit-data').value);
            formData.append('horario', document.getElementById('edit-horario').value);
            formData.append('convidados', document.getElementById('edit-convidados').value);
            formData.append('aniversariante', document.getElementById('edit-aniversariante').value);
            formData.append('observacoes', document.getElementById('edit-observacoes').value);

            try {
                const resposta = await fetch('../backend/controllers/editarReservaController.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    alert(resultado.mensagem);
                    location.reload();
                } else {
                    alert("Erro ao editar: " + resultado.mensagem);
                }
            } catch (erro) {
                alert("Erro ao conectar com o servidor.");
            }
        });
    }
    </script>
</body>
</html>