document.addEventListener("DOMContentLoaded", () => {
    // Procura o formulário na página (seja de login ou cadastro)
    const authForm = document.querySelector("form");
    if (!authForm) return;

    const btnSubmit = authForm.querySelector("button[type='submit']") || authForm.querySelector("button");

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
        const wrapper = document.createElement("div");
        wrapper.className = "password-wrapper";
        wrapper.style.cssText = "position: relative; display: flex; align-items: center; width: 100%;";

        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        const btnOlho = document.createElement("button");
        btnOlho.type = "button";
        btnOlho.innerHTML = svgOlhoAberto;
        btnOlho.title = "Mostrar/Ocultar senha";
        btnOlho.style.cssText = `
            position: absolute; right: 12px; background: transparent; border: none;
            cursor: pointer; padding: 0; display: flex; align-items: center;
            justify-content: center; color: var(--cor-dourada, #e6b800); opacity: 0.7;
            transition: opacity 0.2s ease, transform 0.2s ease; z-index: 5;
        `;

        btnOlho.addEventListener("click", () => {
            if (input.type === "password") {
                input.type = "text";
                btnOlho.innerHTML = svgOlhoFechado;
            } else {
                input.type = "password";
                btnOlho.innerHTML = svgOlhoAberto;
            }
        });

        input.style.paddingRight = "42px";
        wrapper.appendChild(btnOlho);
    });

    // ========================================================
    // 2. FUNÇÕES UTILITÁRIAS E EASTER EGGS
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

    function validarSenhaForte(senha) {
        const regexForte = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_#\-])[A-Za-z\d@$!\%*?&_#\-]{8,}$/;
        return regexForte.test(senha);
    }

    function checarEasterEgg(valor) {
        if (!valor) return;
        const texto = valor.toLowerCase();
        
        if (texto.includes("william") || texto.includes("afton")) {
            mostrarToast("🟣 [ERRO DE SISTEMA]: Credenciais corrompidas. I ALWAYS COME BACK.");
        } else if (texto.includes("vanessa") || texto.includes("vanny")) {
            mostrarToast("🔦 [ACESSO NÍVEL 3]: Bem-vinda, Oficial Vanessa.");
        } else if (texto.includes("michael") || texto.includes("mike")) {
            mostrarToast("🍕 'Você de novo? A gerência já perdeu a conta de quantas vezes você mudou de nome.'");
        } else if (texto.includes("gregory")) {
            mostrarToast("🤖 'Gregory, eu sinto que sua conta não tem permissão dos pais...'");
        }
    }

    // Identifica quais campos existem na tela atual
    const inputEmailLogin = document.getElementById("email");
    const inputSenhaLogin = document.getElementById("senha");
    
    const inputNomeCad = document.getElementById("nome");
    const inputEmailCad = document.getElementById("email-cad");
    const inputSenhaCad = document.getElementById("senha-cad");
    const inputSenhaConf = document.getElementById("senha-conf");

    // Adiciona checagem de Easter Eggs nos campos que existirem
    if (inputEmailLogin) inputEmailLogin.addEventListener("blur", (e) => checarEasterEgg(e.target.value));
    if (inputNomeCad) inputNomeCad.addEventListener("blur", (e) => checarEasterEgg(e.target.value));
    if (inputEmailCad) inputEmailCad.addEventListener("blur", (e) => checarEasterEgg(e.target.value));

    // AQUI ESTÁ O CÓDIGO NOVO: Valida a senha assim que o usuário tira o mouse do campo
    if (inputSenhaCad) {
        inputSenhaCad.addEventListener("blur", (e) => {
            const senhaDigitada = e.target.value;
            if (senhaDigitada.length > 0 && !validarSenhaForte(senhaDigitada)) {
                mostrarToast("⚠️ A senha precisa ser forte (letras, números e símbolos).");
            }
        });
    }

    // ========================================================
    // 3. ENVIO DO FORMULÁRIO (LOGIN OU CADASTRO)
    // ========================================================
    authForm.addEventListener("submit", async (e) => {
        e.preventDefault();

        const textoOriginal = btnSubmit ? btnSubmit.innerHTML : "Enviando...";
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = "⏳ Processando...";
        }

        const formData = new FormData();

        // SE FOR A PÁGINA DE CADASTRO
        if (inputNomeCad) {
            if (!validarSenhaForte(inputSenhaCad.value)) {
                mostrarToast("⚠️ A senha precisa ser forte (letras, números e símbolos).");
                if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.innerHTML = textoOriginal; }
                return;
            }
            if (inputSenhaCad.value !== inputSenhaConf.value) {
                mostrarToast("⚠️ As senhas não coincidem!");
                if (btnSubmit) { btnSubmit.disabled = false; btnSubmit.innerHTML = textoOriginal; }
                return;
            }
            formData.append("acao", "cadastrar");
            formData.append("nome", inputNomeCad.value);
            formData.append("email", inputEmailCad.value);
            formData.append("senha", inputSenhaCad.value);
        } 
        // SE FOR A PÁGINA DE LOGIN
        else if (inputEmailLogin) {
            formData.append("acao", "login");
            formData.append("email", inputEmailLogin.value);
            formData.append("senha", inputSenhaLogin.value);
        }

        try {
            const resposta = await fetch("../backend/controllers/authController.php", {
                method: "POST",
                body: formData
            });
            const resultado = await resposta.json();

            if (resultado.sucesso) {
                // Vai direto para a home em ambos os casos
                window.location.href = "home.php";
            } else {
                mostrarToast("⚠️ " + resultado.mensagem);
            }
        } catch (erro) {
            mostrarToast("Erro ao conectar com os servidores centrais.");
            console.error(erro);
        } finally {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = textoOriginal;
            }
        }
    });
});
