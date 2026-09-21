document.addEventListener("DOMContentLoaded", () => {
    const authForm = document.querySelector(".auth-form");
    if (!authForm) return;

    // Referências - Elementos de Cadastro
    const inputNome = document.getElementById("nome");
    const inputEmailCad = document.getElementById("email-cad");
    const inputSenhaCad = document.getElementById("senha-cad");
    const inputSenhaConf = document.getElementById("senha-conf");

    // Referências - Elementos de Login
    const inputEmailLogin = document.getElementById("email");
    const inputSenhaLogin = document.getElementById("senha");

    const btnSubmit = authForm.querySelector("button[type='submit']");

    // ========================================================
    // 1. RECURSO DE VISUALIZAR SENHA (ÍCONES SVG ALINHADOS)
    // ========================================================
    const svgOlhoAberto = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
            <circle cx="12" cy="12" r="3"></circle>
        </svg>
    `;

    const svgOlhoFechado = `
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
            <line x1="1" y1="1" x2="23" y2="23"></line>
        </svg>
    `;

    const camposSenha = document.querySelectorAll("input[type='password']");
    camposSenha.forEach((input) => {
        // Cria um wrapper exclusivo em volta do input para alinhar somente com a caixa de texto
        const wrapper = document.createElement("div");
        wrapper.className = "password-wrapper";
        wrapper.style.position = "relative";
        wrapper.style.display = "flex";
        wrapper.style.alignItems = "center";
        wrapper.style.width = "100%";

        // Move o input para dentro do wrapper
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        // Cria o botão com o ícone SVG
        const btnOlho = document.createElement("button");
        btnOlho.type = "button";
        btnOlho.innerHTML = svgOlhoAberto;
        btnOlho.title = "Mostrar/Ocultar senha";
        btnOlho.className = "btn-toggle-senha";
        btnOlho.style.cssText = `
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cor-dourada, #e6b800);
            opacity: 0.7;
            transition: opacity 0.2s ease, transform 0.2s ease;
            z-index: 5;
        `;

        btnOlho.addEventListener("mouseenter", () => {
            btnOlho.style.opacity = "1";
            btnOlho.style.transform = "scale(1.1)";
        });
        btnOlho.addEventListener("mouseleave", () => {
            btnOlho.style.opacity = "0.7";
            btnOlho.style.transform = "scale(1)";
        });

        // Alterna entre os dois ícones SVG
        btnOlho.addEventListener("click", () => {
            if (input.type === "password") {
                input.type = "text";
                btnOlho.innerHTML = svgOlhoFechado;
            } else {
                input.type = "password";
                btnOlho.innerHTML = svgOlhoAberto;
            }
        });

        input.style.paddingRight = "42px"; // Espaço interno para o texto não ficar embaixo do ícone
        wrapper.appendChild(btnOlho);
    });

    // ========================================================
    // 2. FUNÇÕES UTILITÁRIAS E VALIDAÇÃO DE SENHA FORTE
    // ========================================================
    function mostrarToast(mensagem) {
        const toast = document.createElement("div");
        toast.className = "toast-fazbear";
        toast.innerText = mensagem;
        document.body.appendChild(toast);
        
        setTimeout(() => toast.classList.add("visivel"), 100);
        setTimeout(() => {
            toast.classList.remove("visivel");
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }

    function exibirModalAuth(titulo, subtitulo, mensagem) {
        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";
        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear">
                <div class="modal-header-fazbear">
                    <h3>${titulo}</h3>
                    <p>${subtitulo}</p>
                </div>
                <div class="modal-body-fazbear">
                    <p class="codigo-destaque">${mensagem}</p>
                </div>
                <button id="fechar-modal-auth" class="btn-fechar-modal">Entendido</button>
            </div>
        `;
        document.body.appendChild(modalOverlay);
        document.getElementById("fechar-modal-auth").addEventListener("click", () => modalOverlay.remove());
    }

    // Regra Padrão: Mín. 8 caracteres, 1 maiúscula, 1 minúscula, 1 número e 1 caractere especial
    function validarSenhaForte(senha) {
        const regexForte = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_#\-])[A-Za-z\d@$!\%*?&_#\-]{8,}$/;
        return regexForte.test(senha);
    }

    // ========================================================
    // 3. EASTER EGGS GERAIS (E-MAILS E NOMES)
    // ========================================================
    function checarEasterEgg(valor) {
        if (!valor) return;
        const texto = valor.toLowerCase();
        
        if (texto.includes("william") || texto.includes("afton")) {
            mostrarToast("🟣 [ERRO DE SISTEMA]: Credenciais corrompidas. I ALWAYS COME BACK.");
        } else if (texto.includes("vanessa") || texto.includes("vanny")) {
            mostrarToast("🔦 [ACESSO NÍVEL 3]: Bem-vinda, Oficial Vanessa. Relatório de anomalias pendente.");
        } else if (texto.includes("michael") || texto.includes("mike")) {
            mostrarToast("🍕 'Você de novo? A gerência já perdeu a conta de quantas vezes você mudou de nome.'");
        } else if (texto.includes("gregory")) {
            mostrarToast("🤖 'Gregory, eu sinto que sua conta não tem permissão dos pais...'");
        } else if (texto.includes("fazbear.com")) {
            mostrarToast("💼 E-mail corporativo detectado. Redirecionando para servidor interno...");
        }
    }

    // ========================================================
    // 4. LÓGICA DA PÁGINA DE CADASTRO
    // ========================================================
    if (inputNome && inputEmailCad && inputSenhaCad && inputSenhaConf) {
        
        // Elemento para dica de senha forte
        const dicaSenha = document.createElement("small");
        dicaSenha.style.display = "block";
        dicaSenha.style.marginTop = "4px";
        dicaSenha.style.fontSize = "0.78rem";
        inputSenhaCad.parentNode.appendChild(dicaSenha);

        // Validação dinâmica de força da senha
        inputSenhaCad.addEventListener("input", () => {
            const senha = inputSenhaCad.value;
            if (senha === "") {
                dicaSenha.textContent = "";
                inputSenhaCad.style.borderColor = "";
            } else if (!validarSenhaForte(senha)) {
                dicaSenha.textContent = "⚠️ Mínimo 8 caracteres, com maiúscula, minúscula, número e símbolo (@$!%*?&).";
                dicaSenha.style.color = "#ff4d4d";
                inputSenhaCad.style.borderColor = "#ff4d4d";
            } else {
                dicaSenha.textContent = "✓ Senha forte aprovada!";
                dicaSenha.style.color = "#4caf50";
                inputSenhaCad.style.borderColor = "#4caf50";
            }
            validarCoincidenciaSenhas();
        });

        // Validação dinâmica de confirmação de senha
        function validarCoincidenciaSenhas() {
            if (inputSenhaConf.value === "") {
                inputSenhaConf.style.borderColor = "";
                return;
            }
            if (inputSenhaCad.value === inputSenhaConf.value) {
                inputSenhaConf.style.borderColor = "#4caf50";
            } else {
                inputSenhaConf.style.borderColor = "#ff4d4d";
            }
        }

        inputSenhaConf.addEventListener("input", validarCoincidenciaSenhas);

        // Dispara os easter eggs ao sair do campo
        inputNome.addEventListener("blur", (e) => checarEasterEgg(e.target.value));
        inputEmailCad.addEventListener("blur", (e) => checarEasterEgg(e.target.value));

        // Submissão do Cadastro
        authForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            // 1. Verificar Força da Senha
            if (!validarSenhaForte(inputSenhaCad.value)) {
                mostrarToast("⚠️ Sua senha deve ter no mínimo 8 caracteres, letra maiúscula, minúscula, número e símbolo!");
                inputSenhaCad.focus();
                return;
            }

            // 2. Verificar se as Senhas Coincidem
            if (inputSenhaCad.value !== inputSenhaConf.value) {
                mostrarToast("⚠️ Erro: As senhas digitadas não são iguais!");
                inputSenhaConf.focus();
                return;
            }

            const textoOriginal = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = "⏳ Criando sua conta...";

            try {
                await new Promise(resolve => setTimeout(resolve, 2000));
                exibirModalAuth(
                    "🎉 BEM-VINDO À FAMÍLIA!",
                    "Fazbear Entertainment - Novo ID",
                    "Sua conta foi criada com sucesso! Você já pode acessar o portal e reservar suas festas."
                );
                authForm.reset();
                dicaSenha.textContent = "";
                inputSenhaCad.style.borderColor = "";
                inputSenhaConf.style.borderColor = "";
            } catch (erro) {
                mostrarToast("Erro ao conectar com o banco de dados.");
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = textoOriginal;
            }
        });
    }

    // ========================================================
    // 5. LÓGICA DA PÁGINA DE LOGIN
    // ========================================================
    if (inputEmailLogin) {
        inputEmailLogin.addEventListener("blur", (e) => checarEasterEgg(e.target.value));

        authForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const textoOriginal = btnSubmit.innerHTML;
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = "⏳ Autenticando na Rede...";

            try {
                await new Promise(resolve => setTimeout(resolve, 1800));
                exibirModalAuth(
                    "🔓 ACESSO LIBERADO",
                    "Rede Corporativa Fazbear",
                    "Autenticação efetuada com sucesso. Bem-vindo de volta ao Mega Pizzaplex!"
                );
                authForm.reset();
            } catch (erro) {
                mostrarToast("Erro ao conectar com os servidores centrais.");
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = textoOriginal;
            }
        });
    }
});