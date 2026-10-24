const formulario = document.querySelector('[data-answer-form]');

if (formulario) {
    formulario.querySelectorAll('[data-required-choice]').forEach((grupo) => {
        const caixas = [...grupo.querySelectorAll('input[type="checkbox"]')];
        const atualizar = () => {
            caixas[0]?.setCustomValidity(caixas.some((caixa) => caixa.checked)
                ? '' : 'Selecione pelo menos uma opção.');
        };
        caixas.forEach((caixa) => caixa.addEventListener('change', atualizar));
        atualizar();
    });
    let enviando = false;
    formulario.addEventListener('submit', (event) => {
        if (enviando) {
            event.preventDefault();
            return;
        }
        enviando = true;
        const botao = formulario.querySelector('[data-submit-answers]');
        botao.disabled = true;
        botao.textContent = 'Enviando…';
    });
    window.addEventListener('pageshow', () => {
        enviando = false;
        const botao = formulario.querySelector('[data-submit-answers]');
        botao.disabled = false;
        botao.textContent = 'Enviar respostas';
    });
    document.querySelector('[data-submission-errors]')?.focus();
}
