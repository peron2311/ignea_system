<?php

namespace App\Services\Documents;

use App\Models\Project;
use App\Services\Calculations\PciCalculations;

class PlanoEmergencia extends BaseDocument
{
    protected string $filePrefix = 'Plano_Emergencia';

    public function __construct()
    {
        $this->templatePath = resource_path('templates/word/25.110-PL-PE-PCI-00.docx');
    }

    public function gerar(Project $project): string
    {
        $this->criarSecao();

        $this->addHeading("PLANO DE EMERGÊNCIA CONTRA INCÊNDIO E PÂNICO", 1);
        $this->addParagrafo("Edificação: " . $project->nome_obra, true);
        $this->addParagrafo("Endereço: " . $project->endereco);

        $this->addHeading("1. CARACTERÍSTICAS DA EDIFICAÇÃO", 2);
        $this->addParagrafo("Uso/Ocupação: " . ($project->ocupacoes[0]['divisao'] ?? 'N/A'));
        $this->addParagrafo("População Fixa: " . ($project->populacao_total ?? 'Não informada'));
        $this->addParagrafo("Horário de Funcionamento: " . ($project->horario_funcionamento ?? 'Não informado'));

        $this->addHeading("2. CÁLCULO DA BRIGADA DE INCÊNDIO", 2);
        $brigada = PciCalculations::calcularBrigadistas(
            $project->populacao_total ?? 0,
            $project->tipo_brigadista ?? 'organico',
            $project->risco_espaco_confinado ?? false,
            $project->risco_trabalho_altura ?? false,
            $project->risco_produtos_perigosos ?? false
        );

        $this->addParagrafo("De acordo com a NPT 017, a composição da brigada de incêndio para esta edificação é:");
        $this->addBullet("Número de Brigadistas: " . $brigada['num_final']);
        $this->addBullet("Carga Horária do Treinamento: " . $brigada['carga_horaria_total'] . " horas");

        if (!empty($brigada['riscos_extras'])) {
            $this->addParagrafo("Riscos Específicos Identificados:", true);
            foreach ($brigada['riscos_extras'] as $risco) {
                $this->addBullet($risco['risco'] . " (+ " . $risco['horas'] . "h)");
            }
        }

        $this->addHeading("3. PROCEDIMENTOS EM CASO DE EMERGÊNCIA", 2);
        $this->addBullet("Alerta: Qualquer pessoa que detectar um princípio de incêndio deve acionar o alarme.");
        $this->addBullet("Abandono de Área: Deve ser feito de forma ordenada seguindo a sinalização.");
        $this->addBullet("Corte de Energia: A brigada deve providenciar o corte da energia elétrica e gás.");

        return $this->salvar($project->codigo_interno . "-plano-emergencia.docx");
    }
}
