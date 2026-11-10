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

// 1. Redireciona se NÃO estiver logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Trava de Segurança por Perfil: Apenas 'funcionario' (ou 'admin') pode acessar
$perfilAtual = $_SESSION['perfil'] ?? '';
if ($perfilAtual !== 'funcionario') {
    header("Location: home.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$candidaturas = [];
$erroBanco = null;

try {
    if (file_exists('backend/config/database.php')) {
        require_once 'backend/config/database.php';
    } elseif (file_exists('../backend/config/database.php')) {
        require_once '../backend/config/database.php';
    }
    
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        SELECT * FROM candidaturas 
        WHERE usuario_id = ? 
        ORDER BY id DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $candidaturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $erroBanco = "Não foi possível carregar o banco de horas e candidaturas.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Quadro de Horários e Banco de Horas dos colaboradores da Fazbear Entertainment.">
    <title>Banco de Horas | Fazbear Entertainment</title>
    <link rel="stylesheet" href="assets/geral.css"/>
    <link rel="stylesheet" href="assets/banco_horas.css">
</head>

<body>
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
            <h2>Quadro de Banco de Horas e Colaboradores</h2>
            <p class="subtitulo-painel">Consulte sua ficha de contratação, turno de plantão e escala no Mega Pizzaplex.</p>

            <?php if ($erroBanco): ?>
                <aside class="aviso-erro">
                    <h3>⚠️ Erro do Sistema</h3>
                    <p><?php echo htmlspecialchars($erroBanco); ?></p>
                </aside>
            <?php elseif (empty($candidaturas)): ?>
                <div class="card-vazio">
                    <div class="icone-festa">🪪</div>
                    <h3>Nenhum plantão ativo no momento</h3>
                    <p>Sua ficha não consta no sistema de escalas atual. Por favor, procure a administração ou o RH para registrar seu novo turno no Mega Pizzaplex.</p>
                </div>
            <?php else: ?>
                <div class="grid-candidaturas">
                    <?php foreach ($candidaturas as$cand): ?>
                        <?php 
                            $criadoFmt = date('d/m/Y \à\s H:i', strtotime($cand['criado_em']));
                            $mat = htmlspecialchars($cand['matricula']);
                            $isNoturno = strpos(strtolower($cand['turno']), 'noturno') !== false;
                            $escalaHoras =$isNoturno ? '00:00 às 06:00 (30h/sem + Adicional Noturno)' : '08:00 às 16:00 (40h/sem)';
                        ?>
                        <article class="card-cracha" id="cracha-<?php echo $mat; ?>">
                            <header class="card-cracha-header">
                                <div class="matricula-badge">
                                    <small>Matrícula Corporativa</small>
                                    <strong><?php echo $mat; ?></strong>
                                </div>
                                <span class="status-badge status-ativo">● Ativo no RH</span>
                            </header>

                            <div class="card-cracha-body">
                                <div class="info-grupo destaque-cargo">
                                    <h4>💼 <?php echo htmlspecialchars($cand['cargo']); ?></h4>
                                    <p class="turno-texto">
                                        ⏰ <strong>Horário de Escala:</strong> <?php echo $escalaHoras; ?>
                                    </p>
                                </div>

                                <hr class="card-divisor" />

                                <div class="detalhes-grid">
                                    <div class="detalhe-item">
                                        <span class="label">Colaborador:</span>
                                        <span class="valor"><?php echo htmlspecialchars($cand['nome']); ?></span>
                                    </div>
                                    <div class="detalhe-item">
                                        <span class="label">E-mail:</span>
                                        <span class="valor"><?php echo htmlspecialchars($cand['email']); ?></span>
                                    </div>
                                    <div class="detalhe-item">
                                        <span class="label">Telefone:</span>
                                        <span class="valor"><?php echo htmlspecialchars($cand['telefone']); ?></span>
                                    </div>
                                    <div class="detalhe-item">
                                        <span class="label">Turno Selecionado:</span>
                                        <span class="valor"><?php echo htmlspecialchars($cand['turno']); ?></span>
                                    </div>
                                </div>

                                <div class="bloco-experiencia">
                                    <strong>Histórico / Experiência Prévia:</strong>
                                    <p><?php echo nl2br(htmlspecialchars($cand['experiencia'])); ?></p>
                                </div>
                            </div>

                            <footer class="card-cracha-footer">
                                <small>Ficha criada em <?php echo $criadoFmt; ?></small>
                                
                                <div class="acoes-card">
                                    <button class="btn-acao btn-editar" onclick="carregarModalEdicao('<?php echo $mat; ?>')">✏️ Editar Ficha</button>
                                    <button class="btn-acao btn-cancelar" onclick="confirmarExclusao('<?php echo $mat; ?>')">🗑️ Cancelar Ficha</button>
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
    const listaCandidaturas = <?php echo json_encode($candidaturas); ?>;

    function carregarModalEdicao(matricula) {
        const cand = listaCandidaturas.find(c => c.matricula === matricula);
        if (cand) {
            abrirModalEditar(cand);
        } else {
            alert("Ficha não encontrada.");
        }
    }

    function confirmarExclusao(matricula) {
        const modalExistente = document.querySelector(".modal-overlay-fazbear");
        if (modalExistente) modalExistente.remove();

        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";

        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear" style="max-width: 450px; text-align: center;">
                <div class="modal-header-fazbear">
                    <h3>🗑️ CANCELAR FICHA DE FUNCIONÁRIO</h3>
                    <p>Deseja remover a ficha de matrícula <strong>${matricula}</strong> do banco de horas do RH?</p>
                </div>
                <div style="display: flex; gap: 15px; justify-content: center; margin-top: 20px;">
                    <button type="button" id="btn-nao-cancelar" class="btn-acao" style="background-color: #6c757d; color: white; padding: 10px 20px;">Voltar</button>
                    <button type="button" id="btn-sim-cancelar" class="btn-acao btn-cancelar" style="padding: 10px 20px;">Confirmar Remoção</button>
                </div>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        document.getElementById("btn-nao-cancelar").addEventListener("click", () => modalOverlay.remove());

        document.getElementById("btn-sim-cancelar").addEventListener("click", async () => {
            const tokenCsrf = document.getElementById('csrf_token').value;
            const formData = new FormData();
            formData.append('csrf_token', tokenCsrf);
            formData.append('matricula', matricula);

            try {
                const resposta = await fetch('../backend/controllers/deletarVagasController.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    alert(resultado.mensagem);
                    location.reload();
                } else {
                    alert("Erro ao remover: " + resultado.mensagem);
                }
            } catch (erro) {
                alert("Erro ao conectar com o servidor.");
            }
        });
    }

    function abrirModalEditar(cand) {
        const modalExistente = document.querySelector(".modal-overlay-fazbear");
        if (modalExistente) modalExistente.remove();

        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";

        const nome = cand.nome ? cand.nome : '';
        const em   = cand.email ? cand.email : '';
        const tel  = cand.telefone ? cand.telefone : '';
        const cg   = cand.cargo ? cand.cargo : '';
        const tur  = cand.turno ? cand.turno : '';
        const exp  = cand.experiencia ? cand.experiencia : '';

        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear" style="max-width: 550px; text-align: left;">
                <div class="modal-header-fazbear">
                    <h3>✏️ EDITAR FICHA ${cand.matricula}</h3>
                    <p>Atualize as informações do seu registro corporativo</p>
                </div>
                <form id="form-editar-candidatura" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
                    <input type="hidden" id="edit-matricula" value="${cand.matricula}">
                    
                    <label><strong>Nome do Colaborador:</strong>
                        <input type="text" id="edit-nome" value="${nome}" required style="width: 100%; padding: 8px; margin-top: 4px;">
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
                        <label style="flex: 1;"><strong>Cargo:</strong>
                            <select id="edit-cargo" required style="width: 100%; padding: 8px; margin-top: 4px;">
                                <option value="Guarda Noturno Chefe" ${cg === 'Guarda Noturno Chefe' ? 'selected' : ''}>Guarda Noturno Chefe</option>
                                <option value="Atendente da Creche" ${cg === 'Atendente da Creche' ? 'selected' : ''}>Atendente da Creche</option>
                                <option value="Técnico em Robótica" ${cg === 'Técnico em Robótica' ? 'selected' : ''}>Técnico em Robótica</option>
                                <option value="Faxineiro" ${cg === 'Faxineiro' ? 'selected' : ''}>Faxineiro</option>
                                <option value="Cozinheiro de Pizzas" ${cg === 'Cozinheiro de Pizzas' ? 'selected' : ''}>Cozinheiro de Pizzas</option>
                            </select>
                        </label>

                        <label style="flex: 1;"><strong>Turno:</strong>
                            <select id="edit-turno" required style="width: 100%; padding: 8px; margin-top: 4px;">
                                <option value="Diurno (08:00 - 16:00)" ${tur.indexOf('Diurno') !== -1 ? 'selected' : ''}>Diurno (08:00 - 16:00)</option>
                                <option value="Noturno (00:00 - 06:00)" ${tur.indexOf('Noturno') !== -1 ? 'selected' : ''}>Noturno (00:00 - 06:00)</option>
                            </select>
                        </label>
                    </div>

                    <label><strong>Experiência / Observações:</strong>
                        <textarea id="edit-experiencia" rows="3" style="width: 100%; padding: 8px; margin-top: 4px;">${exp}</textarea>
                    </label>

                    <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 15px;">
                        <button type="button" id="btn-fechar-editar" style="background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Cancelar</button>
                        <button type="submit" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Salvar Ficha</button>
                    </div>
                </form>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        document.getElementById("btn-fechar-editar").addEventListener("click", () => modalOverlay.remove());

        document.getElementById("form-editar-candidatura").addEventListener("submit", async (e) => {
            e.preventDefault();

            const tokenCsrf = document.getElementById('csrf_token').value;
            const formData = new FormData();
            formData.append('csrf_token', tokenCsrf);
            formData.append('matricula', document.getElementById('edit-matricula').value);
            formData.append('nome', document.getElementById('edit-nome').value);
            formData.append('email', document.getElementById('edit-email').value);
            formData.append('telefone', document.getElementById('edit-telefone').value);
            formData.append('cargo', document.getElementById('edit-cargo').value);
            formData.append('turno', document.getElementById('edit-turno').value);
            formData.append('experiencia', document.getElementById('edit-experiencia').value);

            try {
                const resposta = await fetch('../backend/controllers/editarVagasController.php', {
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