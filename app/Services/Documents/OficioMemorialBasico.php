<?php

namespace App\Services\Documents;

use App\Models\Project;

class OficioMemorialBasico extends BaseDocument
{
    protected string $templatePath = 'C:/xampp/htdocs/ignea_system/resources/templates/word/Oficio de Apresentação do PSCIP (MODELO PARANÁ) XX.XXX-PL-OA-PCI-00.docx';
    protected string $filePrefix = 'Oficio';

    public function gerar(Project $project): string
    {
        $processor = $this->getTemplateProcessor();

        // Substituições básicas de exemplo
        $this->setSafeValue($processor, 'codigo_interno', $project->codigo_interno);
        $this->setSafeValue($processor, 'nome_obra', $project->nome_obra);
        $this->setSafeValue($processor, 'endereco', $project->endereco);
        
        return $this->salvar($processor, $project);
    }
}
