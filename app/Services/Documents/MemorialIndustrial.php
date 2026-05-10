<?php

namespace App\Services\Documents;

use App\Models\Project;

class MemorialIndustrial extends BaseDocument
{
    protected string $filePrefix = "Memorial_Industrial";

    public function __construct()
    {
        $this->templatePath = resource_path('templates/word/MEMORIAL INDUSTRIAL DE SEGURANÇA CONTRA INCÊNDIO E DESASTRE.docx');
    }

    public function gerar(Project $project): string
    {
        return $this->salvar($this->getTemplateProcessor(), $project);
    }
}
