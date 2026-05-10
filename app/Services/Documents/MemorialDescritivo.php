<?php

namespace App\Services\Documents;

use App\Models\Project;

class MemorialDescritivo extends BaseDocument
{
    protected string $templatePath = 'C:/xampp/htdocs/ignea_system/resources/templates/word/X.XXX-PL-MD-PCI-00.docx';
    protected string $filePrefix = 'Memorial';

    public function gerar(Project $project): string
    {
        $processor = $this->getTemplateProcessor();
        return $this->salvar($processor, $project);
    }
}
