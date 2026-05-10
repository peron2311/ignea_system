<?php

namespace App\Services\Documents;

use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\Project;
use Exception;

abstract class BaseDocument
{
    /**
     * O caminho para o template .docx
     */
    protected string $templatePath;

    /**
     * O prefixo para o nome do arquivo gerado
     */
    protected string $filePrefix = 'Documento';

    public function __construct()
    {
        // As subclasses devem definir o $templatePath no seu construtor
    }

    /**
     * Retorna o processador de template inicializado
     */
    protected function getTemplateProcessor(): TemplateProcessor
    {
        if (!file_exists($this->templatePath)) {
            throw new Exception("Template não encontrado: " . $this->templatePath);
        }
        return new TemplateProcessor($this->templatePath);
    }

    /**
     * Retorna um valor padrão se a variável for vazia
     */
    protected function val(?string $value, string $default = 'Não informado'): string
    {
        return empty(trim((string)$value)) ? $default : $value;
    }

    /**
     * Define um valor no template de forma segura (evita erro de tipo no PHP 8.1+)
     */
    protected function setSafeValue(TemplateProcessor $processor, string $key, $value): void
    {
        if (is_array($value)) {
            // Se for um array simples (strings/números), junta com vírgula.
            // Se for complexo (objetos), converte para JSON ou ignora para evitar erro.
            try {
                $isSimple = true;
                foreach ($value as $item) {
                    if (is_array($item) || is_object($item)) {
                        $isSimple = false;
                        break;
                    }
                }
                $value = $isSimple ? implode(', ', $value) : json_encode($value, JSON_UNESCAPED_UNICODE);
            } catch (\Exception $e) {
                $value = '[Erro de conversão]';
            }
        }
        
        if (is_bool($value)) {
            $value = $value ? 'Sim' : 'Não';
        }
        
        $processor->setValue($key, (string)($value ?? ''));
    }

    /**
     * Formata área em m²
     */
    protected function formatArea(?float $value): string
    {
        if (!$value) return 'Não informado';
        return number_format($value, 2, ',', '.') . ' m²';
    }

    /**
     * Preenche os dados básicos do projeto no template
     */
    protected function preencherDadosBasicos(TemplateProcessor $processor, Project $project): void
    {
        $dados = $project->getTemplateData();
        
        foreach ($dados as $chave => $valor) {
            // Tratamento especial para blocos (começam com has_)
            if (str_starts_with($chave, 'has_')) {
                $nomeBloco = 'block_' . str_replace('has_', '', $chave);
                if ($valor === true) {
                    $processor->cloneBlock($nomeBloco, 1, true, false);
                } else {
                    $processor->cloneBlock($nomeBloco, 0, true, false);
                }
            } else {
                $this->setSafeValue($processor, $chave, $valor);
            }
        }

        // Esconde os blocos que não vieram marcados (false/inexistentes)
        $todosOsBlocos = [
            'rec_acesso', 'rec_separacao', 'rec_estrutural', 'rec_compartimentacao',
            'rec_materiais', 'rec_saidas', 'rec_elevador', 'rec_brigada', 'rec_iluminacao',
            'rec_alarme', 'rec_sinalizacao', 'rec_extintores', 'rec_hidrantes', 'rec_chuveiros',
            'rec_controle_fumaca', 'rec_liquidos'
        ];
        foreach ($todosOsBlocos as $bloco) {
            if (!isset($dados['has_' . $bloco])) {
                $processor->cloneBlock('block_' . $bloco, 0, true, false);
            }
        }
    }

    /**
     * Salva o documento e retorna o caminho
     */
    protected function salvar(TemplateProcessor $processor, Project $project, string $sulfixo = ''): string
    {
        // Auto-injetar os dados do projeto antes de salvar!
        $this->preencherDadosBasicos($processor, $project);

        $fileName = sprintf('%s_%s%s.docx',
            $project->codigo_interno ?: 'PROJ',
            $this->filePrefix,
            $sulfixo ? '_' . $sulfixo : ''
        );

        $path = storage_path("app/generated/{$fileName}");
        
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $processor->saveAs($path);
        
        return $path;
    }

    /**
     * O método principal de geração, que toda classe filha deve implementar.
     */
    abstract public function gerar(Project $project): string;
}
