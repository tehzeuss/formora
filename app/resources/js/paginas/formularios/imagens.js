import { mostrarMensagem } from './mensagens';

export function criarImagem(container, urlInicial = '') {
    const wrapper = document.createElement('div');
    wrapper.className = 'image-editor';
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/jpeg,image/png,image/webp';
    input.dataset.imagem = '';
    input.setAttribute('aria-label', 'Selecionar imagem');
    const remover = document.createElement('input');
    remover.type = 'hidden';
    remover.dataset.removerImagem = '';
    remover.value = '0';
    const imagem = document.createElement('img');
    imagem.alt = 'Prévia da imagem selecionada';
    imagem.hidden = !urlInicial;
    if (urlInicial) imagem.src = urlInicial;
    const label = document.createElement('label');
    label.className = 'image-upload-label';
    label.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8" cy="8" r="1.5"/><path d="m3 17 5-5 4 4 4-6 5 7"/></svg><span>Inserir ou trocar imagem</span>';
    label.append(input);
    const botao = document.createElement('button');
    botao.type = 'button';
    botao.className = 'remove-image';
    botao.textContent = 'Remover imagem';
    botao.hidden = !urlInicial;
    const dica = document.createElement('small');
    dica.textContent = 'JPG, PNG ou WebP · até 5 MB. Reenvie a imagem se o formulário apresentar erros ao salvar.';
    let blob = null;
    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) return;
        if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 5 * 1024 * 1024) {
            mostrarMensagem('Não foi possível selecionar a imagem', 'Escolha um arquivo JPG, PNG ou WebP de até 5 MB.');
            input.value = '';
            return;
        }
        if (blob) URL.revokeObjectURL(blob);
        blob = URL.createObjectURL(file);
        imagem.src = blob;
        imagem.hidden = false;
        botao.hidden = false;
        remover.value = '0';
    });
    botao.addEventListener('click', () => {
        input.value = '';
        imagem.removeAttribute('src');
        imagem.hidden = true;
        botao.hidden = true;
        remover.value = '1';
        if (blob) URL.revokeObjectURL(blob);
        remover.dispatchEvent(new Event('input', { bubbles: true }));
    });
    wrapper.append(imagem, label, botao, dica, remover);
    container.append(wrapper);
    return { input, remover, imagem };
}
