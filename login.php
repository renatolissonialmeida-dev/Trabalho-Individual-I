<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Acesse sua conta corporativa ou pessoal e gerencie suas reservas, visualize benefícios exclusivos e conecte-se aos serviços do Mega Pizzaplex.">
    <title>Login | Fazbear Entertainment</title>
    <link rel="stylesheet" href="assets/geral.css">
    <link rel="stylesheet" href="assets/login_conta.css">
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
            <a href="home.php">Home</a>
            <a href="sobre.php">Sobre a empresa</a>
            
            <!-- Menu Dropdown -->
            <div class="dropdown">
                <button class="dropbtn">Franquias</button>
                <div class="dropdown-content">
                    <a href="family_dinner.php">Fredbear's Family Diner</a>
                    <a href="freddy_1985.php">Freddy Fazbear's Pizza (1983-1985)</a>
                    <a href="freddy_1987.php">Freddy Fazbear's Pizza (1987)</a>
                    <a href="freddy_1990.php">Freddy Fazbear's Pizza (anos 90)</a>
                </div>
            </div>

            <a href="novidade.php">O Mega Pizzaplex</a>
            <a href="reserva.php">Reservar festa</a>
            <a href="vagas.php">Contratação</a>

            <?php
            // Inicia a sessão no PHP se ainda não estiver aberta
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Se houver um usuário autenticado na sessão:
            if (isset($_SESSION['usuario_id'])) {
                // Exibe o link exclusivo do perfil
                if ($_SESSION['perfil'] === 'cliente') {
                    echo '<a href="minhas_reservas.php" class="link-perfil">Minhas Reservas</a>';
                } elseif ($_SESSION['perfil'] === 'funcionario') {
                    echo '<a href="banco_horas.php" class="link-perfil">Banco de Horas</a>';
                }
                
                // Exibe o botão para encerrar a sessão (Logout)
                echo '<a href="../backend/controllers/logoutController.php" class="btn-logout">Sair</a>';
            } else {
                // Se NÃO estiver logado, mantém o botão padrão de Login
                echo '<a href="login.php" class="btn-login">Login</a>';
            }
            ?>
        </nav>
    </header>

    <main class="auth-page">
        <section class="auth-container">
            <div class="auth-header">
                <h2>Portal de Acesso</h2>
                <p>Insira suas credenciais do Fazbear ID.</p>
            </div>
            
            <form class="auth-form" action="#" method="POST">
                <div class="input-group">
                    <label for="email">E-mail corporativo ou pessoal</label>
                    <input type="email" id="email" name="email" required placeholder="ex: usuario@fazbear.com">
                </div>
                
                <div class="input-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn-principal">Entrar no Sistema</button>
            </form>
            
            <div class="auth-footer">
                <p>Ainda não possui uma conta ? <a href="cadastro.php" class="link-destaque">Cadastrar nova conta</a></p>
            </div>
        </section>
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
    <script src="assets/login_conta.js?v=999"></script>
</body>
</html>