<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $this->prepareData($request);
        $project = Project::create($data);

        return redirect()->route('projects.documents', $project)
                         ->with('success', 'Projeto criado com sucesso!');
    }

    public function edit(Project $project)
    {
        $initialForm = [
            'codigo_interno' => $project->codigo_interno,
            'nome_obra' => $project->nome_obra,
            'mes_ano' => $project->mes_ano,
            'cidade_analise_bombeiros' => $project->cidade_analise_bombeiros,
            'nome_proprietario' => $project->nome_proprietario,
            'tipo_documento' => $project->tipo_documento ?: 'CNPJ',
            'cpf_cnpj' => $project->cpf_cnpj,
            'endereco' => $project->endereco,
            'cidade' => $project->cidade ?: 'Cascavel',
            'estado' => $project->estado ?: 'PR',
            'area_total' => $project->area_total,
            'altura' => $project->altura,
            'num_pavimentos' => $project->num_pavimentos,
            'pne' => (bool)$project->pne,
            'ocupacoes' => $project->ocupacoes ?: [],
            'medidas_selecionadas' => $project->medidas_selecionadas ?: [],
            'recomendacoes_selecionadas' => $project->recomendacoes_selecionadas ?: [],
            'sdai_componentes' => $project->sdai_componentes ?: [],
            'dados_especificos' => $project->dados_especificos ?: [
                'rt_nome' => $project->rt_nome ?: 'Eng. Ana Julia Zunta Carniel',
                'rt_crea' => $project->rt_crea ?: 'CREA-PR 168.913/D',
                'rt_email' => $project->rt_email,
                'rt_telefone' => $project->rt_telefone,
                'area_fria' => $project->area_fria_m2 ?: 0
            ]
        ];

        return view('projects.edit', compact('project', 'initialForm'));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $data = $this->prepareData($request);
        
        // Usar fill e save para garantir que o Eloquent detecte mudanças em campos JSON/Array
        $project->fill($data);
        $project->save();

        return redirect()->route('projects.edit', $project)
                         ->with('success', 'Projeto atualizado com sucesso!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projeto excluído com sucesso.');
    }

    public function documents(Project $project)
    {
        return view('projects.documents', compact('project'));
    }

    private function prepareData(Request $request): array
    {
        // Pega todos os dados brutos do request
        $data = $request->all();

        // 1. Campos que chegam do assistente (podem vir como JSON String ou Array)
        $jsonFields = [
            'ocupacoes', 
            'medidas_selecionadas', 
            'recomendacoes_selecionadas', 
            'dados_especificos',
            'sdai_componentes'
        ];

        foreach ($jsonFields as $field) {
            $val = $request->input($field);
            
            if (is_string($val) && !empty($val)) {
                $decoded = json_decode($val, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$field] = $decoded;
                }
            } elseif (is_array($val)) {
                $data[$field] = $val;
            }
        }

        // 2. Sincronizar o array 'dados_especificos'
        // Ele deve conter uma cópia de todos os campos técnicos para persistência híbrida
        $currentDados = $data['dados_especificos'] ?? [];
        if (is_string($currentDados)) {
            $currentDados = json_decode($currentDados, true) ?? [];
        }
        
        $technicalFields = [
            'rt_nome', 'rt_crea', 'rt_email', 'rt_telefone',
            'nome_signatario', 'cpf_signatario', 'rg_signatario',
            'cidade_analise_bombeiros', 'mes_ano', 'endereco', 'cidade', 'estado', 'cpf_cnpj', 'tipo_documento',
            'area_total', 'altura', 'num_pavimentos',
            'estrutura', 'cobertura', 'forro', 'pisos', 'esquadrias', 'divisao_interna', 'via_acesso',
            'sdai_tipo_sistema', 'sdai_topologia', 'sdai_local_central',
            'populacao_total', 'carga_horaria_brigada', 'tipo_brigadista', 'num_brigadistas', 'num_brigadistas_organicos', 'num_brigadistas_profissionais',
            'hidrante_vazao_dimensionamento', 'hidrante_pressao_minima', 'hidrante_dn_esguicho', 'bomba_marca', 'bomba_modelo', 'bomba_potencia_cv', 'reservatorio_volume',
            'glp_tipo_cilindro', 'glp_num_cilindros', 'gerador_combustivel', 'gerador_capacidade_litros'
        ];

        foreach ($technicalFields as $field) {
            // Tenta pegar do request direto (input com name) ou do array dados_especificos que veio no JSON
            $val = $request->input($field) ?? ($currentDados[$field] ?? null);
            
            if ($val !== null) {
                $currentDados[$field] = $val;
                // Garante que o campo também esteja no nível superior para salvar na coluna do banco
                $data[$field] = $val;
            }
        }
        
        // Mapeamento especial para area_fria
        if ($request->has('area_fria')) {
            $areaFriaVal = $request->input('area_fria');
            $currentDados['area_fria'] = $areaFriaVal;
            $data['area_fria_m2'] = $areaFriaVal;
            $data['area_fria'] = (float)$areaFriaVal > 0;
        }
        
        $data['dados_especificos'] = $currentDados;

        // 3. Garantir que campos booleanos sejam salvos como booleanos
        $booleans = [
            'tem_glp', 'tem_gerador', 'tem_subestacao', 'pne', 
            'edificacao_residencial', 'edificacao_aluguel', 
            'porta_correr_saida_emergencia', 'edificacao_existente', 'substituicao_projeto',
            'sdai_enderecavel', 'sdai_nota3_cscip', 'sdai_tem_damper', 'chuveiro_tem_camara_fria', 'chuveiro_ul_fm'
        ];
        
        foreach ($booleans as $boolField) {
            if ($request->has($boolField)) {
                $data[$boolField] = filter_var($request->input($boolField), FILTER_VALIDATE_BOOLEAN);
            }
        }

        // 4. Limpeza final: remover campos internos do Laravel e campos vazios que não devem ser null
        unset($data['_token'], $data['_method']);

        return $data;
    }
}
