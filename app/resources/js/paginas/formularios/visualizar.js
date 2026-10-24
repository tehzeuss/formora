document.querySelectorAll('[data-character-limit]').forEach((input) => {
    const counter = document.getElementById(input.getAttribute('aria-describedby'));
    if (!counter) return;
    const update = () => {
        counter.querySelector('[data-character-count]').textContent = input.value.length.toLocaleString('pt-BR');
        counter.classList.toggle('at-limit', input.value.length >= input.maxLength);
    };
    input.addEventListener('input', update);
    update();
});
