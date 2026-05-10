<?php

namespace App\Services\Documents;

use App\Models\Project;

class ManualTecnico extends BaseDocument
{
    protected string $filePrefix = "Manual_Tecnico";

    public function __construct()
    {
        $this->templatePath = resource_path('templates/word/MANUAL TÉCNICO - PR - MODELO (COMPLETO) - IGNEA.docx');
    }

    public function gerar(Project $project): string
    {
        return $this->salvar($this->getTemplateProcessor(), $project);
    }
}
