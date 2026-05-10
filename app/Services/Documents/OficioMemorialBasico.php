<?php

namespace App\Services\Documents;

use App\Models\Project;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Shared\Converter;

class OficioMemorialBasico extends BaseDocument
{
    protected string $filePrefix = 'Oficio';

    public function __construct()
    {
        $this->templatePath = resource_path('templates/word/Oficio de Apresentação do PSCIP (MODELO PARANÁ) XX.XXX-PL-OA-PCI-00.docx');
    }

    public function gerar(Project $project): string
    {
        $this->criarSecao();

        $cidade = $project->cidade ?? 'Cascavel';
        $dataFormatada = $this->formatarDataExtenso();

        // ── 1. Cabeçalho com data (alinhado à esquerda) ──
        $this->section->addText(
            htmlspecialchars("{$cidade} – Paraná, {$dataFormatada}."),
            ['name' => 'Arial', 'size' => 12],
            ['alignment' => Jc::START, 'spaceAfter' => Converter::pointToTwip(24)]
        );

        // ── 2. Destinatário (centralizado) ──
        $pCenter = ['alignment' => Jc::CENTER, 'spaceAfter' => 0, 'lineHeight' => 1.5];
        $pCenterAfter = ['alignment' => Jc::CENTER, 'spaceAfter' => Converter::pointToTwip(6), 'lineHeight' => 1.5];
        $fontNormal = ['name' => 'Arial', 'size' => 12];
        $fontBold   = ['name' => 'Arial', 'size' => 12, 'bold' => true];

        $this->section->addText('Ao', $fontNormal, $pCenter);
        $this->section->addText(
            'Serviço de Prevenção Contra Incêndio e Pânico',
            $fontBold,
            $pCenter
        );
        $this->section->addText(
            'Corpo de Bombeiro Militar do Paraná',
            $fontNormal,
            $pCenter
        );
        $cidadeAnalise = $project->cidade_analise_bombeiros ?? $cidade;
        $this->section->addText(
            htmlspecialchars("{$cidadeAnalise} – Paraná"),
            $fontBold,
            ['alignment' => Jc::CENTER, 'spaceAfter' => Converter::pointToTwip(36), 'lineHeight' => 1.5]
        );

        // ── 3. Saudação ──
        $this->section->addText(
            'Ilustríssimos Senhores,',
            $fontNormal,
            ['alignment' => Jc::CENTER, 'spaceAfter' => Converter::pointToTwip(24), 'lineHeight' => 1.5]
        );

        // ── 4. Corpo do texto ──
        $this->section->addText(
            'Em conformidade com o CSCIP-CBMPR, vimos por meio deste solicitar a análise e posterior aprovação do Plano de Segurança Contra Incêndio e Pânico da seguinte edificação:',
            $fontBold,
            ['alignment' => Jc::CENTER, 'spaceAfter' => Converter::pointToTwip(20), 'lineHeight' => 1.5]
        );

        // ── 5. Dados da edificação (centralizado, label em negrito) ──
        $this->addCampo('Obra', $project->nome_obra);
        $this->addCampo('Proprietário', $project->nome_proprietario);
        $this->addCampo('CNPJ/CPF', $project->cpf_cnpj);

        $endereco = $project->endereco;
        if ($project->cidade || $project->estado) {
            $endereco .= ', ' . ($project->cidade ?? '') . '/' . ($project->estado ?? '');
        }
        $this->addCampo('Endereço', $endereco);

        $this->addCampo('Indicação Fiscal/Inscrição Imobiliária', $project->inscricao_imobiliaria ?? '');

        // Ocupação derivada do campo JSON ocupacoes
        $ocupacaoTexto = $this->formatarOcupacoes($project->ocupacoes);
        $this->addCampo('Ocupação', $ocupacaoTexto);

        $areaFormatada = number_format($project->area_total ?? 0, 2, ',', '.') . ' m²';
        $this->addCampo('Área Total', $areaFormatada, Converter::pointToTwip(30));

        // ── 6. Fechamento ──
        $this->section->addText(
            'Restrito ao exposto, antecipadamente agradecemos.',
            $fontNormal,
            ['alignment' => Jc::CENTER, 'spaceAfter' => Converter::pointToTwip(12), 'lineHeight' => 1.5]
        );
        $this->section->addText(
            'Atenciosamente',
            ['name' => 'Arial', 'size' => 12, 'italic' => true],
            ['alignment' => Jc::CENTER, 'spaceAfter' => Converter::pointToTwip(60), 'lineHeight' => 1.5]
        );

        // ── 7. Assinatura ──
        $this->section->addText(
            '________________________________________',
            $fontNormal,
            ['alignment' => Jc::CENTER, 'spaceAfter' => 0]
        );

        // Nome do RT (sem "Eng." prefixo se já está no campo)
        $this->section->addText(
            htmlspecialchars($project->rt_nome ?? 'Ana Julia Zunta Carniel'),
            $fontNormal,
            ['alignment' => Jc::CENTER, 'spaceAfter' => 0, 'lineHeight' => 1.5]
        );

        // CREA formatado
        $creaTexto = $project->rt_crea ?? 'CREA: 168.913/D';
        // Normalizar: se já começa com "CREA", manter; senão prefixar
        if (stripos($creaTexto, 'CREA') === false) {
            $creaTexto = 'CREA: ' . $creaTexto;
        }
        $this->section->addText(
            htmlspecialchars($creaTexto),
            $fontNormal,
            ['alignment' => Jc::CENTER, 'spaceAfter' => 0, 'lineHeight' => 1.5]
        );

        return $this->salvar($project->codigo_interno . '-oficio.docx');
    }

    /**
     * Adiciona uma linha de campo centralizada: "Label: Valor"
     * O label fica em negrito e o valor em peso normal.
     */
    private function addCampo(string $label, ?string $valor, int $spaceAfter = 0): void
    {
        $textRun = $this->section->addTextRun([
            'alignment' => Jc::CENTER,
            'spaceAfter' => $spaceAfter,
            'lineHeight' => 1.5,
        ]);

        $textRun->addText(
            htmlspecialchars("{$label}: "),
            ['name' => 'Arial', 'size' => 12, 'bold' => true]
        );

        $textRun->addText(
            htmlspecialchars($valor ?? ''),
            ['name' => 'Arial', 'size' => 12]
        );
    }

    /**
     * Formata o array de ocupações JSON em texto legível.
     * Ex: [{grupo: "F", divisao: "11"}, {grupo: "C", divisao: "2"}] → "F-11 e C-2"
     */
    private function formatarOcupacoes(?array $ocupacoes): string
    {
        if (empty($ocupacoes)) {
            return '';
        }

        $items = [];
        foreach ($ocupacoes as $oc) {
            $grupo  = $oc['grupo'] ?? '';
            $divisao = $oc['divisao'] ?? '';
            $items[] = trim("{$grupo}-{$divisao}", '-');
        }

        if (count($items) === 0) {
            return '';
        }

        if (count($items) === 1) {
            return $items[0];
        }

        // "F-11, C-2 e D-1" → last item joined with " e ", rest with ", "
        $last = array_pop($items);
        return implode(', ', $items) . ' e ' . $last;
    }

    /**
     * Retorna a data atual por extenso: "25 de março de 2025"
     */
    private function formatarDataExtenso(): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];

        $dia = date('j');
        $mes = $meses[(int) date('n')];
        $ano = date('Y');

        return "{$dia} de {$mes} de {$ano}";
    }
}
