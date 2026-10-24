import './bootstrap';

document.querySelectorAll('[data-password-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.getAttribute('aria-controls'));
        if (!input) return;
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(visible));
        button.setAttribute('aria-label', visible ? 'Ocultar senha' : 'Mostrar senha');
    });
});

document.querySelectorAll('[data-user-menu]').forEach((menu) => {
    menu.addEventListener('pointerenter', (event) => {
        if (event.pointerType === 'mouse') menu.open = true;
    });
    menu.addEventListener('pointerleave', (event) => {
        if (event.pointerType === 'mouse' && !menu.contains(document.activeElement)) menu.open = false;
    });
    menu.addEventListener('focusout', (event) => {
        if (!menu.contains(event.relatedTarget)) menu.open = false;
    });
    menu.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            menu.querySelector('summary').focus();
            menu.open = false;
        }
    });
    document.addEventListener('click', (event) => {
        if (!menu.contains(event.target)) menu.open = false;
    });
});
