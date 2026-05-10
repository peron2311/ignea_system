<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Documents\{
    MemorialDescritivo, PlanoEmergencia, OficioMemorialBasico,
    MemorialIndustrial, MemorialExecutivo, ManualTecnico, Termos, DocumentosEspeciais
};

class DocumentController extends Controller
{
    private array $geradores = [
        'memorial'                 => MemorialDescritivo::class,
        'oficio'                   => OficioMemorialBasico::class,
        'plano-emergencia'         => PlanoEmergencia::class,
        'memorial-industrial'      => MemorialIndustrial::class,
        'memorial-executivo'       => MemorialExecutivo::class,
        'manual-tecnico'           => ManualTecnico::class,
        'termo-compromisso'        => [Termos::class, 'gerarTermoCompromisso'],
        'termo-entrega-projetos'   => [Termos::class, 'gerarTermoEntregaProjetos'],
        'termo-saidas-emergencia'  => [Termos::class, 'gerarTermoSaidasEmergencia'],
        'termo-sindico'            => [Termos::class, 'gerarTermoSindico'],
        'termo-inquilino'          => [Termos::class, 'gerarTermoInquilino'],
        'comprovacao-existencia'   => [DocumentosEspeciais::class, 'gerarComprovacao'],
        'requerimento-substituicao'=> [DocumentosEspeciais::class, 'gerarRequerimento'],
    ];

    public function gerar(Project $project, string $tipo)
    {
        abort_unless(isset($this->geradores[$tipo]), 404, "Documento '$tipo' não encontrado.");

        try {
            $gerador = $this->geradores[$tipo];

            if (is_array($gerador)) {
                [$classe, $metodo] = $gerador;
                $path = (new $classe())->$metodo($project);
            } else {
                $path = (new $gerador())->gerar($project);
            }

            $filename = ($project->codigo_interno ?: 'PROJ') . '-' . $tipo . '.docx';
            
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
