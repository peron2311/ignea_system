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
                            <label class="precision-label">Altura (m)</label>
                            <input type="number" step="0.01" name="altura" x-model="form.altura" class="precision-input">
                        </div>
                        <div>
                            <label class="precision-label">Pavimentos</label>
                            <input type="number" name="num_pavimentos" x-model="form.num_pavimentos" class="precision-input">
                        </div>
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
            </form>
        </div>
    </div>
</x-app-layout>
