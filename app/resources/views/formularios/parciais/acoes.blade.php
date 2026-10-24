<div class="form-management-actions">
    <form method="post" action="{{ route('formularios.duplicar', $form) }}">
        @csrf
        <button type="submit"><x-icone name="copy" /> Duplicar</button>
    </form>
    <a class="form-delete-action" href="{{ route('formularios.confirmar-exclusao', $form) }}" aria-label="Excluir formulário {{ $form->titulo }}"><x-icone name="trash" /></a>
</div>
