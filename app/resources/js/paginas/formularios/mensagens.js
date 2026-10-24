export function mostrarMensagem(titulo, texto) {
    const editor = document.querySelector('[data-form-editor]');
    if (!editor) return;
    editor.querySelector('[data-editor-message]')?.remove();
    const aviso = document.createElement('div');
    aviso.className = 'editor-message';
    aviso.dataset.editorMessage = '';
    aviso.setAttribute('role', 'alert');
    aviso.tabIndex = -1;
    const conteudo = document.createElement('div');
    const cabecalho = document.createElement('strong');
    cabecalho.textContent = titulo;
    const descricao = document.createElement('p');
    descricao.textContent = texto;
    conteudo.append(cabecalho, descricao);
    const fechar = document.createElement('button');
    fechar.type = 'button';
    fechar.textContent = 'Entendi';
    fechar.addEventListener('click', () => {
        aviso.remove();
        editor.querySelector('#form-title')?.nextElementSibling?.querySelector('[contenteditable]')?.focus();
    });
    aviso.append(conteudo, fechar);
    editor.prepend(aviso);
    aviso.focus();
    aviso.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
}

export function solicitarLink() {
    return new Promise((resolver) => {
        const dialogo = document.createElement('dialog');
        dialogo.className = 'editor-link-dialog';
        dialogo.setAttribute('aria-labelledby', 'editor-link-title');
        dialogo.innerHTML = `
            <form novalidate>
                <h2 id="editor-link-title">Inserir link</h2>
                <p>Informe o endereço para o texto selecionado.</p>
                <label for="editor-link-url">Endereço do link</label>
                <input id="editor-link-url" type="url" placeholder="https://exemplo.com" autocomplete="url" aria-describedby="editor-link-error" required autofocus>
                <span id="editor-link-error" role="alert"></span>
                <div class="editor-link-actions">
                    <button type="button" data-cancel-link>Cancelar</button>
                    <button type="submit">Inserir link</button>
                </div>
            </form>`;
        const input = dialogo.querySelector('input');
        const erro = dialogo.querySelector('[role="alert"]');
        let resultado = null;
        dialogo.querySelector('[data-cancel-link]').addEventListener('click', () => dialogo.close());
        dialogo.querySelector('form').addEventListener('submit', (event) => {
            event.preventDefault();
            const valor = input.value.trim();
            if (!/^https?:\/\//i.test(valor) || !URL.canParse(valor)) {
                erro.textContent = 'Informe um endereço válido começando com http:// ou https://.';
                input.setAttribute('aria-invalid', 'true');
                input.focus();
                return;
            }
            resultado = valor;
            dialogo.close();
        });
        dialogo.addEventListener('close', () => {
            dialogo.remove();
            resolver(resultado);
        }, { once: true });
        document.body.append(dialogo);
        dialogo.showModal();
    });
}
