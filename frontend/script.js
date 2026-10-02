// ========================================================
// CONTROLE DO HEADER (INTERSECTION OBSERVER + TRAVA DE TOPO)
// ========================================================
const header = document.querySelector("header");
const footer = document.querySelector("footer, .site-footer");

if (header && footer) {
    let footerEstaVisivel = false;

    // Função central para decidir se mostra ou esconde o header
    const atualizarHeader = () => {
        // 1. Verifica se a página realmente tem conteúdo longo o suficiente para precisar de rolagem
        const temRolagemSignificativa = document.documentElement.scrollHeight > window.innerHeight + 150;

        // 2. REGRA DE OURO: Se estiver nos primeiros 50px do topo OU a página for curta, o header FICA VISÍVEL.
        if (window.scrollY < 50 || !temRolagemSignificativa) {
            header.classList.remove("oculto");
        } 
        // 3. Só esconde se o footer estiver visível E o usuário estiver abaixo do topo
        else if (footerEstaVisivel) {
            header.classList.add("oculto");
        } else {
            header.classList.remove("oculto");
        }
    };

    // Observer eficiente para detectar se o footer entrou/saiu da tela
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            footerEstaVisivel = entry.isIntersecting;
            atualizarHeader();
        });
    }, { threshold: 0.1 });

    observer.observe(footer);

    // Escuta rolagem e redimensionamento para atualizar instantaneamente a posição
    window.addEventListener("scroll", atualizarHeader, { passive: true });
    window.addEventListener("resize", atualizarHeader, { passive: true });

    // Execução inicial
    atualizarHeader();
}
