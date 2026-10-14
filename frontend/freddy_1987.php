<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A Nova Geração da diversão! Explore a Grand Re-Opening de 1987 com os modelos Toy reluzentes e a tecnologia pioneira de reconhecimento facial.">
    <title>Freddy_Fazbear's Pizza (1987) | Fazbear Entertainment</title>
    <link rel="stylesheet" href="assets/geral.css">
    <link rel="stylesheet" href="assets/freddy_1987.css">
</head>

<body>
    <header>
        <a href="home.php" class="logo-link">
            <figure>
                <!-- Logo da empresa -->
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
            // Inicia a sessão no PHP se ainda não estiver aberta
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Oculta as abas "Reservar festa" e "Contratação" se o utilizador for um funcionário
            if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'funcionario') {
                echo '<a href="reserva.php" style="text-align: center; line-height: 1.2;">Reservar festa</a>';
                echo '<a href="vagas.php" style="text-align: center; line-height: 1.2;">Contratação</a>';
            }

            // Se houver um utilizador autenticado na sessão:
            if (isset($_SESSION['usuario_id'])) {
                
                if ($_SESSION['perfil'] === 'cliente') {
                    echo '<a href="minhas_reservas.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Minhas Reservas</a>';
                } elseif ($_SESSION['perfil'] === 'funcionario') {
                    echo '<a href="banco_horas.php" class="link-perfil" style="text-align: center; line-height: 1.2;">Banco de Horas</a>';
                }
                
                // Resgata o nome da sessão corretamente
                $nomeCompleto = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Visitante';
                $primeiroNome = explode(' ', trim($nomeCompleto))[0]; 

                // Renderiza o Nome e o Botão de Sair empilhados
                echo '<div class="user-logout-container" style="display: flex; flex-direction: column; align-items: center; gap: 4px; margin-left: 15px;">';
                echo '<span style="font-size: 0.85rem; color: var(--cor-dourada, #e6b800); font-weight: bold;">Olá, ' . htmlspecialchars($primeiroNome, ENT_QUOTES, 'UTF-8') . '</span>';
                
                // Criamos um estilo rápido e invisível só para esse botão conseguir ter o efeito :hover
                echo '<style>
                        .btn-sair-vermelho { background-color: #b5382a !important; color: #ffffff !important; }
                        .btn-sair-vermelho:hover { background-color: var(--cor-dourada, #e6b800) !important; color: #000000 !important; }
                    </style>';
                
                // Removemos a cor do 'style' e passamos a usar a nossa classe nova 'btn-sair-vermelho'
                echo '<a href="../backend/controllers/logoutController.php" class="btn-login btn-sair-vermelho" style="text-align: center; margin: 0; padding: 6px 16px; line-height: 1.2;">Sair</a>';
                echo '</div>';

            } else {
                echo '<a href="login.php" class="btn-login" style="text-align: center; line-height: 1.2;">Login</a>';
            }
            ?>
        </nav>
    </header>
    
    <main class="historico-fazbear-87">
        <h2>Freddy Fazbear's Pizza (1987)</h2>
        <p class="subtitulo">Onde a tecnologia de ponta e o design futurista redefiniram a diversão.</p>

        <article class="intro-texto">
            <p>Em 1987, a Fazbear Entertainment promoveu a sua inesquecível "Grand Re-Opening". Investindo pesado em um visual reluzente, salões gigantescos e uma frota inteiramente nova de mascotes, essa unidade foi projetada para ser o local mais seguro e moderno do planeta para as famílias.</p>
        </article>

        <h3>Os Modelos "Toy"</h3>
        <p class="texto-apoio">Para refletir os novos tempos, os velhos trajes foram armazenados e substituídos por versões amigáveis, feitas de plástico polido e acabamento impecável:</p>
        
        <div class="galeria-animatronics">
            <!-- Card Toy Freddy -->
            <div class="card-animatronic">
                <img src="imagens/Toy_freddy.webp" alt="Toy Freddy">
                <h4>Toy Freddy</h4>
                <p>A versão moderna do nosso querido líder. Com bochechas coradas e visual refinado, comandava os shows com elegância e simpatia.</p>
            </div>

            <!-- Card Toy Bonnie -->
            <div class="card-animatronic">
                <img src="imagens/Toy_bonnie.webp" alt="Toy Bonnie">
                <h4>Toy Bonnie</h4>
                <p>Com um acabamento em vinil azul brilhante e olhos expressivos, Toy Bonnie trazia solos de guitarra ainda mais dinâmicos ao palco.</p>
            </div>

            <!-- Card Toy Chica -->
            <div class="card-animatronic">
                <img src="imagens/Toy_chica.webp" alt="Toy Chica">
                <h4>Toy Chica</h4>
                <p>Sempre acompanhada do seu Cupcake reestilizado, ela era a estrela das festas de aniversário e da distribuição de doces no salão.</p>
            </div>

            <!-- Card Mangle / Toy Foxy -->
            <div class="card-animatronic">
                <img src="imagens/Mangle.webp" alt="The Mangle">
                <h4>The Mangle</h4>
                <p>Originalmente Toy Foxy, foi adaptado para a área <em>Kid's Cove</em> como uma atração interativa de "monte e desmonte", estimulando a criatividade dos pequenos.</p>
            </div>
        </div>

        <h3>Segurança Avançada e Reconhecimento Facial</h3>
        <article class="tecnologia-texto">
            <p>A grande inovação de 1987 foi a integração de sistemas de segurança governamentais diretamente no endoesqueleto dos animatrônicos. Cada modelo Toy contava com scanners de **Reconhecimento Facial** conectados a um banco de dados local.</p>
            
            <blockquote>
                <p>"Mobilidade total durante o dia e um sistema de proteção ativa projetado para manter qualquer ameaça longe das nossas crianças."</p>
            </blockquote>

            <p>Além da segurança, esses robôs possuíam articulações avançadas que os permitiam caminhar livremente pelo salão entre as mesas durante o dia, interagindo diretamente com o público e tirando fotos com os visitantes.</p>
        </article>

        <h3>Uma Edição Especial e Limitada</h3>
        <article class="fim-texto">
            <p>Apesar do sucesso estrondoso de público, a linha "Toy" teve uma vida curta. Os custos de manutenção da altíssima tecnologia de reconhecimento facial e pequenos bugs operacionais tornaram o modelo insustentável a longo prazo.</p>
            <p>Após poucas semanas de operação intensiva, a unidade de 1987 encerrou suas atividades como uma "edição especial de reabertura". A empresa optou por descartar os protótipos de plástico e guardar os modelos clássicos para a reestruturação da marca anos mais tarde.</p>
            <p class="nota-final">A era de 1987 provou a capacidade da Fazbear Entertainment de se reinventar e ousar na engenharia do futuro!</p>
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
