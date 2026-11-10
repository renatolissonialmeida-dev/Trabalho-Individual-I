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

// 2. Trava de perfil: Impedir que colaboradores ou administradores acessem o formulário de candidatura
$perfilAtual = $_SESSION['perfil'] ?? '';
if ($perfilAtual === 'funcionario' || $perfilAtual === 'admin') {
    header("Location: home.php");
    exit;
}

// 3. Gera o Token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Trabalhe no Freddy Fazbear's Mega Pizzaplex! Vagas abertas para guarda noturno, atendente de creche e técnicos em robótica. Candidate-se agora.">
    <title>Contratação | Fazbear Entertainment</title>
    <link rel="stylesheet" href="assets/geral.css"/>
    <link rel="stylesheet" href="assets/vagas.css">
</head>

<body>
    <!-- Campo oculto para envio do Token CSRF via JavaScript -->
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
        <section id="beneficios">
            <h2>Trabalhe no Freddy Fazbear's Mega Pizzaplex</h2>
            <p>Buscamos profissionais dedicados, corajosos e com alta tolerância a ambientes de pouca iluminação! Na Fazbear Entertainment, valorizamos nossa equipe de linha de frente.</p>

            <h3>O que oferecemos aos nossos colaboradores:</h3>

            <ul>
                <li><b>Desconto Faz-Lunch:</b> 10% de desconto na compra de pizzas de pepperoni durante seu turno.</li>
                <li><b>Equipamento Oficial:</b> Fornecimento gratuito de um relógio tático <i>Faz-Watch</i>.</li>
                <li><b>Plano de Carreira:</b> Possibilidade de promoção rápida (devido à alta rotatividade de pessoal).</li>
                <li><b>Assistência Médica Básica:</b> Cobertura para curativos simples e soro fisiológico no local.</li>
            </ul>

            <p>
                <b>Documentação Obrigatória:</b> 
                <a href="arquivos/termo_sigilo.pdf" download>Baixe aqui o Termo de Isenção de Responsabilidade e Sigilo em PDF</a>
            </p>
        </section>

        <hr />

        <section id="formulario-vagas">
            <h3>Formulário de Candidatura</h3>

            <form id="form-candidatura-fazbear">
                <!-- Grupo 1: Dados de Contato -->
                <fieldset>
                    <legend><b>1. Dados Pessoais e Contato</b></legend>
            
                    <p>
                        <label for="nome">Nome Completo:</label><br />
                        <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo" required />
                    </p>

                    <p>
                        <label for="email">E-mail para Contato:</label><br />
                        <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required />
                    </p>

                    <p>
                        <label for="telefone">Telefone:</label><br />
                        <input type="tel" id="telefone" name="telefone" placeholder="(00) 90000-0000" required />
                    </p>
                </fieldset>

                <br />

                <!-- Grupo 2: Opções do Cargo -->
                <fieldset>
                    <legend><b>2. Seleção de Vaga e Turno</b></legend>

                    <p>
                        <label for="cargo">Qual vaga você deseja disputar?</label><br />
                        <select id="cargo" name="cargo" required>
                            <option value="" disabled selected>-- Selecione um cargo --</option>
                            <option value="Guarda Noturno Chefe">Guarda Noturno Chefe</option>
                            <option value="Atendente da Creche">Atendente da Creche (Tolerância a escuro exigida)</option>
                            <option value="Técnico em Robótica">Técnico em Robótica (Glamrocks e S.T.A.F.F. Bots)</option>
                            <option value="Faxineiro">Faxineiro (Saiba limpar manchas difíceis e sangue)</option>
                            <option value="Cozinheiro de Pizzas">Cozinheiro de Pizzas Automático</option>
                        </select>
                    </p>

                    <p>
                        <label>Turno de Preferência:</label><br />
                        <input type="radio" id="diurno" name="turno" value="Diurno (08:00 - 16:00)" />
                        <label for="diurno">Diurno (08:00 às 16:00)</label><br />

                        <input type="radio" id="noturno" name="turno" value="Noturno (00:00 - 06:00)" checked />
                        <label for="noturno">Noturno / Madrugada (00:00 às 06:00 - Adicional Noturno)</label>
                    </p>

                    <p>
                        <label for="experiencia">Descreva sua experiência prévia (e confirme que não é um repórter investigativo):</label><br />
                        <textarea id="experiencia" name="experiencia" rows="4" cols="40" placeholder="Conte brevemente onde já trabalhou..."></textarea>
                    </p>
                </fieldset>

                <br />

                <!-- Grupo 3: Termos e Envio -->
                <fieldset>
                    <legend><b>3. Confirmação e Aceite</b></legend>

                    <p>
                        <input type="checkbox" id="termo" name="termo" required />
                        <label for="termo">Declaro estar ciente dos riscos de ruídos metálicos noturnos e isento a Fazbear Entertainment de qualquer complicação de saúde física ou mental.</label>
                    </p>

                    <p>
                        <button type="submit">Aceitar Riscos e Enviar Candidatura</button>
                    </p>
                </fieldset>
            </form>
        </section>
        <hr />
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
    <script src="assets/vagas.js"></script>
</body>
</html>