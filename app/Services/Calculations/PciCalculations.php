<?php

namespace App\Services\Calculations;

class PciCalculations
{
    /**
     * Tabela 2 CSCIP — Classificação por altura
     */
    public static function getClassificacaoAltura(float $altura): array
    {
        if ($altura <= 0)  return ['tipo' => 'TIPO I',   'denominacao' => 'Edificação Térrea',              'faixa' => 'Um pavimento'];
        if ($altura <= 6)  return ['tipo' => 'TIPO II',  'denominacao' => 'Edificação Baixa',               'faixa' => 'H ≤ 6,00 m'];
        if ($altura <= 12) return ['tipo' => 'TIPO III', 'denominacao' => 'Edificação de Baixa-Média Altura','faixa' => '6,00 m < H ≤ 12,00 m'];
        if ($altura <= 23) return ['tipo' => 'TIPO IV',  'denominacao' => 'Edificação de Média Altura',      'faixa' => '12,00 m < H ≤ 23,00 m'];
        if ($altura <= 30) return ['tipo' => 'TIPO V',   'denominacao' => 'Edificação Mediamente Alta',      'faixa' => '23,00 m < H ≤ 30,00 m'];
        return             ['tipo' => 'TIPO VI',  'denominacao' => 'Edificação Alta',              'faixa' => 'Acima de 30,00 m'];
    }

    /**
     * Tabela 3 CSCIP — Classificação por CI (Carga de Incêndio)
     */
    public static function getClassificacaoRisco(float $ci): string
    {
        if ($ci <= 300)  return 'Risco Leve';
        if ($ci <= 1200) return 'Risco Moderado';
        return 'Risco Elevado';
    }

    /**
     * Carga de incêndio média ponderada
     */
    public static function calcularCargaIncendio(array $ocupacoes): float
    {
        $totalArea = collect($ocupacoes)->sum('area');
        if ($totalArea == 0) return 0;
        return collect($ocupacoes)->sum(fn($o) => ($o['ci'] ?? 0) * ($o['area'] ?? 0)) / $totalArea;
    }

    /**
     * Cálculo de brigadistas (NPT 017)
     */
    public static function calcularBrigadistas(
        int $populacao,
        string $tipo = 'organico',
        bool $confinado = false,
        bool $altura = false,
        bool $perigosos = false
    ): array {
        $popExposta  = (int) ceil($populacao * 1.3);
        $numOrganicos = (int) ceil($popExposta / 200);
        $numProfissionais = (int) ceil($numOrganicos / 5);

        $cargaBase = 32;
        $riscos = [];
        if ($confinado) { $riscos[] = ['risco' => 'Espaço Confinado', 'horas' => 32]; $cargaBase += 32; }
        if ($altura)    { $riscos[] = ['risco' => 'Trabalho em Altura', 'horas' => 16]; $cargaBase += 16; }
        if ($perigosos) { $riscos[] = ['risco' => 'Produtos Perigosos', 'horas' => 32]; $cargaBase += 32; }
        if (!empty($riscos)) $cargaBase += 16; // +16h comando se há riscos específicos

        return [
            'num_organicos'       => max(1, $numOrganicos),
            'num_profissionais'   => max(1, $numProfissionais),
            'num_final'           => $tipo === 'profissional' ? max(1, $numProfissionais) : max(1, $numOrganicos),
            'carga_horaria_total' => $cargaBase,
            'riscos_extras'       => $riscos,
        ];
    }

    /**
     * TRRF por ocupação e altura (NPT 008)
     */
    public static function calcularTrrf(string $divisao, float $altura): array
    {
        $grupo = substr($divisao, 0, 1);
        $classeAltura = self::getClassificacaoAltura($altura);

        // Tabela simplificada TRRF (baseada na NPT 008)
        $tabela = [
            'A' => ['TIPO I' => 30, 'TIPO II' => 30, 'TIPO III' => 60, 'TIPO IV' => 60, 'TIPO V' => 90, 'TIPO VI' => 120],
            'B' => ['TIPO I' => 60, 'TIPO II' => 60, 'TIPO III' => 60, 'TIPO IV' => 90, 'TIPO V' => 90, 'TIPO VI' => 120],
            'C' => ['TIPO I' => 30, 'TIPO II' => 30, 'TIPO III' => 60, 'TIPO IV' => 60, 'TIPO V' => 90, 'TIPO VI' => 120],
            'D' => ['TIPO I' => 30, 'TIPO II' => 30, 'TIPO III' => 60, 'TIPO IV' => 60, 'TIPO V' => 90, 'TIPO VI' => 120],
            'E' => ['TIPO I' => 30, 'TIPO II' => 30, 'TIPO III' => 60, 'TIPO IV' => 60, 'TIPO V' => 90, 'TIPO VI' => 120],
            'F' => ['TIPO I' => 60, 'TIPO II' => 60, 'TIPO III' => 60, 'TIPO IV' => 90, 'TIPO V' => 90, 'TIPO VI' => 120],
            'G' => ['TIPO I' => 30, 'TIPO II' => 30, 'TIPO III' => 60, 'TIPO IV' => 60, 'TIPO V' => 90, 'TIPO VI' => 120],
            'H' => ['TIPO I' => 60, 'TIPO II' => 60, 'TIPO III' => 90, 'TIPO IV' => 90, 'TIPO V' => 120,'TIPO VI' => 120],
            'I' => ['TIPO I' => 30, 'TIPO II' => 60, 'TIPO III' => 60, 'TIPO IV' => 90, 'TIPO V' => 90, 'TIPO VI' => 120],
            'J' => ['TIPO I' => 30, 'TIPO II' => 60, 'TIPO III' => 60, 'TIPO IV' => 90, 'TIPO V' => 90, 'TIPO VI' => 120],
        ];

        $trrf = $tabela[$grupo][$classeAltura['tipo']] ?? 60;

        return [
            'divisao'      => $divisao,
            'grupo'        => $grupo,
            'classe_altura'=> $classeAltura['tipo'],
            'faixa_altura' => $classeAltura['faixa'],
            'trrf'         => $trrf,
        ];
    }

