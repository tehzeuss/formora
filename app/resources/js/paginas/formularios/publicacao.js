export function iniciarPublicacao() {
    const button = document.querySelector('[data-copy-share]');
    const field = document.getElementById('share-link');
    const feedback = document.querySelector('[data-copy-feedback]');
    if (!button || !field) return;
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(field.value);
            feedback.textContent = 'Link copiado.';
        } catch {
            field.focus();
            field.select();
            feedback.textContent = 'Link selecionado. Use Ctrl+C ou a opção Copiar do navegador.';
        }
    });
}
