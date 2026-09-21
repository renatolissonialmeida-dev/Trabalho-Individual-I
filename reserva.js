document.addEventListener("DOMContentLoaded", () => {
    // Referências aos elementos do formulário de reserva
    const formReserva = document.querySelector("#formulario-reserva form");
    if (!formReserva) return; // Só executa se o formulário existir na página

    const inputData = document.getElementById("data-festa");
    const selectPacote = document.getElementById("pacote-selecionado");
    const inputConvidados = document.getElementById("qtd-convidados");
    const selectHorario = document.getElementById("horario-festa");
    const inputAniversariante = document.getElementById("aniversariante");
    const inputTelefone = document.getElementById("telefone-reserva");
    const btnSubmit = formReserva.querySelector("button[type='submit']");
    const tabelaPacotes = document.querySelectorAll("#pacotes tbody tr");

    // ========================================================
    // 1. MÁSCARA E VALIDAÇÃO DE TELEFONE (APENAS NÚMEROS E MÁX 11 DÍGITOS)
    // ========================================================
    if (inputTelefone) {
        inputTelefone.addEventListener("input", (e) => {
            // Remove tudo que não for dígito
            let valor = e.target.value.replace(/\D/g, "");
            
            // Limita a 11 dígitos (DDD + 9 dígitos)
            if (valor.length > 11) {
                valor = valor.slice(0, 11);
            }

            // Aplica a formatação em tempo real: (XX) XXXXX-XXXX ou (XX) XXXX-XXXX
            if (valor.length > 10) {
                valor = valor.replace(/^(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
            } else if (valor.length > 6) {
                valor = valor.replace(/^(\d{2})(\d{4,5})/, "($1) $2-");
            } else if (valor.length > 2) {
                valor = valor.replace(/^(\d{2})/, "($1) ");
            } else if (valor.length > 0) {
                valor = valor.replace(/^(\d*)/, "($1");
            }

            e.target.value = valor;
        });
    }

    // ========================================================
    // 2. EFEITOS DE MANIPULAÇÃO DO DOM
    // ========================================================

    // Bloquear datas passadas no calendário
    const hoje = new Date().toISOString().split("T")[0];
    if (inputData) inputData.setAttribute("min", hoje);

    // Clicar na linha da tabela seleciona o pacote no formulário
    const pacotesMapeados = ["bronze", "gold", "vip"];
    tabelaPacotes.forEach((linha, index) => {
        linha.style.cursor = "pointer";
        linha.title = "Clique para selecionar este pacote";
        
        linha.addEventListener("click", () => {
            selectPacote.value = pacotesMapeados[index];
            selectPacote.dispatchEvent(new Event("change")); // Dispara a validação
            
            // Destaque visual na tabela
            tabelaPacotes.forEach(l => l.style.backgroundColor = "");
            linha.style.backgroundColor = "rgba(230, 180, 0, 0.2)";
            
            // Rola até o formulário
            selectPacote.scrollIntoView({ behavior: "smooth", block: "center" });
        });
    });

    // Limites de capacidade por pacote
    const limitesCapacidade = { bronze: 10, gold: 25, vip: 50 };

    // Elemento para exibir avisos de validação em tempo real
    const avisoCapacidade = document.createElement("small");
    avisoCapacidade.style.display = "block";
    avisoCapacidade.style.marginTop = "5px";
    avisoCapacidade.style.fontWeight = "bold";
    inputConvidados.parentNode.appendChild(avisoCapacidade);

    function validarCapacidade() {
        const pacote = selectPacote.value;
        const qtd = parseInt(inputConvidados.value) || 0;

        if (pacote && limitesCapacidade[pacote]) {
            const limiteMax = limitesCapacidade[pacote];
            if (qtd > limiteMax) {
                avisoCapacidade.textContent = `⚠️ Atenção: O ${selectPacote.options[selectPacote.selectedIndex].text} permite no máximo ${limiteMax} pessoas!`;
                avisoCapacidade.style.color = "#ff4d4d";
                inputConvidados.style.borderColor = "#ff4d4d";
                return false;
            } else if (qtd > 0) {
                avisoCapacidade.textContent = `✓ Capacidade dentro do limite do pacote (${qtd}/${limiteMax} convidados).`;
                avisoCapacidade.style.color = "#4caf50";
                inputConvidados.style.borderColor = "#4caf50";
                return true;
            }
        }
        avisoCapacidade.textContent = "";
        inputConvidados.style.borderColor = "";
        return true;
    }

    selectPacote.addEventListener("change", validarCapacidade);
    inputConvidados.addEventListener("input", validarCapacidade);

    // Aviso de Horário Noturno (20:00)
    const avisoHorario = document.createElement("small");
    avisoHorario.style.display = "block";
    avisoHorario.style.marginTop = "5px";
    avisoHorario.style.color = "#f39c12";
    selectHorario.parentNode.appendChild(avisoHorario);

    selectHorario.addEventListener("change", () => {
        if (selectHorario.value === "20:00") {
            avisoHorario.textContent = "🌙 Nota: As portas fecham imediatamente às 24:00. Protocolos noturnos serão ativados.";
        } else {
            avisoHorario.textContent = "";
        }
    });

    // Easter Egg com o nome do aniversariante
    inputAniversariante.addEventListener("blur", () => {
        const nome = inputAniversariante.value.toLowerCase();
        if (nome.includes("gregory")) {
            mostrarToast("🤖 Glamrock Freddy: 'Gregory? Você deveria estar na área de achados e perdidos...'");
        } else if (nome.includes("cassidy") || nome.includes("golden")) {
            mostrarToast("⚠️ [ALERTA DE SEGURANÇA]: Registro marcado com prioridade Nível A-1.");
        } else if (nome.includes("william") || nome.includes("afton") || nome.includes("purple guy")) {
            mostrarToast("🟣 [SISTEMA PHANTOM]: 'I ALWAYS COME BACK.' — Arquivo restrito aos fundadores.");
        }
    });

    // Notificação Flutuante (Toast)
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

    // ========================================================
    // 3. EVENTO ASSÍNCRONO (SIMULAÇÃO DE API / FETCH)
    // ========================================================

    formReserva.addEventListener("submit", async (e) => {
        e.preventDefault();

        if (!validarCapacidade()) {
            alert("Por favor, ajuste a quantidade de convidados para o pacote selecionado.");
            inputConvidados.focus();
            return;
        }

        // Alterar estado do botão para indicar processamento
        const textoOriginalBtn = btnSubmit.innerHTML;
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = "⏳ Conectando aos Servidores Fazbear...";

        // Coleta de dados do formulário
        const dadosReserva = {
            responsavel: document.getElementById("nome-responsavel").value,
            email: document.getElementById("email-reserva").value,
            telefone: inputTelefone.value,
            pacote: selectPacote.options[selectPacote.selectedIndex].text,
            data: inputData.value,
            horario: selectHorario.value,
            convidados: inputConvidados.value,
            aniversariante: inputAniversariante.value
        };

        try {
            // Requisição Assíncrona simulada
            const respostaServidor = await simularEnvioServidor(dadosReserva);

            if (respostaServidor.sucesso) {
                exibirModalComprovante(respostaServidor);
                formReserva.reset();
                avisoCapacidade.textContent = "";
            }
        } catch (erro) {
            alert("Erro na conexão com a central Fazbear. Tente novamente em instantes.");
            console.error(erro);
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = textoOriginalBtn;
        }
    });

    // Função que simula uma chamada de API (Promise / Async)
    function simularEnvioServidor(dados) {
        return new Promise((resolve) => {
            setTimeout(() => {
                const codigoGerado = "FAZ-" + Math.floor(100000 + Math.random() * 900000);
                resolve({
                    sucesso: true,
                    codigoReserva: codigoGerado,
                    dadosAgendados: dados,
                    mensagem: "Reserva confirmada no banco de dados do Mega Pizzaplex!"
                });
            }, 2200);
        });
    }

    // Modal de Confirmação Final
    function exibirModalComprovante(resultado) {
        const { codigoReserva, dadosAgendados } = resultado;

        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";

        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear">
                <div class="modal-header-fazbear">
                    <h3>🎉 RESERVA CONFIRMADA!</h3>
                    <p>Fazbear Entertainment Mega Pizzaplex</p>
                </div>
                <div class="modal-body-fazbear">
                    <p class="codigo-destaque">Código: <strong>${codigoReserva}</strong></p>
                    <hr>
                    <p><strong>Responsável:</strong> ${dadosAgendados.responsavel}</p>
                    <p><strong>Telefone:</strong> ${dadosAgendados.telefone}</p>
                    <p><strong>Aniversariante:</strong> ${dadosAgendados.aniversariante}</p>
                    <p><strong>Pacote:</strong> ${dadosAgendados.pacote}</p>
                    <p><strong>Data / Turno:</strong> ${dadosAgendados.data} às ${dadosAgendados.horario}</p>
                    <p><strong>Convidados:</strong> ${dadosAgendados.convidados} pessoas</p>
                    <hr>
                    <p class="aviso-rodape"><small>Um e-mail de confirmação foi enviado para ${dadosAgendados.email}. Por favor, apresente este código na recepção principal.</small></p>
                </div>
                <button id="fechar-modal-fazbear" class="btn-fechar-modal">Entendido</button>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        document.getElementById("fechar-modal-fazbear").addEventListener("click", () => {
            modalOverlay.remove();
        });
    }
});