// ==========================================
// EFEITO: ESCONDER HEADER AO CHEGAR NO FOOTER
// ==========================================

const header = document.querySelector('header');
const footer = document.querySelector('footer');

// Verifica se o header e o footer existem na página antes de executar
if (header && footer) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Footer apareceu: esconde o header
                header.classList.add('oculto');
            } else {
                // Footer sumiu: mostra o header
                header.classList.remove('oculto');
            }
        });
    });

    observer.observe(footer);
}