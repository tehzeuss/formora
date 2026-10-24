<?php

namespace App\Http\Controllers;

use App\Http\Requests\GerenciarFormularioRequest;
use App\Models\Formulario;
use App\Services\ExportadorRespostasCsv;
use Illuminate\Support\Str;

class ExportacaoRespostasController extends Controller
{
    public function exportar(GerenciarFormularioRequest $request, Formulario $form, ExportadorRespostasCsv $exportador)
    {
        $nome = 'respostas-'.(Str::slug(Str::limit($form->titulo, 80, '')) ?: $form->id).'.csv';

        return response()->streamDownload(function () use ($request, $form, $exportador) {
            $arquivo = fopen('php://output', 'wb');
            try {
                $exportador->escrever($form, $request->user()->id, $arquivo);
            } finally {
                fclose($arquivo);
            }
        }, $nome, ['Content-Type' => 'text/csv; charset=UTF-8', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }
}
