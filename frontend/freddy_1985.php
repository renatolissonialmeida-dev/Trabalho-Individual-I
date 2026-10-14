<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Conheça a Era de Ouro da Fazbear Entertainment! Descubra o nascimento da banda clássica com Freddy, Bonnie, Chica e Foxy e a revolução dos endoesqueletos em 1985.">
    <title>Freddy_Fazbear's Pizza (1985) | Fazbear Entertainment</title>
    <link rel="stylesheet" href="assets/geral.css">
    <link rel="stylesheet" href="assets/freddy_1985.css">
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
    
    <main class="historico-fazbear-85">
        <h2>Freddy Fazbear's Pizza (1985)</h2>
        <p class="subtitulo">Onde o quarteto clássico nasceu e conquistou gerações.</p>

        <article class="intro-texto">
            <p>Com o encerramento das atividades da Family Diner, a Fazbear Entertainment deu o seu maior salto comercial em 1985. A inauguração do primeiro grande restaurante da <strong>Freddy Fazbear's Pizza</strong> expandiu a nossa visão: saímos de uma modesta lanchonete para um verdadeiro templo do entretenimento, recheado de arcades, salas de festas temáticas e uma cozinha de alta produção.</p>
        </article>

        <h3>A Banda Principal</h3>
        <p class="texto-apoio">Pela primeira vez na história, aposentamos a dupla e apresentamos ao mundo o quarteto de mascotes que se tornaria o maior ícone da cultura pop infantil da época:</p>
        
        <div class="galeria-animatronics">
            <!-- Card Freddy -->
            <div class="card-animatronic">
                <img src="imagens/Freddy_85.webp" alt="Freddy Fazbear">
                <h4>Freddy Fazbear</h4>
                <p>O vocalista principal e líder da banda. Com sua cartola elegante e microfone, garantia que todas as festas de aniversário tivessem um parabéns inesquecível.</p>
            </div>

            <!-- Card Bonnie -->
            <div class="card-animatronic">
                <img src="imagens/Bonnie_85.webp" alt="Bonnie the Bunny">
                <h4>Bonnie</h4>
                <p>O guitarrista do grupo. Com seu tom roxo vibrante e solos energéticos no palco principal, era o favorito das crianças apaixonadas por música.</p>
            </div>

            <!-- Card Chica -->
            <div class="card-animatronic">
                <img src="imagens/Chica_85.webp" alt="Chica the Chicken">
                <h4>Chica</h4>
                <p>A embaixadora do sabor e backing vocal! Sempre acompanhada do seu fiel Cupcake, Chica trazia alegria e o amor incondicional por pizza.</p>
            </div>

            <!-- Card Foxy -->
            <div class="card-animatronic">
                <img src="imagens/Foxy_85.webp" alt="Foxy the Pirate">
                <h4>Foxy</h4>
                <p>O destemido raposo pirata residente da <em>Pirate Cove</em>. Contava histórias de aventura no alto-mar e encantava os pequenos exploradores.</p>
            </div>
        </div>

        <h3>A Revolução do Endoesqueleto Robótico</h3>
        <article class="tecnologia-texto">
            <p>Em 1985, a Fazbear Entertainment deu um passo definitivo para a segurança e modernização do nosso show. Os complexos trajes híbridos foram oficialmente descontinuados para dar lugar ao sistema de **Endoesqueletos Robóticos Dedicados (Série Endo-01)**.</p>
            
            <blockquote>
                <p>"Quatro personagens independentes operando via sincronização pneumática e circuitos de áudio integrados."</p>
            </blockquote>

            <p>Esses novos robôs eram 100% autônomos no palco. Movidos por componentes pneumáticos e programados com sequências de movimentos computadorizados, eles podiam se apresentar por horas sem a necessidade de operadores humanos dentro dos trajes, garantindo um show consistente e seguro para toda a família.</p>
        </article>

        <h3>Transição de Unidade</h3>
        <article class="fim-texto">
            <p>A unidade de 1985 foi o maior sucesso de público da nossa história inicial. No entanto, o volume de visitantes superou todas as expectativas operacionais do prédio original.</p>
            <p>Para atender às novas normas corporativas e reestruturar nosso modelo de negócios para expansões futuras, o prédio original de 1985 teve suas atividades encerradas anos depois, permitindo que a marca redirecionasse seus recursos para novos locais franqueados e modelos de entretenimento mais compactos.</p>
            <p class="nota-final">A magia de 1985 pavimentou o caminho para que a Fazbear Entertainment se tornasse o império global que você celebra hoje no Mega Pizzaplex!</p>
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
