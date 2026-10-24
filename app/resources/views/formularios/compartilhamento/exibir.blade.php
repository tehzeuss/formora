@extends('layouts.app')
@section('title', $form->titulo.' | Formora')
@push('styles')
    @vite('resources/css/paginas/formularios/compartilhamento.css')
@endpush
@push('scripts')
    @vite('resources/js/paginas/formularios/visualizar.js')
    @vite('resources/js/paginas/formularios/respostas.js')
@endpush
@section('content')
<div class="form-preview shared-form">
    <div class="shared-access-note">
        @if($form->exigeLogin())
            Fechado · {{ auth()->user()->name }} · Uma resposta por usuário.
        @elseif($form->limitaPorIdentificador())
            Aberto com limite · Uma resposta por {{ $form->tipo_identificador === 'cpf' ? 'CPF' : 'e-mail' }} informado. Não exige login.
        @else
            Aberto livre · Sem identificação para controle de envios. Você pode responder mais de uma vez.
        @endif
    </div>
    @if($errors->any())
        <div class="submission-errors" role="alert" tabindex="-1" data-submission-errors>
            <strong>Confira suas respostas antes de enviar.</strong>
            <ul>@foreach($errors->all() as $erro)<li>{{ $erro }}</li>@endforeach</ul>
        </div>
    @endif
    <form method="post" action="{{ route('respostas.enviar', $form->token_compartilhamento) }}" data-answer-form>
        @csrf
        <input type="hidden" name="versao" value="{{ $form->versaoRespostas() }}">
        <input type="hidden" name="chave_envio" value="{{ $chaveEnvio }}">
        @if($form->prazo_encerramento)
            <p class="shared-access-note">Respostas até {{ $form->prazo_encerramento->copy()->timezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }} · Horário de Brasília.</p>
        @endif
        @include('formularios.parciais.conteudo', ['compartilhado' => true, 'respondendo' => true])
        @if($form->limitaPorIdentificador())
            <section class="submission-identifier">
                <label for="identificador">{{ $form->tipo_identificador === 'cpf' ? 'CPF' : 'E-mail' }} <span aria-hidden="true">*</span></label>
                <p id="identifier-help">Usado para limitar a um envio e exibido ao proprietário junto à sua resposta. Este dado é informado por você, sem autenticação.</p>
                <input id="identificador" name="identificador" type="{{ $form->tipo_identificador === 'email' ? 'email' : 'text' }}" inputmode="{{ $form->tipo_identificador === 'email' ? 'email' : 'numeric' }}" autocomplete="{{ $form->tipo_identificador === 'email' ? 'email' : 'off' }}" maxlength="{{ $form->tipo_identificador === 'email' ? 254 : 14 }}" value="{{ old('identificador') }}" aria-describedby="identifier-help identifier-error" @if($errors->has('identificador')) aria-invalid="true" @endif required>
                <span id="identifier-error" class="identifier-error">{{ $errors->first('identificador') }}</span>
            </section>
        @endif
        <div class="submission-actions"><button type="submit" class="button" data-submit-answers>Enviar respostas</button><small>Confira suas respostas antes de enviar.</small></div>
    </form>
</div>
@endsection
