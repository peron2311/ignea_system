<x-app-layout>
    <x-slot name="header">
        <div class="precision-container pt-0 pb-0 flex justify-between items-center">
            <h2 class="precision-heading-3">
                {{ __('Documentos: ') . $project->codigo_interno }}
            </h2>
            <a href="{{ route('projects.index') }}" class="precision-btn precision-btn-ghost">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </x-slot>

    <div class="precision-container pt-12">
        <h3 class="precision-heading-4 mb-6 text-gray-800 border-b pb-2">Fase 1: Início do Projeto (Entrada no CBMPR)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 precision-gap-16 mb-12">
            
            <div class="precision-card hover:border-blue-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-blue-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-file-word text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Memorial Descritivo</h4>
                    <p class="precision-caption">Documento base do projeto.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'memorial']) }}" target="_blank" class="precision-btn precision-btn-primary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>

            <div class="precision-card hover:border-blue-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-blue-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-envelope-open-text text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Ofício</h4>
                    <p class="precision-caption">Encaminhamento para análise.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'oficio']) }}" target="_blank" class="precision-btn precision-btn-primary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>

            @php
                $hasPne = in_array('plano_emergencia', $project->medidas_selecionadas ?? []);
                $hasIndustrial = collect($project->ocupacoes)->contains(fn($o) => str_starts_with($o['divisao'] ?? '', 'I'));
            @endphp

            @if($hasIndustrial)
            <div class="precision-card hover:border-blue-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-blue-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-industry text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Memorial Industrial</h4>
                    <p class="precision-caption">Segurança contra incêndio e desastre.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'memorial-industrial']) }}" target="_blank" class="precision-btn precision-btn-primary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>
            @endif

            @if($hasPne)
            <div class="precision-card hover:border-emerald-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper mx-auto" style="background-color: #ecfdf5; width: 64px; height: 64px;">
                        <i class="fas fa-file-medical-alt text-emerald-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Plano de Emergência</h4>
                    <p class="precision-caption">Dimensionado para população e risco.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'plano-emergencia']) }}" target="_blank" class="precision-btn precision-btn-primary w-full mt-4" style="background-color: var(--color-success);">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>
            @endif

            @if($project->edificacao_existente)
            <div class="precision-card hover:border-amber-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper mx-auto" style="background-color: #fffbeb; width: 64px; height: 64px;">
                        <i class="fas fa-building text-amber-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Comprovação de Existência</h4>
                    <p class="precision-caption">Para edificações antigas.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'comprovacao-existencia']) }}" target="_blank" class="precision-btn precision-btn-secondary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>
            @endif

            @if($project->substituicao_projeto)
            <div class="precision-card hover:border-amber-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper mx-auto" style="background-color: #fffbeb; width: 64px; height: 64px;">
                        <i class="fas fa-exchange-alt text-amber-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Requerimento de Substituição</h4>
                    <p class="precision-caption">Para projetos substitutos.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'requerimento-substituicao']) }}" target="_blank" class="precision-btn precision-btn-secondary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>
            @endif
        </div>

        <h3 class="precision-heading-4 mb-6 text-gray-800 border-b pb-2">Fase 2: Término do Projeto (Entrega ao Cliente)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 precision-gap-16 mb-12">
            
            <div class="precision-card hover:border-purple-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-purple-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-book text-purple-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Manual Técnico</h4>
                    <p class="precision-caption">Com recomendações selecionadas.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'manual-tecnico']) }}" target="_blank" class="precision-btn precision-btn-primary w-full mt-4 bg-purple-600 border-purple-600 hover:bg-purple-700">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>

            <div class="precision-card hover:border-purple-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-purple-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-tools text-purple-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Memorial Executivo</h4>
                    <p class="precision-caption">Diretrizes de execução da obra.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'memorial-executivo']) }}" target="_blank" class="precision-btn precision-btn-primary w-full mt-4 bg-purple-600 border-purple-600 hover:bg-purple-700">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>

            <div class="precision-card hover:border-blue-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-blue-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-handshake text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Termo Compromisso</h4>
                    <p class="precision-caption">Para todos os projetos.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'termo-compromisso']) }}" target="_blank" class="precision-btn precision-btn-secondary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>

            <div class="precision-card hover:border-blue-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-blue-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-folder-open text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Termo Entrega</h4>
                    <p class="precision-caption">Para todos os projetos.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'termo-entrega-projetos']) }}" target="_blank" class="precision-btn precision-btn-secondary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>

            @if($project->edificacao_residencial)
            <div class="precision-card hover:border-teal-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-teal-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-building text-teal-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Termo Síndico</h4>
                    <p class="precision-caption">Para construtora entregar ao síndico.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'termo-sindico']) }}" target="_blank" class="precision-btn precision-btn-secondary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>
            @endif

            @if($project->edificacao_aluguel)
            <div class="precision-card hover:border-teal-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-teal-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-store text-teal-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Termo Inquilino</h4>
                    <p class="precision-caption">Para construtora entregar ao inquilino.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'termo-inquilino']) }}" target="_blank" class="precision-btn precision-btn-secondary w-full mt-4">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>
            @endif

            @if($project->porta_correr_saida_emergencia)
            <div class="precision-card hover:border-red-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-red-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-door-open text-red-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Termo Saídas Emer.</h4>
                    <p class="precision-caption">Portas de correr.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'termo-saidas-emergencia']) }}" target="_blank" class="precision-btn precision-btn-secondary w-full mt-4 text-red-600 border-red-200 hover:bg-red-50">
                        <i class="fas fa-download"></i> Gerar
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
