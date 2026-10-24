<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\FormularioController;
use App\Http\Controllers\PublicacaoFormularioController;
use App\Http\Controllers\CompartilhamentoFormularioController;
use App\Http\Controllers\RespostaFormularioController;
use App\Http\Middleware\AcessoFormularioCompartilhado;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/criar-conta', [\App\Http\Controllers\RegistroController::class, 'criar'])->name('registro.criar');
    Route::post('/criar-conta', [\App\Http\Controllers\RegistroController::class, 'salvar'])->middleware('throttle:5,1')->name('registro.salvar');
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'autenticar'])->middleware('throttle:20,1')->name('login.autenticar');
});

Route::middleware(['auth', \App\Http\Middleware\ExigirUsuarioAtivo::class])->group(function () {
    Route::prefix('administracao')->name('administracao.')->middleware(\App\Http\Middleware\ExigirAdministrador::class)->group(function () {
        Route::get('/usuarios', [\App\Http\Controllers\UsuarioAdministradorController::class, 'listar'])->name('usuarios.listar');
        Route::get('/usuarios/criar', [\App\Http\Controllers\UsuarioAdministradorController::class, 'criar'])->name('usuarios.criar');
        Route::post('/usuarios', [\App\Http\Controllers\UsuarioAdministradorController::class, 'salvar'])->middleware('throttle:20,1')->name('usuarios.salvar');
        Route::get('/usuarios/{usuario}/editar', [\App\Http\Controllers\UsuarioAdministradorController::class, 'editar'])->name('usuarios.editar');
        Route::put('/usuarios/{usuario}', [\App\Http\Controllers\UsuarioAdministradorController::class, 'atualizar'])->name('usuarios.atualizar');
        Route::get('/formularios', [\App\Http\Controllers\FormularioAdministradorController::class, 'listar'])->name('formularios.listar');
        Route::get('/formularios/{form}', [\App\Http\Controllers\FormularioAdministradorController::class, 'visualizar'])->name('formularios.visualizar');
        Route::get('/formularios/{form}/imagem/{pergunta?}', [\App\Http\Controllers\FormularioAdministradorController::class, 'imagem'])->name('formularios.imagem');
        Route::get('/formularios/{form}/transferir', [\App\Http\Controllers\FormularioAdministradorController::class, 'transferencia'])->name('formularios.transferencia');
        Route::post('/formularios/{form}/transferir', [\App\Http\Controllers\FormularioAdministradorController::class, 'transferir'])->name('formularios.transferir');
    });
    Route::get('/formularios/{form}/respostas/exportar', [\App\Http\Controllers\ExportacaoRespostasController::class, 'exportar'])->middleware('throttle:10,1')->name('formularios.respostas.exportar');
    Route::post('/formularios/{form}/duplicar', [\App\Http\Controllers\GerenciamentoFormularioController::class, 'duplicar'])->middleware('throttle:10,1')->name('formularios.duplicar');
    Route::get('/formularios/{form}/excluir', [\App\Http\Controllers\GerenciamentoFormularioController::class, 'confirmarExclusao'])->name('formularios.confirmar-exclusao');
    Route::delete('/formularios/{form}', [\App\Http\Controllers\GerenciamentoFormularioController::class, 'excluir'])->name('formularios.excluir');
    Route::get('/formularios/{form}/respostas', [RespostaFormularioController::class, 'listar'])->name('formularios.respostas.listar');
    Route::get('/formularios/{form}/respostas/{envio}', [RespostaFormularioController::class, 'detalhar'])->whereNumber('envio')->name('formularios.respostas.detalhar');
    Route::post('/formularios/{form}/publicar', [PublicacaoFormularioController::class, 'publicar'])->name('formularios.publicar');
    Route::post('/formularios/{form}/encerrar', [PublicacaoFormularioController::class, 'encerrar'])->name('formularios.encerrar');
    Route::get('/formularios/{form}/imagem/{pergunta?}', [FormularioController::class, 'imagem'])->name('forms.imagem');
    Route::get('/', [FormularioController::class, 'index'])->name('dashboard');
    Route::resource('formularios', FormularioController::class)->parameters(['formularios' => 'form'])->names('forms')->except('destroy');
    Route::post('/logout', [LoginController::class, 'deslogar'])->name('logout');
});

Route::middleware(AcessoFormularioCompartilhado::class)->group(function () {
    Route::post('/f/{token}/respostas', [RespostaFormularioController::class, 'enviar'])->where('token', '[a-zA-Z0-9]{64}')->middleware('throttle:20,1')->name('respostas.enviar');
    Route::get('/f/{token}', [CompartilhamentoFormularioController::class, 'exibir'])->where('token', '[a-zA-Z0-9]{64}')->name('compartilhamento.exibir');
    Route::get('/f/{token}/imagem/{pergunta?}', [CompartilhamentoFormularioController::class, 'imagem'])->where('token', '[a-zA-Z0-9]{64}')->whereNumber('pergunta')->name('compartilhamento.imagem');
});