    public static function calcularTrrfPorOcupacoes(array $ocupacoes, float $altura): array
    {
        return collect($ocupacoes)
            ->map(fn($o) => self::calcularTrrf($o['divisao'] ?? '', $altura))
            ->unique('divisao')
            ->values()
            ->toArray();
    }

    // ── Distâncias de extintores NPT 021 ─────────────────────────────────
    public static function calcularDistanciaExtintores(float $ciMedia): array
    {
        if ($ciMedia <= 300)  return ['dist_base' => 25, 'sem_layout' => 17];
        if ($ciMedia <= 1200) return ['dist_base' => 20, 'sem_layout' => 14];
        return ['dist_base' => 15, 'sem_layout' => 10];
    }

    // ── Mapeamentos ───────────────────────────────────────────────────────
    public static array $medidasDisponiveis = [
        'acesso_viaturas'       => 'Acesso de Viatura na Edificação',
        'isolamento_risco'      => 'Separação entre Edificações (Isolamento de Risco)',
        'seguranca_estrutural'  => 'Segurança Estrutural Contra Incêndio',
        'compartimentacao'      => 'Compartimentação Horizontal e Vertical',
        'materiais_acabamento'  => 'Controle de Materiais de Acabamento (CMAR)',
        'saidas_emergencia'     => 'Saídas de Emergência',
        'controle_fumaca'       => 'Controle de Fumaça',
        'plano_emergencia'      => 'Plano de Emergência',
        'brigada'               => 'Brigada de Incêndio',
        'iluminacao_emergencia' => 'Iluminação de Emergência',
        'alarme_deteccao'       => 'Sistema de Detecção e Alarme de Incêndio',
        'sinalizacao'           => 'Sinalização de Emergência',
        'extintor'              => 'Extintor de Incêndio',
        'hidrante'              => 'Hidrantes e Mangotinhos',
        'chuveiros_automaticos' => 'Chuveiros Automáticos',
        'liquidos_inflamaveis'  => 'Líquidos e Gases Combustíveis e Inflamáveis',
        'spda'                  => 'SPDA',
        'glp'                   => 'Central de GLP',
    ];

    public static array $medidasNpt = [
        'acesso_viaturas'       => 'NPT 006',
        'isolamento_risco'      => 'NPT 007',
        'seguranca_estrutural'  => 'NPT 008',
        'compartimentacao'      => 'NPT 009',
        'materiais_acabamento'  => 'NPT 010',
        'saidas_emergencia'     => 'NPT 011',
        'controle_fumaca'       => 'NPT 015',
        'plano_emergencia'      => 'NPT 016',
        'brigada'               => 'NPT 017',
        'iluminacao_emergencia' => 'NPT 018',
        'alarme_deteccao'       => 'NPT 019',
        'sinalizacao'           => 'NPT 020',
        'extintor'              => 'NPT 021',
        'hidrante'              => 'NPT 022',
        'chuveiros_automaticos' => 'NPT 023/024',
        'liquidos_inflamaveis'  => 'NPT 025',
        'spda'                  => 'NPT 026',
        'glp'                   => 'NPT 028',
    ];

    // ── CI padrão por divisão (Tabela 1 CSCIP) ───────────────────────────
    public static array $ciPorDivisao = [
        'A-1'=>300,'A-2'=>300,'A-3'=>300,
        'B-1'=>300,'B-2'=>300,
        'C-1'=>300,'C-2'=>1000,'C-3'=>1000,
        'D-1'=>300,'D-2'=>600,'D-3'=>600,'D-4'=>600,
        'E-1'=>300,'E-2'=>300,'E-3'=>300,'E-4'=>300,'E-5'=>300,'E-6'=>300,
        'F-1'=>300,'F-2'=>300,'F-3'=>300,'F-4'=>300,'F-5'=>600,
        'F-6'=>600,'F-7'=>300,'F-8'=>600,'F-9'=>300,'F-10'=>300,'F-11'=>600,
        'G-1'=>600,'G-2'=>600,'G-3'=>1200,'G-4'=>600,'G-5'=>1200,'G-6'=>600,
        'H-1'=>300,'H-2'=>300,'H-3'=>300,'H-4'=>300,'H-5'=>300,'H-6'=>300,
        'I-1'=>300,'I-2'=>600,'I-3'=>1200,
        'J-1'=>100,'J-2'=>300,'J-3'=>600,'J-4'=>1200,
        'L-1'=>1200,'L-2'=>1200,'L-3'=>1200,
        'M-1'=>600,'M-2'=>1200,'M-3'=>600,'M-4'=>300,'M-5'=>1200,'M-6'=>300,'M-7'=>300,
    ];
}
