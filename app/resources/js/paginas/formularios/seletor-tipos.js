let contador = 0;

const desenhos = {
    short_text: 'M4 8h16M4 13h9',
    long_text: 'M4 6h16M4 10h16M4 14h16M4 18h10',
    radio: 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0-5a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z',
    checkbox: 'M4 4h16v16H4ZM8 12l3 3 5-6',
    select: 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm-4-10 4 4 4-4',
    escala_linear: 'M3 12h18M4 9v6M12 9v6M20 9v6',
    classificacao: 'm12 3 2.8 5.7 6.3.9-4.6 4.5 1.1 6.3-5.6-3-5.6 3 1.1-6.3L3 9.6l6.2-.9Z',
    grade_multipla: 'M4 4h4v4H4Zm6 0h4v4h-4Zm6 0h4v4h-4ZM4 10h4v4H4Zm6 0h4v4h-4Zm6 0h4v4h-4ZM4 16h4v4H4Zm6 0h4v4h-4Zm6 0h4v4h-4Z',
    grade_caixas: 'M3 3h18v18H3ZM3 9h18M3 15h18M9 3v18M15 3v18',
    data: 'M4 5h16v16H4ZM4 10h16M8 3v4M16 3v4M8 14h3v3H8Z',
    horario: 'M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18ZM12 7v5l4 2',
};

function icone(tipo) {
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    for (const [nome, valor] of Object.entries({ width: '19', height: '19', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '1.6', 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'aria-hidden': 'true' })) svg.setAttribute(nome, valor);
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', desenhos[tipo] || desenhos.short_text);
    svg.append(path);
    return svg;
}

export function criarSeletorTipos(select) {
    const wrapper = document.createElement('span');
    wrapper.className = 'type-picker';
    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'type-picker-trigger';
    trigger.setAttribute('role', 'combobox');
    trigger.setAttribute('aria-label', 'Tipo de resposta');
    trigger.setAttribute('aria-haspopup', 'listbox');
    trigger.setAttribute('aria-expanded', 'false');
    const menu = document.createElement('span');
    menu.className = 'type-picker-menu';
    menu.id = `tipos-pergunta-${++contador}`;
    menu.setAttribute('role', 'listbox');
    menu.setAttribute('aria-label', 'Tipos de resposta');
    menu.hidden = true;
    trigger.setAttribute('aria-controls', menu.id);
    const itens = [];
    const opcoes = [...select.options];
    let ativo = 0;
    let busca = '';
    let tempoBusca;

    function fechar() {
        menu.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        trigger.removeAttribute('aria-activedescendant');
    }
    function destacar(indice) {
        ativo = (indice + itens.length) % itens.length;
        itens.forEach((item, index) => item.classList.toggle('is-active', index === ativo));
        trigger.setAttribute('aria-activedescendant', itens[ativo].id);
        itens[ativo].scrollIntoView({ block: 'nearest' });
    }
    function abrir() {
        document.querySelectorAll('.type-picker-trigger[aria-expanded="true"]').forEach((other) => {
            if (other !== trigger) other.dispatchEvent(new Event('fechar-seletor'));
        });
        menu.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        destacar(select.selectedIndex);
    }
    function atualizar() {
        const texto = document.createElement('span');
        texto.textContent = select.selectedOptions[0]?.textContent || '';
        const seta = document.createElement('span');
        seta.className = 'type-picker-chevron';
        seta.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>';
        trigger.replaceChildren(icone(select.value), texto, seta);
        itens.forEach((item, index) => item.setAttribute('aria-selected', String(index === select.selectedIndex)));
    }
    function selecionar(indice) {
        select.selectedIndex = indice;
        atualizar();
        fechar();
        select.dispatchEvent(new Event('change', { bubbles: true }));
        trigger.focus();
    }
    opcoes.forEach((opcao, index) => {
        const item = document.createElement('span');
        item.className = 'type-picker-option';
        if (['radio', 'escala_linear', 'grade_multipla', 'data'].includes(opcao.value)) item.classList.add('new-group');
        item.id = `${menu.id}-${index}`;
        item.setAttribute('role', 'option');
        const texto = document.createElement('span');
        texto.textContent = opcao.textContent;
        item.append(icone(opcao.value), texto);
        item.addEventListener('mousedown', (event) => event.preventDefault());
        item.addEventListener('click', (event) => { event.preventDefault(); selecionar(index); });
        item.addEventListener('pointermove', () => destacar(index));
        itens.push(item);
        menu.append(item);
    });
    trigger.addEventListener('click', () => menu.hidden ? abrir() : fechar());
    trigger.addEventListener('fechar-seletor', fechar);
    trigger.addEventListener('blur', fechar);
    trigger.addEventListener('keydown', (event) => {
        if (['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) {
            event.preventDefault();
            if (menu.hidden) abrir();
            else destacar(event.key === 'Home' ? 0 : event.key === 'End' ? itens.length - 1 : ativo + (event.key === 'ArrowDown' ? 1 : -1));
        } else if (['Enter', ' '].includes(event.key)) {
            event.preventDefault();
            if (menu.hidden) abrir(); else selecionar(ativo);
        } else if (event.key === 'Escape') {
            event.preventDefault();
            fechar();
        } else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey) {
            busca += event.key.toLocaleLowerCase('pt-BR');
            clearTimeout(tempoBusca);
            tempoBusca = setTimeout(() => { busca = ''; }, 600);
            if (menu.hidden) abrir();
            const encontrado = opcoes.findIndex((option) => option.text.toLocaleLowerCase('pt-BR').startsWith(busca));
            if (encontrado >= 0) destacar(encontrado);
        }
    });
    select.addEventListener('change', atualizar);
    select.hidden = true;
    wrapper.append(trigger, menu);
    select.after(wrapper);
    atualizar();
}
