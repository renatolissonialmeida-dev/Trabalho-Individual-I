document.addEventListener("DOMContentLoaded", () => {
    const formVagas = document.querySelector("#formulario-vagas form");
    if (!formVagas) return;

    const inputNome = document.getElementById("nome");
    const inputTelVagas = document.getElementById("telefone");
    const selectCargo = document.getElementById("cargo");
    const inputExperiencia = document.getElementById("experiencia");
    const radioNoturno = document.getElementById("noturno");
    const btnSubmitVagas = formVagas.querySelector("button[type='submit']");

    // Máscara de Telefone
    if (inputTelVagas) {
        inputTelVagas.addEventListener("input", (e) => {
            let valor = e.target.value.replace(/\D/g, "");
            if (valor.length > 11) valor = valor.slice(0, 11);

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

    // Avisos dinâmicos por vaga
    const avisoCargo = document.createElement("small");
    avisoCargo.style.display = "block";
    avisoCargo.style.marginTop = "6px";
    avisoCargo.style.fontWeight = "bold";
    selectCargo.parentNode.appendChild(avisoCargo);

    const detalhesCargos = {
        creche: { texto: "☀️/🌙 Atenção: Mantenha as luzes acesas a todo custo para evitar ativação do modo noturno.", cor: "#f39c12" },
        seguranca: { texto: "🔦 Nota: Lanterna oficial fornecida. Baterias sobressalentes devem ser adquiridas por conta própria.", cor: "#e74c3c" },
        tecnico: { texto: "⚙️ Cuidado: Animatrônicos em manutenção podem se mover sozinhos durante a noite.", cor: "#e6b800" },
        faxineiro: { texto: "🧹 Obs: Kit de limpeza especial para manchas orgânicas profundas disponível no Depósito B.", cor: "#3498db" },
        cozinheiro: { texto: "🍕 Obs: Supervisão contínua exigida. Cuidado para não cair nos fornos automatizados.", cor: "#2ecc71" }
    };

    selectCargo.addEventListener("change", () => {
        const cargoSel = selectCargo.value;
        if (detalhesCargos[cargoSel]) {
            avisoCargo.textContent = detalhesCargos[cargoSel].texto;
            avisoCargo.style.color = detalhesCargos[cargoSel].cor;
        } else {
            avisoCargo.textContent = "";
        }
    });

    // Easter Eggs de Candidatos
    inputNome.addEventListener("blur", () => {
        const nome = inputNome.value.toLowerCase();
        if (nome.includes("michael") || nome.includes("mike")) {
            mostrarToast("🔧 'Bem-vindo de volta, Afton. Quantas vezes você pretende se candidatar?'");
        } else if (nome.includes("vanessa") || nome.includes("vanny")) {
            mostrarToast("🔦 'Credencial de Segurança Nível 3 identificada. Status: Sob vigilância.'");
        } else if (nome.includes("phone guy") || nome.includes("cara do telefone")) {
            mostrarToast("📞 'Hello, hello? Uh, I wanted to record a message for you to help you get settled...'");
        } else if (nome.includes("william") || nome.includes("afton") || nome.includes("purple guy")) {
            mostrarToast("🟣 [SISTEMA PHANTOM]: 'I ALWAYS COME BACK.' — Candidatura enviada para o RH Central.");
        }
    });

    inputExperiencia.addEventListener("blur", () => {
        const texto = inputExperiencia.value.toLowerCase();
        if (texto.includes("reporter") || texto.includes("jornalista") || texto.includes("investigacao")) {
            mostrarToast("⚠️ [ALERTA DE SEGURANÇA RH]: Candidato marcado como potencial risco de vazamento de mídia.");
        }
    });

    // ========================================================
    // 3. EVENTO ASSÍNCRONO DA CANDIDATURA (2 ETAPAS + PHP/BANCO)
    // ========================================================
    formVagas.addEventListener("submit", (e) => {
        e.preventDefault();

        const turnoSelecionado = radioNoturno.checked ? "Noturno (00:00 - 06:00)" : "Diurno (08:00 - 16:00)";

        // Adicionando a experiência que faltava e o email para exibição
        const dadosCandidato = {
            nome: inputNome.value,
            email: document.getElementById("email").value,
            telefone: inputTelVagas.value,
            cargo: selectCargo.options[selectCargo.selectedIndex].text,
            turno: turnoSelecionado,
            experiencia: inputExperiencia.value || "Nenhuma experiência prévia informada."
        };

        // Chama o modal de revisão (ETAPA 1)
        exibirModalRevisaoVaga(dadosCandidato);
    });

    // Modal de Revisão antes do Envio Real
    function exibirModalRevisaoVaga(dados) {
        const modalExistente = document.querySelector(".modal-overlay-fazbear");
        if (modalExistente) modalExistente.remove();

        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";

        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear">
                <div class="modal-header-fazbear">
                    <h3>📋 REVISÃO DE CANDIDATURA</h3>
                    <p>Fazbear Entertainment — Recursos Humanos</p>
                </div>
                <div class="modal-body-fazbear">
                    <p><strong>Candidato:</strong> ${dados.nome}</p>
                    <p><strong>E-mail:</strong> ${dados.email}</p>
                    <p><strong>Telefone:</strong> ${dados.telefone}</p>
                    <p><strong>Vaga Pretendida:</strong> ${dados.cargo}</p>
                    <p><strong>Turno:</strong> ${dados.turno}</p>
                    <p><strong>Experiência:</strong> ${dados.experiencia}</p>
                    <hr>
                    <p><small>Confira os dados antes de enviar sua ficha ao banco de dados do RH.</small></p>
                </div>
                <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
                    <button id="btn-voltar-vaga" style="background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Voltar / Corrigir</button>
                    <button id="btn-confirmar-vaga" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Confirmar e Enviar</button>
                </div>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        // Ação do Botão VOLTAR
        document.getElementById("btn-voltar-vaga").addEventListener("click", () => {
            modalOverlay.remove();
        });

        // Ação do Botão CONFIRMAR (ETAPA 2: Envio para o PHP/Banco)
        document.getElementById("btn-confirmar-vaga").addEventListener("click", async () => {
            const btnConfirmar = document.getElementById("btn-confirmar-vaga");
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = "⏳ Enviando ao RH...";

            // O laço de repetição que falamos antes, adicionando todos os campos automaticamente
            const formData = new FormData();
            for (let chave in dados) {
                formData.append(chave, dados[chave]);
            }

            try {
                // Requisição Real para a API PHP de Vagas
                const resposta = await fetch('../backend/controllers/vagasController.php', {
                    method: 'POST',
                    body: formData
                });

                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    modalOverlay.remove();
                    exibirModalCracha(resultado.matricula, dados);
                    formVagas.reset();
                    if (typeof avisoCargo !== 'undefined') avisoCargo.textContent = "";
                } else {
                    alert("Erro do Servidor: " + resultado.mensagem);
                    btnConfirmar.disabled = false;
                    btnConfirmar.innerHTML = "Confirmar e Enviar";
                }
            } catch (erro) {
                alert("Erro ao conectar com o sistema de recrutamento do Pizzaplex. O XAMPP está ligado?");
                console.error(erro);
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = "Confirmar e Enviar";
            }
        });
    }

    // Modal Final de Sucesso (Matrícula Gerada pelo Banco)
    function exibirModalCracha(matricula, candidato) {
        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";
        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear">
                <div class="modal-header-fazbear">
                    <h3>📋 CANDIDATURA REGISTRADA!</h3>
                    <p>Fazbear Entertainment — Recursos Humanos</p>
                </div>
                <div class="modal-body-fazbear">
                    <p class="codigo-destaque">Matrícula no Banco: <strong>${matricula}</strong></p>
                    <hr>
                    <p><strong>Candidato:</strong> ${candidato.nome}</p>
                    <p><strong>Vaga Pretendida:</strong> ${candidato.cargo}</p>
                    <p><strong>Turno Escolhido:</strong> ${candidato.turno}</p>
                    <hr>
                    <p class="aviso-rodape"><small>Seu termo de isenção de responsabilidade foi arquivado com sucesso no banco de dados. Apresente este número no setor de RH.</small></p>
                </div>
                <button id="fechar-modal-cracha" class="btn-fechar-modal" style="margin-top: 15px;">Entendido</button>
            </div>
        `;
        document.body.appendChild(modalOverlay);
        document.getElementById("fechar-modal-cracha").addEventListener("click", () => modalOverlay.remove());
    }
});
