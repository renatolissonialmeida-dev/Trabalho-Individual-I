<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fredbear's Family Diner | Fazbear Entertainment</title>
    <meta name="description" content="Conheça as origens da Fazbear Entertainment no clássico Fredbear's Family Diner. Onde a magia e a inovação animatrônica começaram.">
    
    <!-- Seus estilos -->
    <link rel="stylesheet" href="assets/geral.css">
    <link rel="stylesheet" href="assets/family_dinner.css">
</head>
<body>
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

    <main class="historico-family-diner">
        <h2>Fredbear's Family Diner</h2>
        <p class="subtitulo">Onde a magia, a família e a inovação deram seus primeiros passos.</p>

        <article class="intro-texto">
            <p>Antes dos mega-complexos de neon e das pistas de boliche do nosso moderno Pizzaplex, a magia da Fazbear Entertainment nasceu em um lugar muito mais simples, mas igualmente especial. Em meados do final dos anos 70, as portas da <strong>Fredbear's Family Diner</strong> se abriram pela primeira vez, redefinindo o conceito de entretenimento familiar e jantares inesquecíveis.</p>
        </article>

        <h3>Os Astros Originais</h3>
        <p class="texto-apoio">No palco principal da nossa humilde lanchonete original, não tínhamos uma banda completa, mas sim uma dupla dinâmica que roubava a cena e os corações de todas as crianças da cidade:</p>
            
        <div class="galeria-animatronics">
            <!-- Card do Fredbear -->
            <div class="card-animatronic dourado">
                <img src="imagens/Fredbear.webp" alt="Fredbear">
                <h4>Fredbear</h4>
                <p>O carismático urso dourado, sempre com seu icônico chapéu roxo e gravata borboleta, cuja voz animada conduzia as festas de aniversário.</p>
            </div>
            <!-- Card do Spring Bonnie -->
            <div class="card-animatronic dourado">
                <img src="imagens/Spring_bonnie.webp" alt="Spring Bonnie">
                <h4>Spring Bonnie</h4>
                <p>O adorável coelho dourado, braço direito de Fredbear, que sempre trazia um sorriso no rosto e uma melodia no violão.</p>
            </div>
        </div>

        <h3>Inovação e Tecnologia Clássica</h3>
        <article class="tecnologia-texto">
            <p>Hoje estamos acostumados com IA avançada e robôs que caminham livremente, mas na época da Family Diner, a tecnologia que usávamos era uma verdadeira revolução na engenharia robótica!</p>
                
            <blockquote>
                <p>"Uma interação próxima e mágica que definiu o nosso padrão de qualidade em 1983."</p>
            </blockquote>

            <p>Fredbear e Spring Bonnie operavam com o clássico sistema de <strong>Trajes de Molas (Springlocks)</strong>. Essa engenharia brilhante e versátil permitia que os personagens funcionassem de duas maneiras: como animatrônicos autônomos no palco, cantando e dançando; ou, através de um ajuste mecânico nas engrenagens internas, poderiam ser vestidos pelos nossos artistas treinados. Isso significava que eles podiam descer do palco, andar pelo salão, abraçar as crianças e servir fatias de pizza diretamente nas mesas!</p>
        </article>

        <h3>O Fim de uma Era e o Início de um Império</h3>
        <article class="fim-texto">
            <p>Com o sucesso estrondoso, os sorrisos se multiplicaram mais rápido do que o nosso pequeno salão suportava. Em <strong>1983</strong>, a Fredbear's Family Diner fechou as portas em seu formato original.</p>
            <p>Mas não foi um adeus triste! A pequena lanchonete simplesmente ficou pequena demais para os grandes sonhos da Fazbear Entertainment. O encerramento da Family Diner marcou o início de uma nova fase, aposentando os trajes de molas em favor de tecnologias mais modernas e abrindo caminho para a inauguração da nossa primeira grande pizzaria com a turma do Freddy Fazbear que você conhece e ama hoje.</p>
            <p class="nota-final">A Fredbear's Family Diner pode estar no passado, mas a alegria daquela época dourada continua viva no coração da nossa empresa!</p>
        </article>
    </main>

    <footer class="site-footer">
        <!-- Topo do Footer (Logo) -->
        <a href="home.php" class="footer-top logo-container logo-link">
            <img src="imagens/logo_branca.webp" alt="Logo Fazbear" class="footer-icon">
            <h2 class="footer-logo-text">Fazbear<br>Entertainment</h2>
        </a>

        <!-- Linha Divisória -->
        <hr class="footer-divider" />

        <!-- Colunas de Informação -->
        <div class="footer-grid">
                
            <!-- Coluna 1: Localização e Horários (Igual a referência) -->
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

            <!-- Coluna 2: "Aba" de navegação-->
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

            <!-- Coluna 3: Informações do Projeto -->
            <div class="footer-col">
                <h3>Sobre o Projeto</h3>
                <ul>
                    <li><b>Autor:</b> Renato Lissoni de Almeida</li>
                    <li><b>Disciplina:</b> Sistemas Computacionais</li>
                    <li><b>Data:</b> 22 de setembro de 2026</li>
                    <li><b>Contato RH:</b> rl.almeida@unesp.br</li>
                </ul>
            </div>

            <!-- Coluna 4: Fontes -->
            <div class="footer-col">
                <h3>Fontes e Referências</h3>
                <ul>
                    <li><a href="https://store.steampowered.com/app/732690/FIVE_NIGHTS_AT_FREDDYS_HELP_WANTED/" target="_blank">FNAF no Steam</a></li>
                    <li><a href="https://freddy-fazbears-pizza.fandom.com/wiki/Freddy_Fazbear%27s_Pizza_Wiki" target="_blank">FNAF Wiki</a></li>
                    <li><a href="https://commons.wikimedia.org/" target="_blank">Wikimedia Commons</a></li>
                </ul>
            </div>
        </div>

        <!-- O aviso legal macabro lá no finalzinho -->
        <div class="footer-bottom">
            <p><small>&copy; 2026 Fazbear Entertainment. O desaparecimento de clientes dentro do shopping não é de nossa responsabilidade.</small></p>
        </div>
    </footer>
    <script src="assets/script.js"></script>
</body>
</html>