import { solicitarLink } from './mensagens';

function limparHtml(html) {
    const documento = new DOMParser().parseFromString(html || '', 'text/html');
    const fragmento = document.createDocumentFragment();
    function copiar(origem, destino) {
        origem.childNodes.forEach((node) => {
            if (node.nodeType === Node.TEXT_NODE) {
                destino.append(document.createTextNode(node.textContent));
                return;
            }
            if (node.nodeType !== Node.ELEMENT_NODE || ['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT'].includes(node.tagName)) return;
            if (['B', 'STRONG', 'I', 'EM', 'U', 'A'].includes(node.tagName)) {
                const elemento = document.createElement(node.tagName.toLowerCase());
                if (node.tagName === 'A') {
                    const url = node.getAttribute('href') || '';
                    if (!/^https?:\/\//i.test(url)) { copiar(node, destino); return; }
                    elemento.setAttribute('href', url);
                }
                copiar(node, elemento);
                destino.append(elemento);
            } else copiar(node, destino);
        });
    }
    copiar(documento.body, fragmento);
    return fragmento;
}

export function criarTextoFormatado(input, html = '', nomeHtml = '') {
    const caixa = document.createElement('div');
    caixa.className = 'rich-field';
    const area = document.createElement('div');
    area.className = 'rich-input';
    area.contentEditable = 'true';
    area.setAttribute('role', 'textbox');
    area.setAttribute('aria-label', input.id === 'form-title' ? 'Título do formulário' : 'Título da pergunta');
    area.setAttribute('aria-multiline', 'false');
    area.dataset.placeholder = input.placeholder;
    area.append(html ? limparHtml(html) : document.createTextNode(input.value));
    input.type = 'hidden';
    input.required = false;
    const valor = document.createElement('input');
    valor.type = 'hidden';
    valor.name = nomeHtml;
    valor.dataset.tituloFormatado = '';
    const toolbar = document.createElement('div');
    toolbar.className = 'rich-toolbar';
    toolbar.setAttribute('role', 'group');
    toolbar.setAttribute('aria-label', 'Formatação do texto');
    caixa.append(area, toolbar, valor);
    input.after(caixa);
    function sincronizar() {
        if (area.textContent.length > 255) area.textContent = area.textContent.slice(0, 255);
        input.value = area.textContent.trim();
        valor.value = area.innerHTML;
    }
    let selecaoSalva = null;
    function guardarSelecao() {
        const selecao = window.getSelection();
        if (selecao.rangeCount && area.contains(selecao.anchorNode) && area.contains(selecao.focusNode)) {
            selecaoSalva = selecao.getRangeAt(0).cloneRange();
        }
    }
    area.addEventListener('keyup', guardarSelecao);
    area.addEventListener('mouseup', guardarSelecao);
    area.addEventListener('input', () => { sincronizar(); guardarSelecao(); });
    area.addEventListener('keydown', (event) => { if (event.key === 'Enter') event.preventDefault(); });
    area.addEventListener('drop', (event) => event.preventDefault());
    area.addEventListener('paste', (event) => {
        event.preventDefault();
        const selecao = window.getSelection();
        if (!selecao.rangeCount) return;
        const range = selecao.getRangeAt(0);
        const texto = event.clipboardData.getData('text/plain').replace(/[\r\n]+/g, ' ');
        range.deleteContents();
        const node = document.createTextNode(texto.slice(0, 255 - area.textContent.length));
        range.insertNode(node);
        range.setStartAfter(node);
        range.collapse(true);
        selecao.removeAllRanges();
        selecao.addRange(range);
        sincronizar();
        area.dispatchEvent(new Event('input', { bubbles: true }));
    });
    const comandos = [['strong', 'B', 'Negrito'], ['em', 'I', 'Itálico'], ['u', 'U', 'Sublinhado'], ['a', 'link', 'Inserir link'], ['limpar', 'limpar', 'Limpar formatação']];
    comandos.forEach(([tag, texto, descricao]) => {
        const botao = document.createElement('button');
        botao.type = 'button';
        botao.title = descricao;
        botao.setAttribute('aria-label', descricao);
        if (tag === 'a' || tag === 'limpar') {
            botao.innerHTML = tag === 'a'
                ? '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-2 2M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l2-2"/></svg>'
                : '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 4h16M12 4 8 20M3 3l18 18"/></svg>';
        } else {
            const marca = document.createElement(tag);
            marca.textContent = texto;
            botao.append(marca);
        }
        botao.addEventListener('mousedown', (event) => event.preventDefault());
        botao.addEventListener('click', async () => {
            if (tag === 'limpar') {
                area.textContent = area.textContent;
                selecaoSalva = null;
            } else {
                if (!selecaoSalva || selecaoSalva.collapsed || !area.contains(selecaoSalva.commonAncestorContainer)) {
                    area.focus();
                    return;
                }
                const range = selecaoSalva;
                const elemento = document.createElement(tag);
                if (tag === 'a') {
                    const url = await solicitarLink();
                    if (url === null) return;
                    if (!area.isConnected || !area.contains(range.commonAncestorContainer)) return;
                    elemento.setAttribute('href', url);
                }
                elemento.append(range.extractContents());
                range.insertNode(elemento);
                range.selectNodeContents(elemento);
                const selecao = window.getSelection();
                selecao.removeAllRanges();
                selecao.addRange(range);
                area.focus();
            }
            sincronizar();
            area.dispatchEvent(new Event('input', { bubbles: true }));
        });
        toolbar.append(botao);
    });
    sincronizar();
    return { area, valor, sincronizar };
}
