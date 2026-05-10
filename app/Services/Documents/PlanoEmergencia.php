<?php

namespace App\Services\Documents;

use App\Models\Project;

class PlanoEmergencia extends BaseDocument
{
    protected string $templatePath = 'C:/xampp/htdocs/ignea_system/resources/templates/word/25.110-PL-PE-PCI-00.docx';
    protected string $filePrefix = 'Plano_Emergencia';

    public function gerar(Project $project): string
    {
        $processor = $this->getTemplateProcessor();
        return $this->salvar($processor, $project);
    }
}
