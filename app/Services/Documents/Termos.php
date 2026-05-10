<?php

namespace App\Services\Documents;

use App\Models\Project;

class Termos extends BaseDocument
{
    protected string $filePrefix = 'Termo';

    public function gerar(Project $project): string
    {
        return ""; // Usaremos os métodos específicos
    }

    public function gerarTermoCompromisso(Project $project): string
    {
        $this->templatePath = 'C:/xampp/htdocs/ignea_system/resources/templates/word/1. TERMO DE COMPROMISSO ÍGNEA.docx';
        return $this->salvar($this->getTemplateProcessor(), $project, 'Compromisso');
    }

    public function gerarTermoEntregaProjetos(Project $project): string
    {
        $this->templatePath = 'C:/xampp/htdocs/ignea_system/resources/templates/word/2. TERMO DE ENTREGA DE PROJETOS ÍGNEA.docx';
        return $this->salvar($this->getTemplateProcessor(), $project, 'Entrega_Projetos');
    }

    public function gerarTermoSaidasEmergencia(Project $project): string
    {
        $this->templatePath = 'C:/xampp/htdocs/ignea_system/resources/templates/word/3. TERMO DE RESPONSABILIDADE DAS SAÍDAS DE EMERGÊNCIA.docx';
        return $this->salvar($this->getTemplateProcessor(), $project, 'Saidas_Emergencia');
    }

    public function gerarTermoSindico(Project $project): string
    {
        $this->templatePath = 'C:/xampp/htdocs/ignea_system/resources/templates/word/4. TERMO DE ENTREGA PARA SINDICO (para construtora entregar ao sindico futuramente).docx';
        return $this->salvar($this->getTemplateProcessor(), $project, 'Sindico');
    }

    public function gerarTermoInquilino(Project $project): string
    {
        $this->templatePath = 'C:/xampp/htdocs/ignea_system/resources/templates/word/4. TERMO DE ENTREGA PARA INQUILINO (para construtora entregar ao inquilino futuramente).docx';
        return $this->salvar($this->getTemplateProcessor(), $project, 'Inquilino');
    }
}
