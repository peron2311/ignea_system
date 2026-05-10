<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('project') instanceof \App\Models\Project 
            ? $this->route('project')->id 
            : $this->route('project');

        return [
            'codigo_interno' => 'required|string|max:50|unique:projects,codigo_interno,' . ($projectId ?: 'NULL'),
            'nome_obra'      => 'required|string|max:255',
            'mes_ano'        => 'nullable|string|max:20',
            'nome_proprietario' => 'nullable|string|max:255',
            'cpf_cnpj'       => 'nullable|string|max:20',
            'cidade'         => 'nullable|string|max:100',
            'estado'         => 'nullable|string|max:2',
            'area_total'     => 'nullable',
            'altura'         => 'nullable',
            'num_pavimentos' => 'nullable',
            'ocupacoes'      => 'nullable',
            'medidas_selecionadas' => 'nullable',
            'recomendacoes_selecionadas' => 'nullable',
            'dados_especificos' => 'nullable',
        ];
    }
}
