@php
    $modoAcesso = old('modo_acesso', $form->modo_acesso ?? 'aberto_livre');
@endphp
<section class="publication-access" aria-labelledby="access-title" data-access-settings>
    <h2 id="access-title">Acesso e limite de respostas</h2>
    <p>Escolha quem pode responder e como controlar os envios. Salve as alterações para aplicar.</p>
    <label>
        <input type="radio" name="modo_acesso" form="form-builder" value="fechado" @checked($modoAcesso === 'fechado') required>
        <span><strong>Fechado · um envio por login</strong><small>Exige login e cadastro local ativo. Cada usuário pode responder uma única vez.</small></span>
    </label>
    <label>
        <input type="radio" name="modo_acesso" form="form-builder" value="aberto_limitado" @checked($modoAcesso === 'aberto_limitado') required>
        <span><strong>Aberto com limite · um envio por CPF ou e-mail</strong><small>Não exige login. Solicita o identificador escolhido abaixo e aceita uma resposta por identificador.</small></span>
    </label>
    <div class="access-identifier-settings" data-identifier-settings>
        <label for="tipo-identificador">Identificador para limitar os envios</label>
        <select id="tipo-identificador" name="tipo_identificador" form="form-builder">
            <option value="email" @selected(old('tipo_identificador', $form->tipo_identificador ?? 'email') === 'email')>E-mail</option>
            <option value="cpf" @selected(old('tipo_identificador', $form->tipo_identificador) === 'cpf')>CPF</option>
        </select>
        <small>O valor informado não comprova a identidade. Ele será visível ao proprietário junto à resposta.</small>
    </div>
    <label>
        <input type="radio" name="modo_acesso" form="form-builder" value="aberto_livre" @checked($modoAcesso === 'aberto_livre') required>
        <span><strong>Aberto livre · múltiplos envios</strong><small>Qualquer pessoa com o link pode responder várias vezes. Não coleta um identificador para controlar os envios, mesmo quando a pessoa está logada.</small></span>
    </label>
    @if($form->exists)
        <p class="access-history-note">Alterar o modo não apaga respostas anteriores. Identificadores já registrados continuam contando para o limite correspondente; envios anônimos anteriores não podem ser associados a uma pessoa.</p>
    @endif
</section>
