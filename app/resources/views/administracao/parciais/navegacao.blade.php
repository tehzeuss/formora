@if(session('status'))
    <div class="admin-notice" role="status">{{ session('status') }}</div>
@endif
@if($errors->any())
    <div class="admin-errors" role="alert">
        <ul>
            @foreach($errors->all() as $erro)
                <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
@endif
