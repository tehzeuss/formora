import { configurarPergunta } from './configuracoes';
import { criarTextoFormatado } from './texto-formatado';
import { criarImagem } from './imagens';
import { criarSeletorTipos } from './seletor-tipos';
import { iniciarPublicacao } from './publicacao';
import { mostrarMensagem } from './mensagens';

const editor = document.querySelector('[data-form-editor]');

if (editor) {
    const list = editor.querySelector('[data-question-list]');
    const template = editor.querySelector('[data-question-template]');
    const form = editor.querySelector('#form-builder');
    iniciarPublicacao();
    const status = editor.querySelector('[data-editor-status]');
    let dirty = false;
    const choiceTypes = ['radio', 'checkbox', 'select', 'grade_multipla', 'grade_caixas'];
    const markDirty = () => { dirty = true; };
    editor.querySelectorAll('[name="modo_acesso"], [name="tipo_identificador"], [name="data_encerramento"], [name="hora_encerramento"]').forEach((input) => {
        input.addEventListener('change', markDirty);
    });
    const configurarIdentificador = () => {
        const limitado = editor.querySelector('[name="modo_acesso"]:checked')?.value === 'aberto_limitado';
        const configuracao = editor.querySelector('[data-identifier-settings]');
        const seletor = editor.querySelector('[name="tipo_identificador"]');
        configuracao.hidden = !limitado;
        seletor.disabled = !limitado;
        seletor.required = limitado;
    };
    editor.querySelectorAll('[name="modo_acesso"]').forEach((input) => input.addEventListener('change', configurarIdentificador));
    configurarIdentificador();
    editor.querySelectorAll('[data-publication-action]').forEach((action) => {
        action.addEventListener('submit', (event) => {
            if (dirty) {
                event.preventDefault();
                mostrarMensagem('Você tem alterações não salvas', 'Salve as alterações do formulário antes de publicar ou encerrar o recebimento.');
            }
        });
    });
    const campoTitulo = editor.querySelector('#form-title');
    const textoInicial = JSON.parse(editor.querySelector('[data-initial-title]').textContent);
    const tituloFormatado = criarTextoFormatado(campoTitulo, textoInicial, 'titulo_formatado');
    const imagemFormulario = criarImagem(editor.querySelector('[data-form-image]'), editor.dataset.formImageUrl || '');
    imagemFormulario.input.name = 'imagem';
    imagemFormulario.remover.name = 'remover_imagem';
    imagemFormulario.remover.value = editor.dataset.removeFormImage === '1' ? '1' : '0';

    function renumber() {
        const cards = [...list.children];
        cards.forEach((card, index) => {
            card.querySelector('.question-number').textContent = `Pergunta ${index + 1}`;
            card.querySelector('[data-question-title]').name = `questions[${index}][title]`;
            card.querySelector('[data-question-type]').name = `questions[${index}][type]`;
            card.querySelector('[data-required-value]').name = `questions[${index}][required]`;
            const id = card.querySelector('[data-question-id]');
            if (id) id.name = `questions[${index}][id]`;
            const config = card.querySelector('[data-configuracao]');
            if (config) config.name = `questions[${index}][configuracao]`;
            card.querySelector('[data-titulo-formatado]').name = `questions[${index}][titulo_formatado]`;
            card.querySelector('[data-imagem]').name = `questions[${index}][imagem]`;
            card.querySelector('[data-remover-imagem]').name = `questions[${index}][remover_imagem]`;
            card.querySelectorAll('[data-option-input]').forEach((input) => {
                input.name = `questions[${index}][options][]`;
            });
            card.querySelector('[data-move-up]').disabled = index === 0;
            card.querySelector('[data-move-down]').disabled = index === cards.length - 1;
        });
        editor.querySelector('[data-add-question]').disabled = cards.length >= 100;
    }

    function addOption(card, value = '') {
        const options = card.querySelector('[data-options-list]');
        if (options.children.length >= 100) return;
        const row = document.createElement('div');
        row.className = 'option-row';
        const marker = document.createElement('span');
        marker.className = 'option-marker';
        marker.setAttribute('aria-hidden', 'true');
        const input = document.createElement('input');
        input.dataset.optionInput = '';
        input.value = value;
        input.maxLength = 255;
        input.placeholder = `Opção ${options.children.length + 1}`;
        input.setAttribute('aria-label', 'Texto da opção');
        const remove = document.createElement('button');
        remove.type = 'button';
        remove.textContent = '×';
        remove.setAttribute('aria-label', 'Excluir opção');
        remove.addEventListener('click', () => {
            row.remove();
            renumber();
            markDirty();
            card.querySelector('[data-add-option]').focus();
        });
        row.append(marker, input, remove);
        options.append(row);
        renumber();
        return input;
    }

    function applyType(card) {
        const type = card.querySelector('[data-question-type]').value;
        const choices = choiceTypes.includes(type);
        card.dataset.type = type;
        card.querySelector('[data-options-panel]').hidden = !choices;
        const hint = card.querySelector('[data-answer-hint]');
        hint.hidden = choices || ['escala_linear', 'classificacao'].includes(type);
        hint.textContent = type === 'data' ? 'Dia / mês / ano' : type === 'horario' ? 'Hora : minuto' : type === 'long_text'
            ? `Texto de resposta longa · até ${Number(editor.dataset.longLimit).toLocaleString('pt-BR')} caracteres`
            : `Texto de resposta curta · até ${editor.dataset.shortLimit} caracteres`;
        if (choices && card.querySelector('[data-options-list]').children.length === 0) {
            addOption(card, 'Opção 1');
            addOption(card, 'Opção 2');
        }
        card.querySelectorAll('[data-option-input]').forEach((input) => {
            input.disabled = !choices;
            input.required = choices;
        });
    }

    function readCard(card) {
        return {
            title: card.querySelector('[data-question-title]').value,
            type: card.querySelector('[data-question-type]').value,
            required: card.querySelector('[data-question-required]').checked,
            options: [...card.querySelectorAll('[data-option-input]')].map((input) => input.value),
            configuracao: JSON.parse(card.querySelector('[data-configuracao]').value || '{}'),
            titulo_formatado: card.querySelector('[data-titulo-formatado]').value,
        };
    }

    function addQuestion(data = {}, focus = false) {
        if (list.children.length >= 100) return;
        const card = template.content.firstElementChild.cloneNode(true);
        card.querySelector('[data-question-title]').value = data.title || '';
        card.querySelector('[data-question-type]').value = data.type || 'short_text';
        criarSeletorTipos(card.querySelector('[data-question-type]'));
        const required = card.querySelector('[data-question-required]');
        required.checked = data.required === true || data.required === 1 || data.required === '1';
        const requiredValue = document.createElement('input');
        requiredValue.type = 'hidden';
        requiredValue.dataset.requiredValue = '';
        requiredValue.value = required.checked ? '1' : '0';
        card.append(requiredValue);
        required.addEventListener('change', () => { requiredValue.value = required.checked ? '1' : '0'; });
        if (data.id) {
            const id = document.createElement('input');
            id.type = 'hidden';
            id.dataset.questionId = '';
            id.value = data.id;
            card.append(id);
        }
        list.append(card);
        const titulo = card.querySelector('[data-question-title]');
        const campoFormatado = criarTextoFormatado(titulo, data.titulo_formatado || '');
        const imagemPergunta = criarImagem(card.querySelector('[data-question-image]'), data.imagem_url || '');
        imagemPergunta.remover.value = data.remover_imagem === '1' || data.remover_imagem === 1 ? '1' : '0';
        card.querySelectorAll('[data-duplicate], [data-move-up], [data-move-down]').forEach((button) => {
            button.addEventListener('click', () => campoFormatado.area.focus());
        });
        const configurador = configurarPergunta(card, data.configuracao);
        (data.options || []).forEach((value) => addOption(card, value));
        applyType(card);
        card.querySelector('[data-question-type]').addEventListener('change', () => {
            configurador.atualizar();
            applyType(card);
            renumber();
        });
        card.querySelector('[data-add-option]').addEventListener('click', () => {
            const input = addOption(card);
            applyType(card);
            input?.focus();
            markDirty();
        });
        card.querySelector('[data-remove-question]').addEventListener('click', () => {
            card.remove();
            renumber();
            markDirty();
            status.textContent = 'Pergunta excluída.';
            editor.querySelector('[data-add-question]').focus();
        });
        card.querySelector('[data-duplicate]').addEventListener('click', () => {
            if (list.children.length >= 100) return;
            const copy = addQuestion(readCard(card));
            card.after(copy);
            renumber();
            markDirty();
            copy.querySelector('.rich-input').focus();
        });
        for (const [selector, direction] of [['[data-move-up]', -1], ['[data-move-down]', 1]]) {
            card.querySelector(selector).addEventListener('click', () => {
                const sibling = direction < 0 ? card.previousElementSibling : card.nextElementSibling;
                if (!sibling) return;
                if (direction < 0) sibling.before(card); else sibling.after(card);
                renumber();
                markDirty();
                status.textContent = `Pergunta movida para a posição ${[...list.children].indexOf(card) + 1}.`;
                card.querySelector('.rich-input').focus();
            });
        }
        renumber();
        if (focus) campoFormatado.area.focus();
        return card;
    }

    const initial = JSON.parse(editor.querySelector('[data-initial-questions]').textContent);
    (Array.isArray(initial) ? initial : Object.values(initial)).forEach((question) => addQuestion(question));
    editor.querySelector('[data-add-question]').addEventListener('click', () => {
        addQuestion({}, true);
        markDirty();
    });
    form.addEventListener('input', markDirty);
    form.addEventListener('change', markDirty);
    form.addEventListener('submit', (event) => {
        tituloFormatado.sincronizar();
        const vazio = [...list.children].find((card) => !card.querySelector('[data-question-title]').value.trim());
        if (!campoTitulo.value.trim() || vazio) {
            event.preventDefault();
            status.classList.remove('sr-only');
            status.textContent = 'Preencha o título do formulário e de todas as perguntas.';
            (!campoTitulo.value.trim() ? tituloFormatado.area : vazio.querySelector('.rich-input')).focus();
            return;
        }
        if (list.children.length === 0) {
            event.preventDefault();
            status.classList.remove('sr-only');
            status.textContent = 'Adicione pelo menos uma pergunta antes de salvar.';
            editor.querySelector('[data-add-question]').focus();
            return;
        }
        renumber();
        dirty = false;
    });
    window.addEventListener('beforeunload', (event) => {
        if (dirty) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
}
