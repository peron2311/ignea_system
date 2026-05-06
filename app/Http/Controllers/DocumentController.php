<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Documents\MemorialDescritivo;
use App\Services\Documents\PlanoEmergencia;
use App\Services\Documents\OficioMemorialBasico;
use App\Services\Documents\Termos;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    private array $geradores = [
        'memorial'                => MemorialDescritivo::class,
        'plano-emergencia'        => PlanoEmergencia::class,
        'oficio'                  => OficioMemorialBasico::class,
        'termo-compromisso'       => [Termos::class, 'gerarTermoCompromisso'],
        'termo-saidas-emergencia' => [Termos::class, 'gerarTermoSaidasEmergencia'],
    ];

    public function gerar(Project $project, string $tipo)
    {
        if (!isset($this->geradores[$tipo])) {
            abort(404, "Tipo de documento não encontrado: {$tipo}");
        }

        try {
            $classe = $this->geradores[$tipo];
            $path = (new $classe())->gerar($project);

            $idFormatado = str_pad($project->id, 4, '0', STR_PAD_LEFT);
            $tipoNome = match($tipo) {
                'memorial' => 'memorial',
                'plano-emergencia' => 'pne',
                'oficio' => 'oficio',
                'termo-compromisso', 'termo-saidas-emergencia' => 'termo',
                default => 'doc'
            };
            $filename = $tipoNome . $idFormatado . '.docx';

            if (ob_get_length()) {
                ob_end_clean();
            }

            $content = file_get_contents($path);

            return response($content, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
                'Content-Length' => strlen($content),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar documento: ' . $e->getMessage());
        }
    }
}
