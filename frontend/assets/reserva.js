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
    // 3. EVENTO ASSÍNCRONO (CONFIRMAÇÃO EM 2 ETAPAS + PHP/BANCO)
    // ========================================================

    formReserva.addEventListener("submit", (e) => {
        e.preventDefault();

        // 1. Validação local dos campos
        if (!validarCapacidade()) {
            alert("Por favor, ajuste a quantidade de convidados para o pacote selecionado.");
            inputConvidados.focus();
            return;
        }

        // 2. Coleta os dados preenchidos
        const dadosReserva = {
            csrf_token: document.getElementById("csrf_token") ? document.getElementById("csrf_token").value : '',
            responsavel: document.getElementById("nome-responsavel").value,
            email: document.getElementById("email-reserva").value,
            telefone: inputTelefone.value,
            pacote: selectPacote.options[selectPacote.selectedIndex].text,
            data: inputData.value,
            horario: selectHorario.value,
            convidados: inputConvidados.value,
            aniversariante: inputAniversariante.value,
            observacoes: document.getElementById("observacoes").value || "Nenhuma restrição informada."
        };

        // 3. Abre o Modal de Confirmação (ETAPA 1: Revisão)
        exibirModalRevisao(dadosReserva);
    });

    // Modal de Revisão antes do Envio Real
    function exibirModalRevisao(dados) {
        // Remove modal anterior caso exista
        const modalExistente = document.querySelector(".modal-overlay-fazbear");
        if (modalExistente) modalExistente.remove();

        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";

        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear">
                <div class="modal-header-fazbear">
                    <h3>📋 CONFIRME SEUS DADOS</h3>
                    <p>Fazbear Entertainment — Revisão de Reserva</p>
                </div>
                <div class="modal-body-fazbear">
                    <p><strong>Responsável:</strong> ${dados.responsavel}</p>
                    <p><strong>E-mail:</strong> ${dados.email}</p>
                    <p><strong>Telefone:</strong> ${dados.telefone}</p>
                    <p><strong>Aniversariante:</strong> ${dados.aniversariante}</p>
                    <p><strong>Pacote:</strong> ${dados.pacote}</p>
                    <p><strong>Data / Horário:</strong> ${dados.data} às ${dados.horario}</p>
                    <p><strong>Convidados:</strong> ${dados.convidados} pessoas</p>
                    <p><strong>Restrições/Alergias:</strong> ${dados.observacoes}</p>
                    <hr>
                    <p><small>Confira as informações antes de registrar no banco de dados.</small></p>
                </div>
                <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
                    <button id="btn-voltar-reserva" style="background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Voltar / Corrigir</button>
                    <button id="btn-confirmar-reserva" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Confirmar e Enviar</button>
                </div>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        // Ação do Botão VOLTAR
        document.getElementById("btn-voltar-reserva").addEventListener("click", () => {
            modalOverlay.remove();
        });

        // Ação do Botão CONFIRMAR (ETAPA 2: Envio para o PHP/Banco)
        document.getElementById("btn-confirmar-reserva").addEventListener("click", async () => {
            const btnConfirmar = document.getElementById("btn-confirmar-reserva");
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = "⏳ Salvando...";

            const formData = new FormData();

            // Captura o token diretamente do campo HTML no momento do envio
            const tokenInput = document.getElementById("csrf_token");
            formData.append("csrf_token", tokenInput ? tokenInput.value : "");

            // Envia os demais campos para o reservaController.php
            formData.append("responsavel", dados.responsavel);
            formData.append("email", dados.email);
            formData.append("telefone", dados.telefone);
            formData.append("pacote", dados.pacote);
            formData.append("data", dados.data);
            formData.append("horario", dados.horario);
            formData.append("convidados", dados.convidados);
            formData.append("aniversariante", dados.aniversariante);
            formData.append("observacoes", dados.observacoes);

            try {
                const resposta = await fetch('../backend/controllers/reservaController.php', {
                    method: 'POST',
                    credentials: 'same-origin', // Garante o envio do cookie PHPSESSID
                    body: formData
                });

                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    modalOverlay.remove();
                    exibirModalSucesso(resultado.codigoReserva, dados);
                    formReserva.reset();
                    if (typeof avisoCapacidade !== 'undefined' && avisoCapacidade) avisoCapacidade.textContent = "";
                } else {
                    alert("Erro do Servidor: " + resultado.mensagem);
                    btnConfirmar.disabled = false;
                    btnConfirmar.innerHTML = "Confirmar e Enviar";
                }
            } catch (erro) {
                alert("Erro na conexão com o servidor. Verifique se o XAMPP está ativo.");
                console.error(erro);
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = "Confirmar e Enviar";
            }
        });
    }

    // Modal Final de Sucesso (Comprovante Gerado pelo Banco)
    function exibirModalSucesso(codigoReserva, dados) {
        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";

        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear">
                <div class="modal-header-fazbear">
                    <h3>🎉 RESERVA REGISTRADA!</h3>
                    <p>Fazbear Entertainment Mega Pizzaplex</p>
                </div>
                <div class="modal-body-fazbear">
                    <p class="codigo-destaque">Código no Banco: <strong>${codigoReserva}</strong></p>
                    <hr>
                    <p><strong>Responsável:</strong> ${dados.responsavel}</p>
                    <p><strong>Data / Turno:</strong> ${dados.data} às ${dados.horario}</p>
                    <p><strong>Pacote:</strong> ${dados.pacote}</p>
                    <hr>
                    <p class="aviso-rodape"><small>Sua reserva foi salva com sucesso no banco de dados.</small></p>
                </div>
                <button id="fechar-modal-sucesso" class="btn-fechar-modal" style="margin-top: 15px;">Entendido</button>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        document.getElementById("fechar-modal-sucesso").addEventListener("click", () => {
            modalOverlay.remove();
            window.location.href = "minhas_reservas.php"; // Redireciona o cliente para visualizar a reserva listada
        });
    }
});