document.addEventListener("DOMContentLoaded", () => {
    const formVagas = document.getElementById("form-candidatura-fazbear");
    if (!formVagas) return;

    const inputNome = document.getElementById("nome");
    const inputTelVagas = document.getElementById("telefone");
    const selectCargo = document.getElementById("cargo");
    const inputExperiencia = document.getElementById("experiencia");
    const radioNoturno = document.getElementById("noturno");

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
        "Atendente da Creche": { texto: "☀️/🌙 Atenção: Mantenha as luzes acesas a todo custo para evitar ativação do modo noturno.", cor: "#f39c12" },
        "Guarda Noturno Chefe": { texto: "🔦 Nota: Lanterna oficial fornecida. Baterias sobressalentes devem ser adquiridas por conta própria.", cor: "#e74c3c" },
        "Técnico em Robótica": { texto: "⚙️ Cuidado: Animatrônicos em manutenção podem se mover sozinhos durante a noite.", cor: "#e6b800" },
        "Faxineiro": { texto: "🧹 Obs: Kit de limpeza especial para manchas orgânicas profundas disponível no Depósito B.", cor: "#3498db" },
        "Cozinheiro de Pizzas": { texto: "🍕 Obs: Supervisão contínua exigida. Cuidado para não cair nos fornos automatizados.", cor: "#2ecc71" }
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

    // Submissão com envio de CSRF Token
    formVagas.addEventListener("submit", (e) => {
        e.preventDefault();

        const turnoSelecionado = radioNoturno.checked ? "Noturno (00:00 - 06:00)" : "Diurno (08:00 - 16:00)";

        const dadosCandidato = {
            nome: inputNome.value,
            email: document.getElementById("email").value,
            telefone: inputTelVagas.value,
            cargo: selectCargo.value,
            turno: turnoSelecionado,
            experiencia: inputExperiencia.value || "Sem experiência prévia informada."
        };

        exibirModalRevisaoVaga(dadosCandidato);
    });

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
                    <hr style="border:0; border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 0;">
                    <p><small>Confira os dados antes de enviar sua ficha ao banco de dados do RH.</small></p>
                </div>
                <div style="display: flex; gap: 10px; justify-content: center; margin-top: 15px;">
                    <button id="btn-voltar-vaga" style="background-color: #6c757d; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Voltar / Corrigir</button>
                    <button id="btn-confirmar-vaga" style="background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Confirmar e Enviar</button>
                </div>
            </div>
        `;

        document.body.appendChild(modalOverlay);

        document.getElementById("btn-voltar-vaga").addEventListener("click", () => modalOverlay.remove());

        document.getElementById("btn-confirmar-vaga").addEventListener("click", async () => {
            const btnConfirmar = document.getElementById("btn-confirmar-vaga");
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = "⏳ Enviando ao RH...";

            const tokenCsrf = document.getElementById("csrf_token").value;
            const formData = new FormData();
            formData.append("csrf_token", tokenCsrf);

            for (let chave in dados) {
                formData.append(chave, dados[chave]);
            }

            try {
                const resposta = await fetch("../backend/controllers/vagasController.php", {
                    method: "POST",
                    credentials: "same-origin",
                    body: formData
                });

                const resultado = await resposta.json();

                if (resultado.sucesso) {
                    modalOverlay.remove();
                    exibirModalCracha(resultado.matricula, dados);
                    formVagas.reset();
                    if (typeof avisoCargo !== "undefined") avisoCargo.textContent = "";
                } else {
                    alert("Erro do Servidor: " + resultado.mensagem);
                    btnConfirmar.disabled = false;
                    btnConfirmar.innerHTML = "Confirmar e Enviar";
                }
            } catch (erro) {
                alert("Erro ao conectar com o sistema de recrutamento.");
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = "Confirmar e Enviar";
            }
        });
    }

    function exibirModalCracha(matricula, candidato) {
        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";
        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear" style="text-align: center;">
                <div class="modal-header-fazbear">
                    <h3>📋 CANDIDATURA REGISTRADA!</h3>
                    <p>Fazbear Entertainment — Recursos Humanos</p>
                </div>
                <div class="modal-body-fazbear">
                    <p style="font-size: 1.2rem; color: #d4af37; margin-bottom: 10px;">Matrícula no Banco: <strong>${matricula}</strong></p>
                    <hr style="border:0; border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 0;">
                    <p><strong>Candidato:</strong> ${candidato.nome}</p>
                    <p><strong>Vaga Pretendida:</strong> ${candidato.cargo}</p>
                    <p><strong>Turno Escolhido:</strong> ${candidato.turno}</p>
                    <hr style="border:0; border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 0;">
                    <p><small>Sua ficha foi enviada com sucesso para a diretoria. Guarde seu número de matrícula e aguarde o retorno do RH no seu e-mail.</small></p>
                </div>
                <button id="fechar-modal-cracha" style="margin-top: 15px; background-color: #9e1b1b; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold;">Voltar ao Início</button>
            </div>
        `;
        document.body.appendChild(modalOverlay);
        document.getElementById("fechar-modal-cracha").addEventListener("click", () => {
            modalOverlay.remove();
            window.location.href = "home.php";
        });
    }
});