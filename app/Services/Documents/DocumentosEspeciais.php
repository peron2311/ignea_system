<?php

namespace App\Services\Documents;

use App\Models\Project;

class DocumentosEspeciais extends BaseDocument
{
    protected string $filePrefix = "Especial";

    public function gerar(Project $project): string
    {
        return "";
    }

    public function gerarComprovacao(Project $project): string
    {
        $this->templatePath = resource_path('templates/word/COMPROVAÇÃO DE EXISTÊNCIA.docx');
        return $this->salvar($this->getTemplateProcessor(), $project, "Comprovacao_Existencia");
    }

    public function gerarRequerimento(Project $project): string
    {
        $this->templatePath = resource_path('templates/word/Requerimento de Substituição de Projeto.docx');
        return $this->salvar($this->getTemplateProcessor(), $project, "Requerimento_Substituicao");
    }
}
