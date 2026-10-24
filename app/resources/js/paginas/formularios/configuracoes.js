export function configurarPergunta(card, inicial = {}) {
    const panel = document.createElement('div');
    panel.className = 'question-config';
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.dataset.configuracao = '';
    card.querySelector('.question-bottom').before(panel);
    card.append(hidden);
    let configuracao = inicial || {};

    function campo(titulo, chave, tipo, valor, opcoes = []) {
        const label = document.createElement('label');
        label.textContent = titulo;
        const input = document.createElement(tipo === 'select' ? 'select' : tipo === 'textarea' ? 'textarea' : 'input');
        if (tipo === 'select') {
            opcoes.forEach((value) => {
                const option = document.createElement('option');
                option.value = value;
                option.textContent = value;
                input.append(option);
            });
        } else if (tipo === 'textarea') {
            input.rows = 4;
            input.placeholder = 'Uma linha por item';
        } else {
            input.type = 'text';
            input.maxLength = 100;
        }
        input.value = valor;
        input.dataset.configField = chave;
        label.append(input);
        panel.append(label);
    }

    function ler() {
        const type = card.querySelector('[data-question-type]').value;
        const result = {};
        panel.querySelectorAll('[data-config-field]').forEach((input) => {
            const key = input.dataset.configField;
            result[key] = key === 'linhas' ? input.value.split(/\r?\n/).map((v) => v.trim()).filter(Boolean)
                : ['minimo', 'maximo'].includes(key) ? Number(input.value) : input.value;
        });
        hidden.value = JSON.stringify(result);
        hidden.disabled = !['escala_linear', 'classificacao', 'grade_multipla', 'grade_caixas'].includes(type);
        return result;
    }

    function atualizar() {
        const type = card.querySelector('[data-question-type]').value;
        panel.replaceChildren();
        if (type === 'escala_linear') {
            campo('Início da escala', 'minimo', 'select', configuracao.minimo ?? 1, [0, 1]);
            campo('Fim da escala', 'maximo', 'select', configuracao.maximo ?? 5, [2, 3, 4, 5, 6, 7, 8, 9, 10]);
            campo('Rótulo inicial (opcional)', 'rotulo_inicial', 'text', configuracao.rotulo_inicial ?? '');
            campo('Rótulo final (opcional)', 'rotulo_final', 'text', configuracao.rotulo_final ?? '');
        } else if (type === 'classificacao') {
            campo('Quantidade de estrelas', 'maximo', 'select', configuracao.maximo ?? 5, [2, 3, 4, 5, 6, 7, 8, 9, 10]);
        } else if (['grade_multipla', 'grade_caixas'].includes(type)) {
            campo('Linhas da grade', 'linhas', 'textarea', (configuracao.linhas ?? ['Linha 1', 'Linha 2']).join('\n'));
            const info = document.createElement('p');
            info.className = 'grid-editor-help';
            info.textContent = 'Defina as linhas acima e as colunas nas opções abaixo. Cada linha terá suas próprias respostas.';
            panel.append(info);
        }
        panel.hidden = panel.children.length === 0;
        ler();
    }

    panel.addEventListener('input', () => { configuracao = ler(); });
    panel.addEventListener('change', () => { configuracao = ler(); });
    atualizar();
    return { atualizar, ler };
}
