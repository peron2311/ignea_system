<?php

namespace App\Services\Documents;

use App\Models\Project;

class MemorialDescritivo extends BaseDocument
{
    protected string $filePrefix = 'Memorial';

    public function __construct()
    {
        $this->templatePath = resource_path('templates/word/X.XXX-PL-MD-PCI-00.docx');
    }

    public function gerar(Project $project): string
    {
        $this->criarSecao();

        $this->addHeading("MEMORIAL DESCRITIVO DE SEGURANÇA CONTRA INCÊNDIO E PÂNICO", 1);
        $this->addParagrafo("Obra: " . $project->nome_obra, true);
        $this->addParagrafo("Código Interno: " . $project->codigo_interno);
        $this->addParagrafo("Endereço: " . $project->endereco . ", " . $project->cidade . " - " . $project->estado);

        $this->addHeading("1. IDENTIFICAÇÃO", 2);
        $this->addParagrafo("Proprietário: " . $project->nome_proprietario);
        $this->addParagrafo($project->tipo_documento . ": " . $project->cpf_cnpj);

        $this->addHeading("2. DADOS DA EDIFICAÇÃO", 2);
        $this->addParagrafo("Área Total: " . number_format($project->area_total, 2, ',', '.') . " m²");
        $this->addParagrafo("Altura: " . number_format($project->altura, 2, ',', '.') . " m");
        $this->addParagrafo("Número de Pavimentos: " . $project->num_pavimentos);

        $this->addHeading("3. MEDIDAS DE SEGURANÇA", 2);
        if ($project->medidas_selecionadas) {
            foreach ($project->medidas_selecionadas as $medida) {
                $this->addBullet(ucfirst($medida));
            }
        } else {
            $this->addParagrafo("Nenhuma medida selecionada.");
        }

        return $this->salvar($project->codigo_interno . "-memorial-descritivo.docx");
    }
}
