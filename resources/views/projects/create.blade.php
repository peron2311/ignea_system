<x-app-layout>
    <x-slot name="header">
        <div class="precision-container pt-0 pb-0">
            <h2 class="precision-heading-3">
                {{ __('Novo Projeto PCI') }}
            </h2>
        </div>
    </x-slot>

    <div class="precision-container pt-12" x-data="{
        step: 1,
        totalSteps: 7,
        form: {
            codigo_interno: '',
            nome_obra: '',
            mes_ano: '{{ date('m/Y') }}',
            nome_proprietario: '',
            tipo_documento: 'CNPJ',
            cpf_cnpj: '',
            endereco: '',
            inscricao_imobiliaria: '',
            cidade: 'Cascavel',
            estado: 'PR',
            area_total: '',
            altura: '',
            num_pavimentos: '',
            ocupacoes: [],
            medidas_selecionadas: [],
            tem_glp: false,
            tem_gerador: false,
            tem_subestacao: false,
            pne: false,
            edificacao_residencial: false,
            edificacao_aluguel: false,
            porta_correr_saida_emergencia: false,
            edificacao_existente: false,
            substituicao_projeto: false,
            recomendacoes_selecionadas: [],
            dados_especificos: {},
        },
        addOcupacao() {
            this.form.ocupacoes.push({ divisao: 'F-1', area: '', ci: 300 });
        },
        removeOcupacao(i) {
            this.form.ocupacoes.splice(i, 1);
        },
        toggleMedida(key) {
            const idx = this.form.medidas_selecionadas.indexOf(key);
            if (idx === -1) this.form.medidas_selecionadas.push(key);
            else this.form.medidas_selecionadas.splice(idx, 1);
        },
        toggleRecomendacao(key) {
            const idx = this.form.recomendacoes_selecionadas.indexOf(key);
            if (idx === -1) this.form.recomendacoes_selecionadas.push(key);
            else this.form.recomendacoes_selecionadas.splice(idx, 1);
        },
        submit() {
            $refs.mainForm.submit();
        }
    }">
        <div class="precision-card max-w-4xl mx-auto">
            <!-- Progress Header -->
            <div class="precision-card-header mb-8 border-none">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="precision-heading-3" x-text="
                            step === 1 ? 'Identificação' : 
                            step === 2 ? 'Proprietário' : 
                            step === 3 ? 'Localização' : 
                            step === 4 ? 'Edificação' : 
                            step === 5 ? 'Segurança' : 
                            step === 6 ? 'Sistemas' : 'Finalização'
                        "></h3>
                        <p class="precision-caption mt-1">Preencha os dados técnicos para gerar os documentos.</p>
                    </div>
                    <div class="text-right">
                        <span class="precision-heading-3 text-blue-600" x-text="step"></span>
                        <span class="precision-heading-5 text-gray-300">/ <span x-text="totalSteps"></span></span>
                    </div>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500 ease-out" :style="'width: ' + (step/totalSteps)*100 + '%'"></div>
                </div>
            </div>

            <form action="{{ route('projects.store') }}" method="POST" x-ref="mainForm" class="space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="precision-alert precision-alert-error">
                        <i class="fas fa-exclamation-circle text-2xl"></i>
                        <div>
                            <ul class="list-disc ml-4 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                
                <!-- Step 1: Identificação -->
                <div x-show="step === 1" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-2 precision-gap-16">
                        <div>
                            <label class="precision-label">Código Interno</label>
                            <input type="text" name="codigo_interno" x-model="form.codigo_interno" class="precision-input" placeholder="Ex: PCI-2024-001" required>
                        </div>
                        <div>
                            <label class="precision-label">Nome da Obra</label>
                            <input type="text" name="nome_obra" x-model="form.nome_obra" class="precision-input" placeholder="Ex: Residencial Aurora" required>
                        </div>
                        <div>
                            <label class="precision-label">Mês/Ano</label>
                            <input type="text" name="mes_ano" x-model="form.mes_ano" class="precision-input">
                        </div>
                        <div>
                            <label class="precision-label">Cidade (Análise Bombeiros)</label>
                            <input type="text" name="cidade_analise_bombeiros" class="precision-input" placeholder="Cascavel">
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-4">Responsável Técnico</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="precision-label">Nome do RT</label>
                                <input type="text" name="rt_nome" x-model="form.dados_especificos.rt_nome" class="precision-input" placeholder="Ex: Eng. Ana Julia">
                            </div>
                            <div>
                                <label class="precision-label">CREA/CAU</label>
                                <input type="text" name="rt_crea" x-model="form.dados_especificos.rt_crea" class="precision-input" placeholder="Ex: 168.913/D">
                            </div>
                            <div>
                                <label class="precision-label">E-mail</label>
                                <input type="email" name="rt_email" x-model="form.dados_especificos.rt_email" class="precision-input" placeholder="email@exemplo.com">
                            </div>
                            <div>
                                <label class="precision-label">Telefone</label>
                                <input type="text" name="rt_telefone" x-model="form.dados_especificos.rt_telefone" class="precision-input" placeholder="(00) 00000-0000">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-4">Condições Especiais da Edificação</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                <input type="checkbox" name="edificacao_existente" x-model="form.edificacao_existente" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Edificação Existente (Gera Comprovação)</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                <input type="checkbox" name="substituicao_projeto" x-model="form.substituicao_projeto" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Substituição de Projeto (Gera Requerimento)</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                <input type="checkbox" name="edificacao_residencial" x-model="form.edificacao_residencial" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Edifício Residencial (Gera Termo Síndico)</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                <input type="checkbox" name="edificacao_aluguel" x-model="form.edificacao_aluguel" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Para Aluguel (Gera Termo Inquilino)</span>
                            </label>
                            <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-100">
                                <input type="checkbox" name="porta_correr_saida_emergencia" x-model="form.porta_correr_saida_emergencia" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                <span class="text-sm font-medium text-gray-700">Portas de Correr (Gera Termo de Responsabilidade)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Proprietário -->
                <div x-show="step === 2" x-transition>
                    <div class="space-y-4">
                        <div>
                            <label class="precision-label">Nome do Proprietário/Empresa</label>
                            <input type="text" name="nome_proprietario" x-model="form.nome_proprietario" class="precision-input">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 precision-gap-16">
                            <div>
                                <label class="precision-label">Tipo de Documento</label>
                                <select name="tipo_documento" x-model="form.tipo_documento" class="precision-input">
                                    <option value="CNPJ">CNPJ</option>
                                    <option value="CPF">CPF</option>
                                </select>
                            </div>
                            <div>
                                <label class="precision-label">Número do Documento</label>
                                <input type="text" name="cpf_cnpj" x-model="form.cpf_cnpj" class="precision-input">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-4">Dados do Signatário (Termos)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="precision-label">Nome do Signatário</label>
                                <input type="text" name="nome_signatario" x-model="form.dados_especificos.nome_signatario" class="precision-input" placeholder="Quem assina os termos">
                            </div>
                            <div>
                                <label class="precision-label">CPF</label>
                                <input type="text" name="cpf_signatario" x-model="form.dados_especificos.cpf_signatario" class="precision-input">
                            </div>
                            <div>
                                <label class="precision-label">RG</label>
                                <input type="text" name="rg_signatario" x-model="form.dados_especificos.rg_signatario" class="precision-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Endereço -->
                <div x-show="step === 3" x-transition>
                    <div class="space-y-4">
                        <div>
                            <label class="precision-label">Endereço Completo</label>
                            <input type="text" name="endereco" x-model="form.endereco" class="precision-input">
                        </div>
                        <div>
                            <label class="precision-label">Indicação Fiscal/Inscrição Imobiliária</label>
                            <input type="text" name="inscricao_imobiliaria" x-model="form.inscricao_imobiliaria" class="precision-input">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 precision-gap-16">
                            <div>
                                <label class="precision-label">Cidade</label>
                                <input type="text" name="cidade" x-model="form.cidade" class="precision-input">
                            </div>
                            <div>
                                <label class="precision-label">Estado</label>
                                <input type="text" name="estado" x-model="form.estado" class="precision-input">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Edificação & Ocupações -->
                <div x-show="step === 4" x-transition>
                    <div class="grid grid-cols-1 md:grid-cols-3 precision-gap-16 precision-mb-24">
                        <div>
                            <label class="precision-label">Área Total (m²)</label>
                            <input type="number" step="0.01" name="area_total" x-model="form.area_total" class="precision-input">
                        </div>
                        <div>
                            <label class="precision-label">Área Fria (m²)</label>
                            <input type="number" step="0.01" name="area_fria" x-model="form.dados_especificos.area_fria" class="precision-input" placeholder="Ex: Piscinas, etc.">
                        </div>
                        <div>
                            <label class="precision-label">Área Protegida (m²)</label>
                            <div class="precision-input bg-gray-50 flex items-center" x-text="(form.area_total - (form.dados_especificos.area_fria || 0)).toFixed(2)"></div>
                        </div>
                        <div>
                            <label class="precision-label">Altura (m)</label>
                            <input type="number" step="0.01" name="altura" x-model="form.altura" class="precision-input">
                        </div>
                        <div>
                            <label class="precision-label">Pavimentos</label>
                            <input type="number" name="num_pavimentos" x-model="form.num_pavimentos" class="precision-input">
                        </div>
                        <div>
                            <label class="precision-label">Via de Acesso</label>
                            <input type="text" x-model="form.dados_especificos.via_acesso" class="precision-input" placeholder="Ex: Estrada Alto Salvador">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <div><label class="precision-label">Estrutura</label><input type="text" x-model="form.dados_especificos.estrutura" class="precision-input" placeholder="Ex: Concreto Armado"></div>
                        <div><label class="precision-label">Cobertura</label><input type="text" x-model="form.dados_especificos.cobertura" class="precision-input" placeholder="Ex: Telha metálica"></div>
                        <div><label class="precision-label">Forro</label><input type="text" x-model="form.dados_especificos.forro" class="precision-input" placeholder="Ex: Gesso, Madeira"></div>
                        <div><label class="precision-label">Pisos</label><input type="text" x-model="form.dados_especificos.pisos" class="precision-input" placeholder="Ex: Cerâmica e concreto"></div>
                        <div><label class="precision-label">Esquadrias</label><input type="text" x-model="form.dados_especificos.esquadrias" class="precision-input" placeholder="Ex: Alumínio e vidro"></div>
                        <div><label class="precision-label">Divisão Interna</label><input type="text" x-model="form.dados_especificos.divisao_interna" class="precision-input" placeholder="Ex: Alvenaria"></div>
                    </div>

                    <div class="p-6 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="precision-flex-between mb-4">
                            <h4 class="font-bold text-gray-800">Ocupações Detalhadas</h4>
                            <button type="button" @click="addOcupacao" class="precision-btn precision-btn-secondary precision-btn-sm"><i class="fas fa-plus"></i> Adicionar</button>
                        </div>
                        <div class="space-y-3">
                            <template x-for="(ocup, index) in form.ocupacoes" :key="index">
                                <div class="flex gap-4 items-center">
                                    <div class="flex-1">
                                        <input type="text" x-model="ocup.divisao" class="precision-input" placeholder="Divisão (Ex: F-1)">
                                    </div>
                                    <div class="flex-1">
                                        <input type="number" x-model="ocup.area" class="precision-input" placeholder="Área (m²)">
                                    </div>
                                    <button type="button" @click="removeOcupacao(index)" class="precision-btn precision-btn-ghost text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </template>
                            <div x-show="form.ocupacoes.length === 0" class="text-center py-4 text-gray-400 text-sm">Nenhuma ocupação adicionada.</div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Medidas de Segurança -->
                <div x-show="step === 5" x-transition>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $medidas = [
                                'acesso_viaturas' => 'Acesso de Viaturas',
                                'separacao_edificacoes' => 'Separação entre Edificações',
                                'resistencia_fogo' => 'Resistência ao Fogo',
                                'compartimentacao' => 'Compartimentação',
                                'controle_materiais' => 'Controle de Materiais',
                                'saidas_emergencia' => 'Saídas de Emergência',
                                'elevador_emergencia' => 'Elevador de Emergência',
                                'brigada_incendio' => 'Brigada de Incêndio',
                                'iluminacao_emergencia' => 'Iluminação de Emergência',
                                'deteccao_alarme' => 'Detecção e Alarme',
                                'sinalizacao_emergencia' => 'Sinalização de Emergência',
                                'extintores' => 'Extintores',
                                'hidrantes' => 'Hidrantes e Mangotinhos',
                                'chuveiros_automaticos' => 'Chuveiros Automáticos',
                                'plano_emergencia' => 'Plano de Emergência',
                            ];
                        @endphp
                        @foreach($medidas as $key => $label)
                            <div @click="toggleMedida('{{ $key }}')" 
                                 :class="form.medidas_selecionadas.includes('{{ $key }}') ? 'bg-blue-50 text-blue-600 border-blue-500 ring-1 ring-blue-500' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100'"
                                 class="p-4 rounded-xl border cursor-pointer transition-all duration-200 text-sm font-semibold flex items-center justify-center text-center">
                                {{ $label }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Step 6: Sistemas Específicos -->
                <div x-show="step === 6" x-transition>
                    <div class="space-y-6">
                        <!-- Chaves Gerais (Sempre Visíveis) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-6 bg-gray-50 rounded-xl border border-gray-200 flex flex-col justify-between space-y-4">
                                <div class="precision-flex gap-3">
                                    <div class="icon-wrapper" style="background-color: #fffbeb;">
                                        <i class="fas fa-fire text-amber-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Central GLP</h4>
                                    </div>
                                </div>
                                <div class="precision-flex-between">
                                    <span class="text-sm font-semibold text-gray-500" x-text="form.tem_glp ? 'Ativo' : 'Inativo'"></span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="tem_glp" x-model="form.tem_glp" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                                    </label>
                                </div>
                                <div x-show="form.tem_glp" x-transition class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="precision-label">Tipo Cilindro</label>
                                            <input type="text" x-model="form.dados_especificos.glp_tipo_cilindro" class="precision-input" placeholder="Ex: P-45 kg">
                                        </div>
                                        <div>
                                            <label class="precision-label">Quantidade</label>
                                            <input type="number" x-model="form.dados_especificos.glp_num_cilindros" class="precision-input">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-gray-50 rounded-xl border border-gray-200 flex flex-col justify-between space-y-4">
                                <div class="precision-flex gap-3">
                                    <div class="icon-wrapper bg-blue-50">
                                        <i class="fas fa-bolt text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900">Gerador</h4>
                                    </div>
                                </div>
                                <div class="precision-flex-between">
                                    <span class="text-sm font-semibold text-gray-500" x-text="form.tem_gerador ? 'Ativo' : 'Inativo'"></span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="tem_gerador" x-model="form.tem_gerador" class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-blue-600 after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div>
                                    </label>
                                </div>
                                <div x-show="form.tem_gerador" x-transition class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="precision-label">Combustível</label>
                                            <input type="text" x-model="form.dados_especificos.gerador_combustivel" class="precision-input" placeholder="Ex: Diesel">
                                        </div>
                                        <div>
                                            <label class="precision-label">Capacidade (L)</label>
                                            <input type="number" x-model="form.dados_especificos.gerador_capacidade_litros" class="precision-input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Painéis Dinâmicos Baseados nas Medidas Selecionadas -->
                        <div x-show="form.medidas_selecionadas.includes('hidrantes')" x-transition class="p-6 bg-white rounded-xl border border-blue-200 shadow-sm">
                            <h4 class="font-bold text-blue-900 mb-4 border-b pb-2"><i class="fas fa-fire-extinguisher mr-2"></i>Hidrantes e Mangotinhos</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><label class="precision-label">Vazão (l/min)</label><input type="text" x-model="form.dados_especificos.hidrante_vazao_dimensionamento" class="precision-input"></div>
                                <div><label class="precision-label">Pressão (mca)</label><input type="text" x-model="form.dados_especificos.hidrante_pressao_minima" class="precision-input"></div>
                                <div><label class="precision-label">DN Esguicho (mm)</label><input type="text" x-model="form.dados_especificos.hidrante_dn_esguicho" class="precision-input"></div>
                                <div><label class="precision-label">Bomba - Marca</label><input type="text" x-model="form.dados_especificos.bomba_marca" class="precision-input"></div>
                                <div><label class="precision-label">Bomba - Modelo</label><input type="text" x-model="form.dados_especificos.bomba_modelo" class="precision-input"></div>
                                <div><label class="precision-label">Bomba - CV</label><input type="text" x-model="form.dados_especificos.bomba_potencia_cv" class="precision-input"></div>
                                <div><label class="precision-label">Reservatório (m³)</label><input type="text" x-model="form.dados_especificos.reservatorio_volume" class="precision-input"></div>
                                <div><label class="precision-label">Lances Mangueira Int.</label><input type="text" x-model="form.dados_especificos.hidrante_lances_internos" class="precision-input"></div>
                                <div><label class="precision-label">Lances Mangueira Ext.</label><input type="text" x-model="form.dados_especificos.hidrante_lances_externos" class="precision-input"></div>
                            </div>
                        </div>

                        <div x-show="form.medidas_selecionadas.includes('deteccao_alarme')" x-transition class="p-6 bg-white rounded-xl border border-red-200 shadow-sm">
                            <h4 class="font-bold text-red-900 mb-4 border-b pb-2"><i class="fas fa-bell mr-2"></i>Detecção e Alarme (SDAI)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div><label class="precision-label">Tipo do Sistema</label><input type="text" x-model="form.dados_especificos.sdai_tipo_sistema" class="precision-input" placeholder="Ex: endereçável"></div>
                                <div><label class="precision-label">Topologia</label><input type="text" x-model="form.dados_especificos.sdai_topologia" class="precision-input" placeholder="Ex: Classe A"></div>
                                <div><label class="precision-label">Local da Central</label><input type="text" x-model="form.dados_especificos.sdai_local_central" class="precision-input" placeholder="Ex: Portaria"></div>
                            </div>
                            <div>
                                <label class="precision-label">Componentes do Sistema</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach(['Central', 'Detectores Ópticos', 'Detectores Termovelocimétricos', 'Acionadores Manuais', 'Sinalizadores Audiovisuais', 'Módulos de Isolamento', 'Repetidoras'] as $comp)
                                        <label class="flex items-center gap-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                                            <input type="checkbox" @change="
                                                if(!form.dados_especificos.sdai_componentes) form.dados_especificos.sdai_componentes = [];
                                                const idx = form.dados_especificos.sdai_componentes.indexOf('{{ $comp }}');
                                                if(idx === -1) form.dados_especificos.sdai_componentes.push('{{ $comp }}');
                                                else form.dados_especificos.sdai_componentes.splice(idx, 1);
                                            " :checked="form.dados_especificos.sdai_componentes && form.dados_especificos.sdai_componentes.includes('{{ $comp }}')" class="rounded text-red-600">
                                            <span class="text-xs">{{ $comp }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div x-show="form.medidas_selecionadas.includes('brigada_incendio')" x-transition class="p-6 bg-white rounded-xl border border-orange-200 shadow-sm">
                            <h4 class="font-bold text-orange-900 mb-4 border-b pb-2"><i class="fas fa-users mr-2"></i>Brigada de Incêndio</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div><label class="precision-label">Tipo Brigadista</label><input type="text" x-model="form.dados_especificos.tipo_brigadista" class="precision-input" placeholder="Ex: orgânicos"></div>
                                <div><label class="precision-label">População Total</label><input type="number" x-model="form.dados_especificos.populacao_total" class="precision-input"></div>
                                <div>
                                    <label class="precision-label">População Exposta (1,3x)</label>
                                    <div class="precision-input bg-gray-50 flex items-center" x-text="Math.ceil((form.dados_especificos.populacao_total || 0) * 1.3)"></div>
                                </div>
                                <div><label class="precision-label">Brigadistas Orgânicos</label><input type="number" x-model="form.dados_especificos.num_brigadistas_organicos" class="precision-input"></div>
                                <div><label class="precision-label">Brigadistas Profissionais</label><input type="number" x-model="form.dados_especificos.num_brigadistas_profissionais" class="precision-input"></div>
                                <div><label class="precision-label">Número Final</label><input type="number" x-model="form.dados_especificos.num_brigadistas" class="precision-input"></div>
                                <div><label class="precision-label">Carga Horária (h)</label><input type="number" x-model="form.dados_especificos.carga_horaria_brigada" class="precision-input"></div>
                                <div><label class="precision-label">Riscos Específicos</label><input type="text" x-model="form.dados_especificos.riscos_especificos_brigada" class="precision-input" placeholder="Ex: Espaço Confinado"></div>
                            </div>
                        </div>

                        <div x-show="form.medidas_selecionadas.includes('isolamento_risco')" x-transition class="p-6 bg-white rounded-xl border border-orange-200 shadow-sm">
                            <h4 class="font-bold text-orange-900 mb-4 border-b pb-2"><i class="fas fa-arrows-alt-h mr-2"></i>Isolamento de Risco (NPT 007)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div><label class="precision-label">Distância Existente (m)</label><input type="text" x-model="form.dados_especificos.iso_distancia_existente" class="precision-input"></div>
                                <div><label class="precision-label">Distância Mínima Requerida (m)</label><input type="text" x-model="form.dados_especificos.iso_distancia_minima" class="precision-input"></div>
                                <div><label class="precision-label">Área de Aberturas Edif. 1 (m²)</label><input type="text" x-model="form.dados_especificos.iso_ed1_aberturas" class="precision-input"></div>
                            </div>
                        </div>
                        
                        <div x-show="form.medidas_selecionadas.includes('plano_emergencia')" x-transition class="p-6 bg-white rounded-xl border border-emerald-200 shadow-sm">
                            <h4 class="font-bold text-emerald-900 mb-4 border-b pb-2"><i class="fas fa-file-medical-alt mr-2"></i>Plano de Emergência (PNE)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><label class="precision-label">Horário de Funcionamento</label><input type="text" x-model="form.dados_especificos.horario_funcionamento" class="precision-input" placeholder="Ex: 8h às 22h"></div>
                                <div><label class="precision-label">Características do Entorno</label><input type="text" x-model="form.dados_especificos.caracteristicas_entorno" class="precision-input" placeholder="Ex: Baixa concentração"></div>
                                <div><label class="precision-label">Distância Bombeiros (km)</label><input type="text" x-model="form.dados_especificos.distancia_bombeiros_km" class="precision-input"></div>
                                <div><label class="precision-label">Endereço Bombeiros</label><input type="text" x-model="form.dados_especificos.endereco_bombeiros" class="precision-input"></div>
                                <div><label class="precision-label">Nome do Hospital Base</label><input type="text" x-model="form.dados_especificos.hospital_nome" class="precision-input"></div>
                                <div><label class="precision-label">Endereço Hospital Base</label><input type="text" x-model="form.dados_especificos.hospital_endereco" class="precision-input"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-4">Recomendações do Manual Técnico</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @php
                                $recomendacoes = [
                                    'rec_acesso' => 'Recomendações de Acesso de Viaturas',
                                    'rec_separacao' => 'Recomendações de Separação entre Edificações',
                                    'rec_estrutural' => 'Recomendações de Segurança Estrutural',
                                    'rec_compartimentacao' => 'Recomendações de Compartimentação',
                                    'rec_materiais' => 'Recomendações de Controle de Materiais (CMAR)',
                                    'rec_saidas' => 'Recomendações de Saídas de Emergência',
                                    'rec_elevador' => 'Recomendações de Elevador de Emergência',
                                    'rec_brigada' => 'Recomendações de Brigada de Incêndio',
                                    'rec_iluminacao' => 'Recomendações de Iluminação de Emergência',
                                    'rec_alarme' => 'Recomendações de Detecção e Alarme',
                                    'rec_sinalizacao' => 'Recomendações de Sinalização de Emergência',
                                    'rec_extintores' => 'Recomendações de Extintores de Incêndio',
                                    'rec_hidrantes' => 'Recomendações de Hidrantes e Mangotinhos',
                                    'rec_chuveiros' => 'Recomendações de Chuveiros Automáticos',
                                    'rec_controle_fumaca' => 'Recomendações de Controle de Fumaça',
                                    'rec_liquidos' => 'Recomendações de Líquidos e Gases Inflamáveis'
                                ];
                            @endphp
                            @foreach($recomendacoes as $key => $label)
                                <label class="flex items-center gap-3 p-3 bg-white rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="checkbox" @change="toggleRecomendacao('{{ $key }}')" :checked="form.recomendacoes_selecionadas.includes('{{ $key }}')" class="w-5 h-5 rounded text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Step 7: Finalização -->
                <div x-show="step === 7" x-transition>
                    <div class="text-center py-8 space-y-6">
                        <div class="icon-wrapper mx-auto" style="background-color: #ecfdf5; width: 80px; height: 80px;">
                            <i class="fas fa-check text-emerald-500 text-4xl"></i>
                        </div>
                        <div>
                            <h4 class="precision-heading-3 mb-2">Tudo pronto!</h4>
                            <p class="precision-body text-gray-500">Seu projeto foi configurado com sucesso. Clique no botão abaixo para salvar.</p>
                        </div>
                        <div class="p-4 bg-blue-50 rounded-xl inline-flex items-center gap-3 border border-blue-100">
                            <input type="checkbox" name="pne" x-model="form.pne" class="w-5 h-5 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm font-semibold text-blue-900">Gerar Plano de Emergência automaticamente?</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="pt-6 mt-6 border-t border-gray-100 flex justify-between">
                    <div class="flex gap-2">
                        <a href="{{ route('projects.index') }}" class="precision-btn precision-btn-ghost">Cancelar</a>
                        <button type="button" x-show="step > 1" @click="step--" class="precision-btn precision-btn-secondary">Anterior</button>
                    </div>
                    
                    <button type="button" x-show="step < totalSteps" @click="step++" class="precision-btn precision-btn-primary">Próximo</button>
                    <button type="button" x-show="step === totalSteps" @click="submit" class="precision-btn precision-btn-primary" style="background-color: var(--color-success);">Criar Projeto PCI</button>
                </div>

                <!-- Hidden JSON fields -->
                <input type="hidden" name="ocupacoes" :value="JSON.stringify(form.ocupacoes)">
                <input type="hidden" name="medidas_selecionadas" :value="JSON.stringify(form.medidas_selecionadas)">
                <input type="hidden" name="recomendacoes_selecionadas" :value="JSON.stringify(form.recomendacoes_selecionadas)">
                <input type="hidden" name="dados_especificos" :value="JSON.stringify(form.dados_especificos)">
            </form>
        </div>
    </div>
</x-app-layout>
