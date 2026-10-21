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

// 1. Se NÃO estiver logado, redireciona imediatamente para a tela de login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// 2. Trava de perfil: Apenas clientes podem fazer reservas de festas
$perfilAtual = $_SESSION['perfil'] ?? '';
if ($perfilAtual !== 'cliente') {
    header("Location: home.php");
    exit;
}

// Gera o token CSRF se ainda não existir
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width" />
    <meta name="description" content="Reserve sua festa de aniversário no Freddy Fazbear's Mega Pizzaplex. Pacotes VIP, salões temáticos e agendamento online simplificado.">
    <title>Reserva de festas | Fazbear Entertainment</title>
    <link rel="stylesheet" href="assets/geral.css"/>
    <link rel="stylesheet" href="assets/reserva.css">
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
      <!-- SEÇÃO 1: APRESENTAÇÃO DOS PACOTES DE FESTA -->
      <section id="pacotes">
        <h2>Reserve sua Festa no Mega Pizzaplex</h2>
        <p>Comemore o aniversário do seu filho com os Glamrocks! Selecione o pacote ideal e garanta uma experiência inesquecível (e corporativamente supervisionada).</p>

        <br />

        <table border="1" cellpadding="10">
          <caption>Tabela de Pacotes de Festas VIP</caption>
          <thead>
            <tr>
              <th>Pacote</th>
              <th>Atrações Incluídas</th>
              <th>Bônus Especial</th>
              <th>Capacidade</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><b>Pacote Bronze (Classic)</b></td>
              <td>Acesso ao Salão Principal + 1 Hora de Boliche no <i>Bonnie Bowl</i>.</td>
              <td>50 Faz-Tokens por criança.</td>
              <td>Até 10 pessoas</td>
            </tr>
            <tr>
              <td><b>Pacote Glamrock Gold</b></td>
              <td>Acesso Livre ao <i>Fazer Blast</i> + Passagem pela Creche (Daycare).</td>
              <td>Visita rápida do Glamrock Freddy na mesa do bolo.</td>
              <td>Até 25 pessoas</td>
            </tr>
            <tr>
              <td><b>Pacote VIP Superstar</b></td>
              <td>Acesso ilimitado a todas as atrações (incluindo <i>Roxy Raceway</i>).</td>
              <td>Camarote VIP exclusivo e presença garantida de toda a banda Glamrock.</td>
              <td>Até 50 pessoas</td>
            </tr>
          </tbody>
        </table>
      </section>

      <hr />

      <!-- SEÇÃO 2: FORMULÁRIO DE RESERVA (AÇÃO EM 3 CLIQUES) -->
      <section id="formulario-reserva">
        <h3>Formulário de Agendamento Express</h3>
        <p>Preencha os dados abaixo para reservar sua data. O processo leva menos de um minuto!</p>

        <form action="/painel/reservas" method="POST">

          <!-- TOKEN CSRF INVISÍVEL PARA SEGURANÇA -->
          <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
          
          <!-- Passo 1: Informações do Responsável -->
          <fieldset>
            <legend><b>1. Dados do Responsável</b></legend>
            
            <p>
              <label for="nome-responsavel">Nome do Responsável:</label><br />
              <input type="text" id="nome-responsavel" name="nome_responsavel" placeholder="Digite seu nome completo" required />
            </p>

            <p>
              <label for="email-reserva">E-mail para Confirmação:</label><br />
              <input type="email" id="email-reserva" name="email_reserva" placeholder="seuemail@exemplo.com" required />
            </p>

            <p>
              <label for="telefone-reserva">Telefone / WhatsApp:</label><br />
              <input type="tel" id="telefone-reserva" name="telefone_reserva" placeholder="(00) 90000-0000" required />
            </p>
          </fieldset>

          <br />

          <!-- Passo 2: Detalhes do Evento -->
          <fieldset>
            <legend><b>2. Detalhes da Festa</b></legend>

            <p>
              <label for="pacote-selecionado">Escolha o Pacote Desejado:</label><br />
              <select id="pacote-selecionado" name="pacote" required>
                <option value="" disabled selected>Selecione um Pacote</option>
                <option value="bronze">Pacote Bronze (Classic)</option>
                <option value="gold">Pacote Glamrock Gold</option>
                <option value="vip">Pacote VIP Superstar</option>
              </select>
            </p>

            <p>
              <label for="data-festa">Data do Evento:</label><br />
              <input type="date" id="data-festa" name="data_festa" required />
            </p>

            <p>
              <label for="horario-festa">Horário do Evento:</label><br />
              <select id="horario-festa" name="horario_festa" required>
                <option value="" disabled selected>Selecione um horário</option>
                <option value="12:00">12:00 às 15:00 (Turno Almoço)</option>
                <option value="16:00">16:00 às 19:00 (Turno Tarde)</option>
                <option value="20:00">20:00 às 23:00 (Turno Noturno - Encerramento às 24:00)</option>
              </select>
            </p>

            <p>
              <label for="qtd-convidados">Quantidade Estimada de Convidados:</label><br />
              <input type="number" id="qtd-convidados" name="qtd_convidados" min="1" max="50" placeholder="Ex: 15" required />
            </p>

            <p>
              <label for="aniversariante">Nome e Idade do Aniversariante:</label><br />
              <input type="text" id="aniversariante" name="aniversariante" placeholder="Ex: Gregory (10 anos)" required />
            </p>
          </fieldset>

          <br />

          <!-- Passo 3: Confirmação e Termos -->
          <fieldset>
            <legend><b>3. Finalização da Reserva</b></legend>

            <p>
              <label for="observacoes">Observações ou Restrições Alimentares (Ex: alergia a amendoim):</label><br />
              <textarea id="observacoes" name="observacoes" rows="3" cols="40" placeholder="Digite aqui caso haja alguma necessidade especial..."></textarea>
            </p>

            <p>
              <input type="checkbox" id="termo-festa" name="termo_festa" required />
              <label for="termo-festa">Estou ciente de que todas as crianças devem estar acompanhadas e autorizo o envio de e-mails promocionais da Fazbear Entertainment.</label>
            </p>

            <p>
              <button type="submit">Confirmar e Garantir Reserva</button>
            </p>
          </fieldset>

        </form>
      </section>

      <hr />

      <!-- SEÇÃO 3: PERGUNTAS FREQUENTES DA RESERVA -->
      <section id="faq-reserva">
        <h3>Dúvidas Frequentes sobre Eventos</h3>

        <dl>
          <dt><strong>O que acontece se o evento ultrapassar as 24:00?</strong></dt>
          <dd>Por normas rígidas do estabelecimento, as portas do shopping fecham pontualmente à meia-noite e as luzes principais são desligadas. Todos os convidados devem deixar o prédio antes deste horário.</dd>

          <dt><strong>Podemos levar nosso próprio bolo?</strong></dt>
          <dd>Não. Todos os bolos, pizzas e refrigerantes devem ser fornecidos exclusivamente pelos serviços de buffet internos do Pizzaplex por motivos de padronização sanitária.</dd>

          <dt><strong>Os animatrônicos tiram fotos individuais com as crianças?</strong></dt>
          <dd>Sim! Nos pacotes Gold e VIP Superstar, há uma sessão de fotos de 15 minutos com os personagens disponíveis no momento.</dd>
        </dl>
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
    <script src="assets/reserva.js"></script>
</body>
</html>