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

    // Evento Assíncrono da Candidatura
    formVagas.addEventListener("submit", async (e) => {
        e.preventDefault();

        const textoOriginalBtn = btnSubmitVagas.innerHTML;
        btnSubmitVagas.disabled = true;
        btnSubmitVagas.innerHTML = "⏳ Analisando ficha na base do RH...";

        const turnoSelecionado = radioNoturno.checked ? "Noturno (00:00 - 06:00)" : "Diurno (08:00 - 16:00)";

        const dadosCandidato = {
            nome: inputNome.value,
            email: document.getElementById("email").value,
            telefone: inputTelVagas.value,
            cargo: selectCargo.options[selectCargo.selectedIndex].text,
            turno: turnoSelecionado
        };

        try {
            const respostaRH = await new Promise((resolve) => {
                setTimeout(() => {
                    resolve({
                        sucesso: true,
                        matricula: "EMP-" + Math.floor(100000 + Math.random() * 900000),
                        candidato: dadosCandidato
                    });
                }, 2300);
            });

            if (respostaRH.sucesso) {
                exibirModalCracha(respostaRH);
                formVagas.reset();
                avisoCargo.textContent = "";
            }
        } catch (erro) {
            alert("Erro ao conectar com o sistema de recrutamento do Pizzaplex.");
        } finally {
            btnSubmitVagas.disabled = false;
            btnSubmitVagas.innerHTML = textoOriginalBtn;
        }
    });

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

    function exibirModalCracha(resultado) {
        const { matricula, candidato } = resultado;
        const modalOverlay = document.createElement("div");
        modalOverlay.className = "modal-overlay-fazbear";
        modalOverlay.innerHTML = `
            <div class="modal-card-fazbear">
                <div class="modal-header-fazbear">
                    <h3>📋 CANDIDATURA RECEBIDA!</h3>
                    <p>Fazbear Entertainment — Recursos Humanos</p>
                </div>
                <div class="modal-body-fazbear">
                    <p class="codigo-destaque">Matrícula Provisória: <strong>${matricula}</strong></p>
                    <hr>
                    <p><strong>Candidato:</strong> ${candidato.nome}</p>
                    <p><strong>Telefone:</strong> ${candidato.telefone}</p>
                    <p><strong>Vaga Pretendida:</strong> ${candidato.cargo}</p>
                    <p><strong>Turno Escolhido:</strong> ${candidato.turno}</p>
                    <hr>
                    <p class="aviso-rodape"><small>Seu termo de isenção de responsabilidade foi arquivado. Apresente este número de matrícula no setor de RH antes do seu primeiro turno.</small></p>
                </div>
                <button id="fechar-modal-cracha" class="btn-fechar-modal">Entendido</button>
            </div>
        `;
        document.body.appendChild(modalOverlay);
        document.getElementById("fechar-modal-cracha").addEventListener("click", () => modalOverlay.remove());
    }
});