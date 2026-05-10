<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-800 leading-tight">
                {{ __('Editar Projeto PCI: ') }} <span class="text-primary-600">{{ $project->codigo_interno }}</span>
            </h2>
            <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-semibold">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="projectWizard">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 shadow-md">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 shadow-md">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="precision-card max-w-4xl mx-auto shadow-2xl bg-white rounded-xl overflow-hidden border border-gray-100">
                <!-- Progress Header -->
                <div class="bg-gray-50 border-b border-gray-200 p-6">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 w-full h-1 bg-gray-200 -translate-y-1/2 z-0"></div>
                        <div class="absolute left-0 top-1/2 h-1 bg-primary-500 -translate-y-1/2 z-0 transition-all duration-500" :style="'width: ' + ((step-1)/6 * 100) + '%'"></div>
                        
                        <template x-for="i in 7">
                            <div class="relative z-10 flex flex-col items-center">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border-2"
                                     :class="step >= i ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white border-gray-300 text-gray-400'">
                                    <span x-text="i" class="font-bold"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="flex justify-between mt-2 px-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <span>Identificação</span>
                        <span>Proprietário</span>
                        <span>Endereço</span>
                        <span>Edificação</span>
                        <span>Segurança</span>
                        <span>Sistemas</span>
                        <span>Finalizar</span>
                    </div>
                </div>

                <form action="{{ route('projects.update', $project) }}" method="POST" x-ref="mainForm" id="editProjectForm">
                    @csrf
                    @method('PATCH')

                    <!-- Campos ocultos para arrays JSON que são sincronizados no submit -->
                    <input type="hidden" name="ocupacoes" value="">
                    <input type="hidden" name="medidas_selecionadas" value="">
                    <input type="hidden" name="recomendacoes_selecionadas" value="">
                    <input type="hidden" name="dados_especificos" value="">
                    <input type="hidden" name="sdai_componentes" value="">

                    <!-- STEP 1: Identificação -->
                    <div x-show="step === 1" x-transition.opacity class="p-8 space-y-6">
                        <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Passo 1: Identificação do Projeto</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código Interno</label>
                                <input type="text" name="codigo_interno" x-model="form.codigo_interno" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Obra</label>
                                <input type="text" name="nome_obra" x-model="form.nome_obra" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mês/Ano</label>
                                <input type="text" name="mes_ano" x-model="form.mes_ano" placeholder="Ex: 05/2026" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade para Análise (Bombeiros)</label>
                                <input type="text" name="cidade_analise_bombeiros" x-model="form.cidade_analise_bombeiros" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <h4 class="font-bold text-gray-800 mb-4">Condições Especiais da Edificação</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="edificacao_existente" x-model="form.edificacao_existente" class="h-5 w-5 text-primary-600 rounded">
                                    <span class="text-sm font-medium text-gray-700">Edificação Existente (Gera Comprovação)</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="substituicao_projeto" x-model="form.substituicao_projeto" class="h-5 w-5 text-primary-600 rounded">
                                    <span class="text-sm font-medium text-gray-700">Substituição de Projeto (Gera Requerimento)</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="edificacao_residencial" x-model="form.edificacao_residencial" class="h-5 w-5 text-primary-600 rounded">
                                    <span class="text-sm font-medium text-gray-700">Edifício Residencial (Gera Termo Síndico)</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="edificacao_aluguel" x-model="form.edificacao_aluguel" class="h-5 w-5 text-primary-600 rounded">
                                    <span class="text-sm font-medium text-gray-700">Para Aluguel (Gera Termo Inquilino)</span>
                                </label>
                                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                    <input type="checkbox" name="porta_correr_saida_emergencia" x-model="form.porta_correr_saida_emergencia" class="h-5 w-5 text-primary-600 rounded">
                                    <span class="text-sm font-medium text-gray-700">Portas de Correr (Gera Termo Responsabilidade)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Proprietário -->
                    <div x-show="step === 2" x-transition.opacity class="p-8 space-y-6">
                        <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Passo 2: Dados do Proprietário</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Proprietário / Razão Social</label>
                                <input type="text" name="nome_proprietario" x-model="form.nome_proprietario" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                                <select name="tipo_documento" x-model="form.tipo_documento" class="w-full border-gray-300 rounded-lg shadow-sm">
                                    <option value="CNPJ">CNPJ</option>
                                    <option value="CPF">CPF</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número do Documento (CPF/CNPJ)</label>
                                <input type="text" name="cpf_cnpj" x-model="form.cpf_cnpj" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg">
                                <h3 class="text-sm font-bold text-blue-800 mb-3">Dados do Signatário (Para Termos e Requerimentos)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-blue-700 uppercase">Nome Completo</label>
                                        <input type="text" name="nome_signatario" x-model="form.dados_especificos.nome_signatario" class="w-full mt-1 border-blue-200 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-blue-700 uppercase">CPF</label>
                                        <input type="text" name="cpf_signatario" x-model="form.dados_especificos.cpf_signatario" class="w-full mt-1 border-blue-200 rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-blue-700 uppercase">RG</label>
                                        <input type="text" name="rg_signatario" x-model="form.dados_especificos.rg_signatario" class="w-full mt-1 border-blue-200 rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Endereço -->
                    <div x-show="step === 3" x-transition.opacity class="p-8 space-y-6">
                        <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Passo 3: Localização da Obra</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Endereço Completo (Rua, Nº, Bairro)</label>
                                <input type="text" name="endereco" x-model="form.endereco" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                                <input type="text" name="cidade" x-model="form.cidade" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <input type="text" name="estado" x-model="form.estado" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Inscrição Imobiliária / Indicação Fiscal</label>
                                <input type="text" name="inscricao_imobiliaria" x-model="form.inscricao_imobiliaria" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Opcional">
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: Edificação & Ocupações -->
                    <div x-show="step === 4" x-transition.opacity class="p-8 space-y-6">
                        <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Passo 4: Edificação e Ocupações</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Área Total (m²)</label>
                                <input type="number" step="0.01" name="area_total" x-model="form.area_total" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Área Fria (m²)</label>
                                <input type="number" step="0.01" name="area_fria" x-model="form.dados_especificos.area_fria" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ex: Piscinas">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Área Protegida (m²)</label>
                                <div class="w-full p-2 bg-gray-50 border rounded-lg text-gray-700 font-bold" x-text="(parseFloat(form.area_total || 0) - parseFloat(form.dados_especificos.area_fria || 0)).toFixed(2)"></div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Altura (m)</label>
                                <input type="number" step="0.01" name="altura" x-model="form.altura" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pavimentos</label>
                                <input type="number" name="num_pavimentos" x-model="form.num_pavimentos" class="w-full border-gray-300 rounded-lg shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Via de Acesso</label>
                                <input type="text" name="via_acesso" x-model="form.dados_especificos.via_acesso" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ex: Estrada Alto Salvador">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                            <div><label class="text-xs font-bold text-gray-500 uppercase">Estrutura</label><input type="text" x-model="form.dados_especificos.estrutura" class="w-full border-gray-300 rounded-lg shadow-sm text-sm"></div>
                            <div><label class="text-xs font-bold text-gray-500 uppercase">Cobertura</label><input type="text" x-model="form.dados_especificos.cobertura" class="w-full border-gray-300 rounded-lg shadow-sm text-sm"></div>
                            <div><label class="text-xs font-bold text-gray-500 uppercase">Forro</label><input type="text" x-model="form.dados_especificos.forro" class="w-full border-gray-300 rounded-lg shadow-sm text-sm"></div>
                            <div><label class="text-xs font-bold text-gray-500 uppercase">Pisos</label><input type="text" x-model="form.dados_especificos.pisos" class="w-full border-gray-300 rounded-lg shadow-sm text-sm"></div>
                            <div><label class="text-xs font-bold text-gray-500 uppercase">Esquadrias</label><input type="text" x-model="form.dados_especificos.esquadrias" class="w-full border-gray-300 rounded-lg shadow-sm text-sm"></div>
                            <div><label class="text-xs font-bold text-gray-500 uppercase">Divisão Interna</label><input type="text" x-model="form.dados_especificos.divisao_interna" class="w-full border-gray-300 rounded-lg shadow-sm text-sm"></div>
                        </div>

                        <div class="mt-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800">Ocupações Detalhadas</h3>
                                <button type="button" @click="addOcupacao()" class="px-3 py-1 bg-primary-600 text-white rounded-md text-sm hover:bg-primary-700">+ Adicionar</button>
                            </div>
                            <div class="space-y-4">
                                <template x-for="(ocup, index) in form.ocupacoes" :key="index">
                                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 flex items-end gap-4">
                                        <div class="flex-1">
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Divisão</label>
                                            <input type="text" x-model="ocup.divisao" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Área (m²)</label>
                                            <input type="number" x-model="ocup.area" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Carga Incêndio</label>
                                            <input type="number" x-model="ocup.ci" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                        </div>
                                        <button type="button" @click="removeOcupacao(index)" class="p-2 text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </template>
                                <div x-show="form.ocupacoes.length === 0" class="text-center py-4 text-gray-400 italic">Nenhuma ocupação adicionada.</div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 5: Sistemas de Segurança -->
                    <div x-show="step === 5" x-transition.opacity class="p-8 space-y-6">
                        <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Passo 5: Medidas de Segurança</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @php
                                $medidas = [
                                    'acesso_viaturas' => 'Acesso de Viaturas',
                                    'separacao_edificios' => 'Separação entre Edifícios',
                                    'seguranca_estrutural' => 'Segurança Estrutural',
                                    'compartimentacao' => 'Compartimentação',
                                    'controle_materiais' => 'Controle de Materiais',
                                    'saidas_emergencia' => 'Saídas de Emergência',
                                    'elevador_emergencia' => 'Elevador de Emergência',
                                    'brigada_incendio' => 'Brigada de Incêndio',
                                    'iluminacao_emergencia' => 'Iluminação de Emergência',
                                    'deteccao_alarme' => 'Detecção e Alarme',
                                    'sinalizacao_emergencia' => 'Sinalização de Emergência',
                                    'extintores' => 'Extintores',
                                    'hidrantes' => 'Hidrantes / Mangotinhos',
                                    'chuveiros_automaticos' => 'Chuveiros Automáticos',
                                    'controle_fumaca' => 'Controle de Fumaça',
                                    'liquidos_inflamaveis' => 'Líquidos Inflamáveis',
                                    'plano_emergencia' => 'Plano de Emergência (PNE)'
                                ];
                            @endphp
                            @foreach($medidas as $key => $label)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
                                       :class="form.medidas_selecionadas.includes('{{ $key }}') ? 'border-primary-500 bg-primary-50' : 'border-gray-200'">
                                    <input type="checkbox" class="hidden" @change="toggleMedida('{{ $key }}')" :checked="form.medidas_selecionadas.includes('{{ $key }}')">
                                    <span class="text-sm font-medium" :class="form.medidas_selecionadas.includes('{{ $key }}') ? 'text-primary-700' : 'text-gray-600'">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase tracking-wider">Recomendações do Manual Técnico</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @php
                                    $recomendacoes = [
                                        'rec_acesso' => 'Recomendações de Acesso de Viaturas',
                                        'rec_separacao' => 'Recomendações de Separação entre Edificações',
                                        'rec_estrutural' => 'Recomendações de Segurança Estrutural',
                                        'rec_compartimentacao' => 'Recomendações de Compartimentação',
                                        'rec_materiais' => 'Recomendações de Controle de Materiais (CMAR)',
                                        'rec_saidas' => 'Recomendações de Saídas de Emergência',
                                        'rec_brigada' => 'Recomendações de Brigada de Incêndio',
                                        'rec_alarme' => 'Recomendações de Detecção e Alarme',
                                        'rec_sinalizacao' => 'Recomendações de Sinalização de Emergência',
                                        'rec_extintores' => 'Recomendações de Extintores de Incêndio',
                                        'rec_hidrantes' => 'Recomendações de Hidrantes e Mangotinhos',
                                        'rec_chuveiros' => 'Recomendações de Chuveiros Automáticos',
                                    ];
                                @endphp
                                @foreach($recomendacoes as $key => $label)
                                    <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                                        <input type="checkbox" @change="toggleRecomendacao('{{ $key }}')" :checked="form.recomendacoes_selecionadas.includes('{{ $key }}')" class="h-5 w-5 text-primary-600 rounded">
                                        <span class="text-xs font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- STEP 6: Sistemas Específicos -->
                    <div x-show="step === 6" x-transition.opacity class="p-8 space-y-6">
                        <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Passo 6: Detalhes dos Sistemas e Técnico</h2>
                        
                        <!-- Chaves Gerais -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="p-6 bg-gray-50 rounded-xl border border-gray-200 flex flex-col justify-between space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <h4 class="font-bold text-gray-900">Central GLP</h4>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-500" x-text="form.tem_glp ? 'Ativo' : 'Inativo'"></span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="tem_glp" x-model="form.tem_glp" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                                    </label>
                                </div>
                                <div x-show="form.tem_glp" x-transition class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-bold text-gray-500">Tipo Cilindro</label>
                                            <input type="text" x-model="form.dados_especificos.glp_tipo_cilindro" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: P-45 kg">
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-gray-500">Quantidade</label>
                                            <input type="number" x-model="form.dados_especificos.glp_num_cilindros" class="w-full border-gray-300 rounded-md text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-gray-50 rounded-xl border border-gray-200 flex flex-col justify-between space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                                        <i class="fas fa-bolt"></i>
                                    </div>
                                    <h4 class="font-bold text-gray-900">Gerador</h4>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-500" x-text="form.tem_gerador ? 'Ativo' : 'Inativo'"></span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="tem_gerador" x-model="form.tem_gerador" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-primary-600 after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                                    </label>
                                </div>
                                <div x-show="form.tem_gerador" x-transition class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-bold text-gray-500">Combustível</label>
                                            <input type="text" x-model="form.dados_especificos.gerador_combustivel" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: Diesel">
                                        </div>
                                        <div>
                                            <label class="text-xs font-bold text-gray-500">Capacidade (L)</label>
                                            <input type="number" x-model="form.dados_especificos.gerador_capacidade_litros" class="w-full border-gray-300 rounded-md text-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Painéis Dinâmicos -->
                        <div x-show="form.medidas_selecionadas.includes('hidrantes')" x-transition class="p-6 bg-white rounded-xl border border-blue-200 shadow-sm mb-6">
                            <h4 class="font-bold text-blue-900 mb-4 border-b pb-2"><i class="fas fa-fire-extinguisher mr-2"></i>Hidrantes e Mangotinhos</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Vazão (l/min)</label><input type="text" x-model="form.dados_especificos.hidrante_vazao_dimensionamento" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Pressão (mca)</label><input type="text" x-model="form.dados_especificos.hidrante_pressao_minima" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">DN Esguicho (mm)</label><input type="text" x-model="form.dados_especificos.hidrante_dn_esguicho" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Bomba - Marca</label><input type="text" x-model="form.dados_especificos.bomba_marca" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Bomba - Modelo</label><input type="text" x-model="form.dados_especificos.bomba_modelo" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Bomba - CV</label><input type="text" x-model="form.dados_especificos.bomba_potencia_cv" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Reservatório (m³)</label><input type="text" x-model="form.dados_especificos.reservatorio_volume" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Lances Mangueira Int.</label><input type="text" x-model="form.dados_especificos.hidrante_lances_internos" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Lances Mangueira Ext.</label><input type="text" x-model="form.dados_especificos.hidrante_lances_externos" class="w-full border-gray-300 rounded-md text-sm"></div>
                            </div>
                        </div>

                        <div x-show="form.medidas_selecionadas.includes('deteccao_alarme')" x-transition.opacity class="p-6 bg-white rounded-xl border border-red-200 shadow-sm mb-6">
                            <h4 class="font-bold text-red-900 mb-4 border-b pb-2"><i class="fas fa-bell mr-2"></i>Detecção e Alarme (SDAI)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Tipo do Sistema</label><input type="text" x-model="form.dados_especificos.sdai_tipo_sistema" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: endereçável"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Topologia</label><input type="text" x-model="form.dados_especificos.sdai_topologia" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: Classe A"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Local da Central</label><input type="text" x-model="form.dados_especificos.sdai_local_central" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: Portaria"></div>
                            </div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Componentes</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2">
                                @foreach(['Central', 'Detectores Ópticos', 'Detectores Termovelocimétricos', 'Acionadores Manuais', 'Sinalizadores Audiovisuais', 'Módulos de Isolamento', 'Repetidoras'] as $comp)
                                    <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" @change="
                                            if(!form.dados_especificos.sdai_componentes) form.dados_especificos.sdai_componentes = [];
                                            const idx = form.dados_especificos.sdai_componentes.indexOf('{{ $comp }}');
                                            if(idx === -1) form.dados_especificos.sdai_componentes.push('{{ $comp }}');
                                            else form.dados_especificos.sdai_componentes.splice(idx, 1);
                                        " :checked="form.dados_especificos.sdai_componentes && form.dados_especificos.sdai_componentes.includes('{{ $comp }}')" class="h-4 w-4 text-red-600 rounded">
                                        <span class="text-xs">{{ $comp }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div x-show="form.medidas_selecionadas.includes('brigada_incendio')" x-transition.opacity class="p-6 bg-white rounded-xl border border-orange-200 shadow-sm mb-6">
                            <h4 class="font-bold text-orange-900 mb-4 border-b pb-2"><i class="fas fa-users mr-2"></i>Brigada de Incêndio</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Tipo Brigadista</label><input type="text" x-model="form.dados_especificos.tipo_brigadista" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: Orgânicos"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">População Total</label><input type="number" x-model="form.dados_especificos.populacao_total" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase">População Exposta (1,3x)</label>
                                    <div class="w-full p-2 bg-gray-50 border rounded-md text-sm font-bold" x-text="Math.ceil((form.dados_especificos.populacao_total || 0) * 1.3)"></div>
                                </div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Brigadistas Orgânicos</label><input type="number" x-model="form.dados_especificos.num_brigadistas_organicos" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Brigadistas Profissionais</label><input type="number" x-model="form.dados_especificos.num_brigadistas_profissionais" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Número Total Final</label><input type="number" x-model="form.dados_especificos.num_brigadistas" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Carga Horária (h)</label><input type="number" x-model="form.dados_especificos.carga_horaria_brigada" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Riscos Específicos</label><input type="text" x-model="form.dados_especificos.riscos_especificos_brigada" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: Espaço Confinado"></div>
                            </div>
                        </div>

                        <div x-show="form.medidas_selecionadas.includes('separacao_edificios') || form.medidas_selecionadas.includes('separacao_edificacoes')" x-transition.opacity class="p-6 bg-white rounded-xl border border-orange-200 shadow-sm mb-6">
                            <h4 class="font-bold text-orange-900 mb-4 border-b pb-2"><i class="fas fa-arrows-alt-h mr-2"></i>Isolamento de Risco (NPT 007)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Distância Existente (m)</label><input type="text" x-model="form.dados_especificos.iso_distancia_existente" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Distância Mínima Requerida (m)</label><input type="text" x-model="form.dados_especificos.iso_distancia_minima" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Área de Aberturas Edif. 1 (m²)</label><input type="text" x-model="form.dados_especificos.iso_ed1_aberturas" class="w-full border-gray-300 rounded-md text-sm"></div>
                            </div>
                        </div>

                        <div x-show="form.medidas_selecionadas.includes('plano_emergencia')" x-transition.opacity class="p-6 bg-white rounded-xl border border-emerald-200 shadow-sm mb-6">
                            <h4 class="font-bold text-emerald-900 mb-4 border-b pb-2"><i class="fas fa-file-medical-alt mr-2"></i>Plano de Emergência (PNE)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Horário de Funcionamento</label><input type="text" x-model="form.dados_especificos.horario_funcionamento" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: 8h às 22h"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Características do Entorno</label><input type="text" x-model="form.dados_especificos.caracteristicas_entorno" class="w-full border-gray-300 rounded-md text-sm" placeholder="Ex: Baixa concentração"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Distância Bombeiros (km)</label><input type="text" x-model="form.dados_especificos.distancia_bombeiros_km" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Endereço Bombeiros</label><input type="text" x-model="form.dados_especificos.endereco_bombeiros" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Nome do Hospital Base</label><input type="text" x-model="form.dados_especificos.hospital_nome" class="w-full border-gray-300 rounded-md text-sm"></div>
                                <div><label class="text-xs font-bold text-gray-500 uppercase">Endereço Hospital Base</label><input type="text" x-model="form.dados_especificos.hospital_endereco" class="w-full border-gray-300 rounded-md text-sm"></div>
                            </div>
                        </div>

                        <!-- Dados do RT -->
                        <div class="mt-8 pt-6 border-t border-gray-100 bg-gray-50 p-6 rounded-xl">
                            <h4 class="font-bold text-gray-800 mb-4"><i class="fas fa-user-tie mr-2"></i>Responsável Técnico</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase">Nome Completo</label>
                                    <input type="text" name="rt_nome" x-model="form.dados_especificos.rt_nome" class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase">CREA/CAU</label>
                                    <input type="text" name="rt_crea" x-model="form.dados_especificos.rt_crea" class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase">E-mail</label>
                                    <input type="email" name="rt_email" x-model="form.dados_especificos.rt_email" class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase">Telefone</label>
                                    <input type="text" name="rt_telefone" x-model="form.dados_especificos.rt_telefone" class="w-full border-gray-300 rounded-lg shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 7: Finalizar -->
                    <div x-show="step === 7" x-transition.opacity class="p-8 text-center space-y-6">
                        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Pronto para Salvar!</h2>
                        <p class="text-gray-600 max-w-md mx-auto">Revise os dados se necessário. Ao clicar abaixo, todas as informações antigas serão sobrepostas pelos novos dados preenchidos.</p>
                        
                        <div class="flex items-center justify-center gap-3 p-4 bg-blue-50 rounded-lg max-w-sm mx-auto border border-blue-100">
                            <input type="checkbox" name="pne" x-model="form.pne" class="h-5 w-5 text-primary-600 rounded">
                            <span class="text-sm font-bold text-blue-900 uppercase">Incluir PNE automaticamente</span>
                        </div>

                        <div class="pt-6">
                            <button type="button" @click="submit()" class="px-8 py-3 bg-primary-600 text-white rounded-xl font-bold text-lg hover:bg-primary-700 shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1">
                                Salvar Alterações
                            </button>
                        </div>
                    </div>

                    <!-- Navigation Footer -->
                    <div class="bg-gray-50 border-t border-gray-200 p-6 flex justify-between">
                        <button type="button" @click="step--" x-show="step > 1" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">Anterior</button>
                        <div x-show="step === 1"></div>
                        <button type="button" @click="step++" x-show="step < 7" class="px-8 py-2 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition-colors shadow-md">Próximo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('projectWizard', () => ({
            step: 1,
            form: @json($initialForm),
            addOcupacao() {
                this.form.ocupacoes.push({ divisao: "F-1", area: "", ci: 300 });
            },
            removeOcupacao(i) {
                this.form.ocupacoes.splice(i, 1);
            },
            toggleMedida(key) {
                if (!Array.isArray(this.form.medidas_selecionadas)) this.form.medidas_selecionadas = [];
                const idx = this.form.medidas_selecionadas.indexOf(key);
                if (idx === -1) this.form.medidas_selecionadas.push(key);
                else this.form.medidas_selecionadas.splice(idx, 1);
            },
            toggleRecomendacao(key) {
                if (!Array.isArray(this.form.recomendacoes_selecionadas)) this.form.recomendacoes_selecionadas = [];
                const idx = this.form.recomendacoes_selecionadas.indexOf(key);
                if (idx === -1) this.form.recomendacoes_selecionadas.push(key);
                else this.form.recomendacoes_selecionadas.splice(idx, 1);
            },
            submit() {
                // Sincroniza os inputs hidden antes do submit
                const formElement = document.getElementById('editProjectForm');
                
                // Atribui os valores JSON às strings para envio
                formElement.querySelector('input[name="ocupacoes"]').value = JSON.stringify(this.form.ocupacoes);
                formElement.querySelector('input[name="medidas_selecionadas"]').value = JSON.stringify(this.form.medidas_selecionadas);
                formElement.querySelector('input[name="recomendacoes_selecionadas"]').value = JSON.stringify(this.form.recomendacoes_selecionadas);
                formElement.querySelector('input[name="dados_especificos"]').value = JSON.stringify(this.form.dados_especificos);
                formElement.querySelector('input[name="sdai_componentes"]').value = JSON.stringify(this.form.sdai_componentes || []);

                this.$nextTick(() => {
                    formElement.submit();
                });
            }
        }));
    });
    </script>

    <style>
        .bg-primary-50 { background-color: #eff6ff; }
        .bg-primary-100 { background-color: #dbeafe; }
        .bg-primary-500 { background-color: #3b82f6; }
        .bg-primary-600 { background-color: #2563eb; }
        .bg-primary-700 { background-color: #1d4ed8; }
        .text-primary-600 { color: #2563eb; }
        .text-primary-700 { color: #1d4ed8; }
        .border-primary-500 { border-color: #3b82f6; }
        .border-primary-600 { border-color: #2563eb; }
        .ring-primary-500 { --tw-ring-color: #3b82f6; }
    </style>
</x-app-layout>
