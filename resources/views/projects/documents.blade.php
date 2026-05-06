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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 precision-gap-16">
            
            <!-- Memorial Descritivo -->
            <div class="precision-card hover:border-blue-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-blue-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-file-word text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Memorial Descritivo</h4>
                    <p class="precision-caption">Memorial descritivo padrão contendo todas as medidas de segurança e identificação da edificação.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'memorial']) }}" target="_blank" download="memorial{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}.docx" class="precision-btn precision-btn-primary w-full mt-4">
                        <i class="fas fa-download"></i> Baixar Documento
                    </a>
                </div>
            </div>

            <!-- Ofício de Encaminhamento -->
            <div class="precision-card hover:border-blue-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-blue-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-envelope-open-text text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Ofício</h4>
                    <p class="precision-caption">Ofício de encaminhamento padrão do projeto para análise no Corpo de Bombeiros.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'oficio']) }}" target="_blank" download="oficio{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}.docx" class="precision-btn precision-btn-primary w-full mt-4">
                        <i class="fas fa-download"></i> Baixar Documento
                    </a>
                </div>
            </div>

            <!-- Termos de Responsabilidade -->
            <div class="precision-card hover:border-blue-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper bg-blue-50 mb-0 mx-auto" style="width: 64px; height: 64px;">
                        <i class="fas fa-file-signature text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Termos (ART/RRT)</h4>
                    <p class="precision-caption">Termos de compromisso e responsabilidade técnica preenchidos com os dados da obra.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'termos']) }}" target="_blank" download="termo{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}.docx" class="precision-btn precision-btn-primary w-full mt-4">
                        <i class="fas fa-download"></i> Baixar Documento
                    </a>
                </div>
            </div>

            <!-- Plano de Emergência -->
            @if($project->pne)
            <div class="precision-card hover:border-emerald-500 transition-colors">
                <div class="precision-flex-center flex-col text-center space-y-4">
                    <div class="icon-wrapper mx-auto" style="background-color: #ecfdf5; width: 64px; height: 64px;">
                        <i class="fas fa-file-medical-alt text-emerald-600 text-3xl"></i>
                    </div>
                    <h4 class="precision-heading-5">Plano de Emergência</h4>
                    <p class="precision-caption">Plano de Emergência Contra Incêndio (PNE) dimensionado para a população e risco.</p>
                    <a href="{{ route('documents.gerar', ['project' => $project, 'tipo' => 'pne']) }}" target="_blank" download="pne{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}.docx" class="precision-btn precision-btn-primary w-full mt-4" style="background-color: var(--color-success);">
                        <i class="fas fa-download"></i> Baixar PNE
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
