<?php

namespace App\Services\Documents;

use App\Models\Project;

class MemorialExecutivo extends BaseDocument
{
    protected string $filePrefix = "Memorial_Executivo";

    public function __construct()
    {
        $this->templatePath = resource_path('templates/word/MEMORIAL EXECUTIVO (MODELO).docx');
    }

    public function gerar(Project $project): string
    {
        return $this->salvar($this->getTemplateProcessor(), $project);
    }
}
