<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'ocupacoes'                    => 'array',
        'medidas_selecionadas'         => 'array',
        'sdai_componentes'             => 'array',
        'chuveiro_tipos'               => 'array',
        'chuveiro_vgas'                => 'array',
        'chuveiro_tabela_areas'        => 'array',
        'chuveiro_tabela_areas_colunas'=> 'array',
        'chuveiro_bombas'              => 'array',
        'isolamentos'                  => 'array',
        'me_itens_adicionais'          => 'array',
        'me_tabela_sinalizacao'        => 'array',
        'me_tabela_equipamentos'       => 'array',
        'comprov_itens'                => 'array',
        'tabela_populacao'             => 'array',
        'tabela_ci'                    => 'array',
        'setores_compartimentacao'     => 'array',
        'recomendacoes_selecionadas'   => 'array',
        'dados_especificos'            => 'array',
        'area_fria'                    => 'boolean',
        'tem_glp'                      => 'boolean',
        'tem_gerador'                  => 'boolean',
        'tem_subestacao'               => 'boolean',
        'edificacao_existente'         => 'boolean',
        'substituicao_projeto'         => 'boolean',
        'edificacao_residencial'       => 'boolean',
        'edificacao_aluguel'           => 'boolean',
        'porta_correr_saida_emergencia'=> 'boolean',
        'sdai_enderecavel'             => 'boolean',
        'sdai_nota3_cscip'             => 'boolean',
        'sdai_tem_damper'              => 'boolean',
        'chuveiro_tem_camara_fria'     => 'boolean',
        'chuveiro_ul_fm'               => 'boolean',
        'pne'                          => 'boolean',
    ];

    /**
     * Retorna um array associativo gigante com todas as variáveis para os templates Word
     */
    public function getTemplateData(): array
    {
        // 1. Cálculos Base
        $altura = (float) $this->altura;
        $classAlt = \App\Services\Calculations\PciCalculations::getClassificacaoAltura($altura);
        
        $ocupacoes = is_array($this->ocupacoes) ? $this->ocupacoes : [];
        $ciMedia = \App\Services\Calculations\PciCalculations::calcularCargaIncendio($ocupacoes);
        $classRisco = \App\Services\Calculations\PciCalculations::getClassificacaoRisco($ciMedia);

        $areaFriaVal = (float) $this->area_fria_m2; // Assuming a numeric field for area_fria might exist in future, else use boolean area_fria as 0
        $areaProtegida = (float) $this->area_total - $areaFriaVal;

        // 2. Formatação Básica
        $data = [
            // IDENTIFICAÇÃO E PROPRIETÁRIO
            'codigo_interno' => $this->codigo_interno ?? '-',
            'nome_obra' => $this->nome_obra ?? '-',
            'mes_ano' => $this->mes_ano ?? '-',
            'cidade_analise_bombeiros' => $this->cidade_analise_bombeiros ?? '-',
            'numero_art' => $this->numero_art ?? '-',
            
            'nome_proprietario' => $this->nome_proprietario ?? '-',
            'tipo_documento' => $this->tipo_documento ?? 'CNPJ',
            'cpf_cnpj' => $this->cpf_cnpj ?? '-',
            'nome_signatario' => $this->nome_signatario ?? '-',
            'cpf_signatario' => $this->cpf_signatario ?? '-',
            'rg_signatario' => $this->rg_signatario ?? '-',

            // ENDEREÇO
            'endereco' => $this->endereco ?? '-',
            'cidade' => $this->cidade ?? '-',
            'estado' => $this->estado ?? 'PR',
            'cep' => $this->cep ?? '-',
            'indicacao_fiscal' => $this->inscricao_imobiliaria ?? '-', // Mapeando inscricao_imobiliaria para indicacao_fiscal

            // RESPONSÁVEL TÉCNICO
            'rt_nome' => $this->rt_nome ?? '-',
            'rt_crea' => $this->rt_crea ?? '-',
            'rt_email' => $this->rt_email ?? '-',
            'rt_telefone' => $this->rt_telefone ?? '-',

            // EDIFICAÇÃO
            'area_total' => number_format((float)$this->area_total, 2, ',', '.'),
            'area_protegida' => number_format($areaProtegida, 2, ',', '.'),
            'area_fria' => number_format($areaFriaVal, 2, ',', '.'),
            'altura' => number_format($altura, 2, ',', '.'),
            'num_pavimentos' => $this->num_pavimentos ?? '-',
            'tipo_edificacao' => $classAlt['tipo'],
            'denominacao' => $classAlt['denominacao'],
            'faixa_altura' => $classAlt['faixa'],
            'estrutura' => $this->estrutura ?? '-',
            'divisao_interna' => $this->divisao_interna ?? '-',
            'cobertura' => $this->cobertura ?? '-',
            'forro' => $this->forro ?? '-',
            'pisos' => $this->pisos ?? '-',
            'esquadrias' => $this->esquadrias ?? '-',
            'via_acesso' => $this->via_acesso ?? '-',

            // OCUPAÇÕES CALCULADAS
            'divisao' => $ocupacoes[0]['divisao'] ?? '-',
            'divisao_lista' => implode(' e ', array_column($ocupacoes, 'divisao')),
            'ci_media' => number_format($ciMedia, 2, ',', '.'),
            'classificacao_risco' => $classRisco,
        ];

        // 3. Adicionar Recomendacoes do Manual Tecnico (Blocos de Visibilidade)
        $recs = is_array($this->recomendacoes_selecionadas) ? $this->recomendacoes_selecionadas : [];
        foreach ($recs as $rec) {
            $data['has_' . $rec] = true;
        }

        // 4. Mesclar Dados Específicos Adicionais
        $dadosEspecificos = is_array($this->dados_especificos) ? $this->dados_especificos : [];
        $data = array_merge($data, $dadosEspecificos);

        return $data;
    }
}
