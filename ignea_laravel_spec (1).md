# Ígnea PCI Generator — Especificação para Desenvolvimento em Laravel

## Visão Geral

Sistema web para automação de geração de documentos de Prevenção Contra Incêndio (PCI) da Ígnea Engenharia de Incêndio. Permite cadastrar projetos e gerar automaticamente memoriais, planos de emergência, termos e demais documentos em formato `.docx`.

---

## Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 11 (PHP 8.2+) |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Banco de dados | MySQL (Hostgator) ou SQLite (desenvolvimento) |
| Geração de .docx | PHPWord (phpoffice/phpword) |
| Leitura de Excel | PhpSpreadsheet (phpoffice/phpspreadsheet) |
| Autenticação | Laravel Breeze |
| Deploy | Hostgator cPanel (shared hosting) ou VPS |

---

## Dependências Composer

```json
{
    "require": {
        "php": "^8.2",
        "laravel/framework": "^11.0",
        "laravel/breeze": "^2.0",
        "phpoffice/phpword": "^1.3",
        "phpoffice/phpspreadsheet": "^2.0",
        "livewire/livewire": "^3.0"
    }
}
```

---

## Estrutura de Diretórios

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProjectController.php
│   │   └── DocumentController.php
│   └── Requests/
│       └── ProjectRequest.php
├── Models/
│   └── Project.php
├── Services/
│   ├── Documents/
│   │   ├── BaseDocument.php          ← papel timbrado + estilos
│   │   ├── MemorialDescritivo.php
│   │   ├── PlanoEmergencia.php
│   │   ├── OficioMemorialBasico.php
│   │   ├── MemorialIndustrial.php
│   │   ├── MemorialExecutivo.php
│   │   ├── ManualTecnico.php
│   │   ├── IsolamentoRisco.php
│   │   ├── SdaiChuveiros.php
│   │   ├── Termos.php
│   │   └── DocumentosEspeciais.php
│   └── Calculations/
│       └── PciCalculations.php       ← TRRF, CI, brigadistas
resources/
├── views/
│   ├── layouts/app.blade.php
│   ├── projects/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── documents.blade.php
│   └── dashboard.blade.php
├── templates/
│   └── papel_timbrado.png            ← imagem de fundo dos documentos
└── sinalizacao/                      ← 63 imagens das placas NPT 020
    ├── P1.jpeg
    ├── S2.png
    └── ...
database/
└── migrations/
    └── 2024_01_01_000000_create_projects_table.php
routes/
├── web.php
└── api.php (opcional)
```

---

## Migration — Tabela de Projetos

```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->timestamps();

    // Identificação
    $table->string('codigo_interno');
    $table->string('nome_obra');
    $table->string('mes_ano')->nullable();
    $table->string('cidade_analise_bombeiros')->nullable();

    // Proprietário
    $table->string('nome_proprietario')->nullable();
    $table->string('tipo_documento')->default('CNPJ');
    $table->string('cpf_cnpj')->nullable();
    $table->string('nome_signatario')->nullable();
    $table->string('cpf_signatario')->nullable();
    $table->string('rg_signatario')->nullable();

    // Endereço
    $table->string('endereco')->nullable();
    $table->string('cidade')->default('Cascavel');
    $table->string('estado')->default('PR');
    $table->string('cep')->nullable();

    // Responsável Técnico
    $table->string('rt_nome')->default('Eng. Ana Julia Zunta Carniel');
    $table->string('rt_crea')->default('CREA-PR 168.913/D');
    $table->string('rt_email')->nullable();
    $table->string('rt_telefone')->nullable();

    // Edificação
    $table->decimal('area_total', 10, 2)->nullable();
    $table->decimal('altura', 8, 2)->nullable();
    $table->integer('num_pavimentos')->nullable();
    $table->boolean('area_fria')->default(false);
    $table->decimal('area_fria_m2', 10, 2)->nullable();

    // Características construtivas
    $table->string('estrutura')->nullable();
    $table->string('divisao_interna')->nullable();
    $table->string('cobertura')->nullable();
    $table->string('forro')->nullable();
    $table->string('pisos')->nullable();
    $table->string('esquadrias')->nullable();

    // Ocupações e medidas (JSON)
    $table->json('ocupacoes')->nullable();            // [{grupo, divisao, area, ci}]
    $table->json('medidas_selecionadas')->nullable(); // ['extintor', 'hidrante', ...]

    // Riscos especiais
    $table->boolean('tem_glp')->default(false);
    $table->boolean('tem_gerador')->default(false);
    $table->boolean('tem_subestacao')->default(false);

    // Brigada
    $table->string('tipo_brigadista')->default('organico');
    $table->integer('populacao_total')->nullable();
    $table->boolean('risco_espaco_confinado')->default(false);
    $table->boolean('risco_trabalho_altura')->default(false);
    $table->boolean('risco_produtos_perigosos')->default(false);

    // Hidrante
    $table->string('hidrante_tipo')->nullable();
    $table->integer('hidrante_dn_esguicho')->nullable();
    $table->integer('hidrante_dn_mangueira')->nullable();
    $table->integer('hidrante_comprimento_interno')->nullable();
    $table->integer('hidrante_comprimento_externo')->nullable();
    $table->integer('hidrante_num_internos')->nullable();
    $table->integer('hidrante_num_externos')->nullable();
    $table->string('hidrante_dimensao_caixaria')->nullable();
    $table->string('hidrante_tipo_mangueira')->nullable();
    $table->decimal('hidrante_vazao_minima', 8, 2)->nullable();
    $table->decimal('hidrante_pressao_minima', 8, 2)->nullable();
    $table->string('sistema_hidrante_pressurizacao')->nullable();
    $table->string('sistema_hidrante_acionamento')->nullable();
    $table->string('hidrante_recalque_tipo')->default('enterrado');
    $table->string('ocupacao_dimensionamento_hidrante')->nullable();

    // Reservatório
    $table->decimal('reservatorio_volume', 8, 2)->nullable();
    $table->string('reservatorio_tipo')->default('superior');
    $table->integer('reservatorio_num')->nullable();
    $table->decimal('reservatorio_volume_unitario', 8, 2)->nullable();
    $table->string('reservatorio_abastecimento')->nullable();

    // Motobomba
    $table->string('bomba_marca')->nullable();
    $table->string('bomba_modelo')->nullable();
    $table->integer('bomba_hz')->default(60);
    $table->integer('bomba_rotor_mm')->nullable();
    $table->decimal('bomba_potencia_cv', 8, 2)->nullable();
    $table->decimal('bomba_vazao_m3h', 8, 2)->nullable();
    $table->decimal('bomba_altura_mca', 8, 2)->nullable();

    // GLP
    $table->string('glp_tipo_central')->default('normal');
    $table->integer('glp_num_cilindros')->nullable();
    $table->string('glp_tipo_cilindro')->nullable();
    $table->decimal('glp_capacidade_kg', 8, 2)->nullable();
    $table->string('glp_hidrante_atende')->nullable();
    $table->string('glp_extintores')->nullable();

    // Gerador
    $table->string('gerador_combustivel')->default('diesel');
    $table->integer('gerador_capacidade_litros')->nullable();

    // Plano de emergência
    $table->boolean('pne')->default(false);
    $table->string('horario_funcionamento')->nullable();
    $table->string('caracteristicas_entorno')->nullable();
    $table->decimal('distancia_bombeiros_km', 8, 2)->nullable();
    $table->string('endereco_bombeiros')->nullable();
    $table->string('hospital_nome')->nullable();
    $table->string('hospital_endereco')->nullable();
    $table->string('via_acesso')->nullable();

    // Termos
    $table->boolean('edificacao_residencial')->default(false);
    $table->boolean('edificacao_aluguel')->default(false);
    $table->boolean('porta_correr_saida_emergencia')->default(false);

    // Edificação existente / substituição
    $table->boolean('edificacao_existente')->default(false);
    $table->string('tipo_existente')->default('tipo_1');
    $table->boolean('substituicao_projeto')->default(false);
    $table->string('subst_num_projeto_anterior')->nullable();
    $table->string('subst_rt_anterior_nome')->nullable();
    $table->string('subst_rt_anterior_crea')->nullable();
    $table->text('subst_motivo')->nullable();

    // Textos livres do memorial
    $table->text('descricao_edificacao')->nullable();
    $table->text('texto_carga_incendio')->nullable();
    $table->text('texto_medidas_seguranca')->nullable();
    $table->text('texto_compartimentacao')->nullable();
    $table->text('texto_alarme_deteccao')->nullable();
    $table->text('texto_liquidos_inflamaveis')->nullable();
    $table->decimal('area_max_compartimentacao', 10, 2)->nullable();

    // SDAI
    $table->boolean('sdai_enderecavel')->default(true);
    $table->string('sdai_classe')->default('A');
    $table->boolean('sdai_nota3_cscip')->default(false);
    $table->boolean('sdai_tem_damper')->default(false);
    $table->string('sdai_local_central')->default('portaria');
    $table->json('sdai_componentes')->nullable();

    // Chuveiros automáticos
    $table->text('chuveiro_normas')->nullable();
    $table->json('chuveiro_tipos')->nullable();
    $table->boolean('chuveiro_tem_camara_fria')->default(false);
    $table->boolean('chuveiro_ul_fm')->default(false);
    $table->decimal('chuveiro_vazao_hidrantes', 8, 2)->nullable();
    $table->json('chuveiro_vgas')->nullable();
    $table->json('chuveiro_tabela_areas')->nullable();
    $table->json('chuveiro_tabela_areas_colunas')->nullable();
    $table->text('chuveiro_rti_descricao_area')->nullable();
    $table->integer('chuveiro_rti_tempo_min')->default(60);
    $table->string('chuveiro_rti_sprinkler_tipo')->nullable();
    $table->decimal('chuveiro_rti_sprinkler_area_cob', 8, 2)->nullable();
    $table->decimal('chuveiro_rti_sprinkler_pressao_min', 8, 2)->nullable();
    $table->integer('chuveiro_rti_sprinkler_num')->nullable();
    $table->decimal('chuveiro_rti_sprinkler_vazao', 10, 2)->nullable();
    $table->decimal('chuveiro_rti_hidrante_vazao_min', 10, 2)->nullable();
    $table->decimal('chuveiro_rti_hidrante_vazao_calc', 10, 2)->nullable();
    $table->decimal('chuveiro_rti_calculada', 8, 2)->nullable();
    $table->json('chuveiro_bombas')->nullable();

    // Isolamento de risco
    $table->json('isolamentos')->nullable();

    // Memorial executivo
    $table->text('me_texto_regularizacoes_civis')->nullable();
    $table->json('me_itens_adicionais')->nullable();
    $table->json('me_tabela_sinalizacao')->nullable();
    $table->json('me_tabela_equipamentos')->nullable();

    // Memorial industrial
    $table->text('memorial_ind_atividade')->nullable();
    $table->text('memorial_ind_materias_primas')->nullable();
    $table->text('memorial_ind_produtos_acabados')->nullable();
    $table->text('memorial_ind_processo')->nullable();
    $table->text('memorial_ind_info_complementares')->nullable();
    $table->text('memorial_ind_liquidos_gases')->nullable();

    // Comprovação de existência
    $table->string('comprov_num_processo')->nullable();
    $table->string('comprov_tipo_existente')->default('tipo_1');
    $table->json('comprov_itens')->nullable();
    $table->text('comprov_texto_complementar')->nullable();

    // Tabelas externas (Excel/Revit)
    $table->json('tabela_populacao')->nullable();
    $table->json('tabela_ci')->nullable();
    $table->json('setores_compartimentacao')->nullable();
});
```

---

## Model Project

```php
<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['*']; // ou lista todos os campos

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
}
```

---

## Service de Cálculos

```php
<?php
// app/Services/Calculations/PciCalculations.php

namespace App\Services\Calculations;

class PciCalculations
{
    // Tabela 2 CSCIP — Classificação por altura
    public static function getClassificacaoAltura(float $altura): array
    {
        if ($altura <= 0)  return ['tipo' => 'TIPO I',   'denominacao' => 'Edificação Térrea',              'faixa' => 'Um pavimento'];
        if ($altura <= 6)  return ['tipo' => 'TIPO II',  'denominacao' => 'Edificação Baixa',               'faixa' => 'H ≤ 6,00 m'];
        if ($altura <= 12) return ['tipo' => 'TIPO III', 'denominacao' => 'Edificação de Baixa-Média Altura','faixa' => '6,00 m < H ≤ 12,00 m'];
        if ($altura <= 23) return ['tipo' => 'TIPO IV',  'denominacao' => 'Edificação de Média Altura',      'faixa' => '12,00 m < H ≤ 23,00 m'];
        if ($altura <= 30) return ['tipo' => 'TIPO V',   'denominacao' => 'Edificação Mediamente Alta',      'faixa' => '23,00 m < H ≤ 30,00 m'];
        return             ['tipo' => 'TIPO VI',  'denominacao' => 'Edificação Alta',              'faixa' => 'Acima de 30,00 m'];
    }

    // Tabela 3 CSCIP — Classificação por CI
    public static function getClassificacaoRisco(float $ci): string
    {
        if ($ci <= 300)  return 'Risco Leve';
        if ($ci <= 1200) return 'Risco Moderado';
        return 'Risco Elevado';
    }

    // Carga de incêndio média ponderada
    public static function calcularCargaIncendio(array $ocupacoes): float
    {
        $totalArea = collect($ocupacoes)->sum('area');
        if ($totalArea == 0) return 0;
        return collect($ocupacoes)->sum(fn($o) => ($o['ci'] ?? 0) * ($o['area'] ?? 0)) / $totalArea;
    }

    // Cálculo de brigadistas (NPT 017)
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
            'num_organicos'       => $numOrganicos,
            'num_profissionais'   => $numProfissionais,
            'num_final'           => $tipo === 'profissional' ? $numProfissionais : $numOrganicos,
            'carga_horaria_total' => $cargaBase,
            'riscos_extras'       => $riscos,
        ];
    }

    // TRRF por ocupação e altura (NPT 008)
    public static function calcularTrrf(string $divisao, float $altura): array
    {
        $grupo = substr($divisao, 0, 1);
        $classeAltura = self::getClassificacaoAltura($altura);

        // Tabela simplificada TRRF — expandir conforme NPT 008
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
}
```

---

## Service Base de Documentos

```php
<?php
// app/Services/Documents/BaseDocument.php

namespace App\Services\Documents;

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;

abstract class BaseDocument
{
    protected PhpWord $phpWord;
    protected $section;

    // Cor institucional Ígnea #912F46
    protected string $corIgnea = '912F46';
    protected string $corBranco = 'FFFFFF';
    protected string $corCinza = 'D9D9D9';

    public function __construct()
    {
        $this->phpWord = new PhpWord();
        $this->configurarEstilos();
    }

    protected function configurarEstilos(): void
    {
        // Estilo padrão Normal
        $this->phpWord->addFontStyle('Normal', [
            'name' => 'Arial', 'size' => 12,
        ]);
        $this->phpWord->addParagraphStyle('Normal', [
            'lineHeight' => 1.5, 'spaceAfter' => Converter::pointToTwip(10),
            'alignment'  => 'both', // justificado
        ]);

        // Título 1 — Arial 20 Negrito #912F46
        $this->phpWord->addFontStyle('Titulo1', [
            'name' => 'Arial', 'size' => 20, 'bold' => true,
            'color' => $this->corIgnea,
        ]);
        $this->phpWord->addParagraphStyle('Titulo1', [
            'lineHeight' => 1.5, 'spaceBefore' => Converter::pointToTwip(14),
            'spaceAfter' => Converter::pointToTwip(10), 'keepNext' => true,
        ]);

        // Título 2 — Arial 16 Negrito #912F46
        $this->phpWord->addFontStyle('Titulo2', [
            'name' => 'Arial', 'size' => 16, 'bold' => true,
            'color' => $this->corIgnea,
        ]);
        $this->phpWord->addParagraphStyle('Titulo2', [
            'lineHeight' => 1.5, 'spaceBefore' => Converter::pointToTwip(12),
            'spaceAfter' => Converter::pointToTwip(10), 'keepNext' => true,
        ]);

        // Título 3 — Arial 12 Negrito #912F46
        $this->phpWord->addFontStyle('Titulo3', [
            'name' => 'Arial', 'size' => 12, 'bold' => true,
            'color' => $this->corIgnea,
        ]);
        $this->phpWord->addParagraphStyle('Titulo3', [
            'lineHeight' => 1.5, 'spaceBefore' => Converter::pointToTwip(10),
            'spaceAfter' => Converter::pointToTwip(8), 'keepNext' => true,
        ]);
    }

    protected function criarSecao(): void
    {
        $this->section = $this->phpWord->addSection([
            'paperSize'    => 'A4',
            'marginTop'    => Converter::cmToTwip(4.0),
            'marginBottom' => Converter::cmToTwip(3.0),
            'marginLeft'   => Converter::cmToTwip(3.0),
            'marginRight'  => Converter::cmToTwip(2.5),
        ]);
        $this->adicionarPapelTimbrado();
    }

    protected function adicionarPapelTimbrado(): void
    {
        $imagePath = resource_path('templates/papel_timbrado.png');
        if (!file_exists($imagePath)) return;

        $header = $this->section->addHeader();
        $header->addWatermark($imagePath, [
            'marginTop' => 0, 'marginLeft' => 0,
        ]);
    }

    protected function addHeading(string $texto, int $nivel = 1): void
    {
        $estilo = "Titulo{$nivel}";
        $paragrafo = $this->section->addTextRun($estilo);
        $paragrafo->addText(htmlspecialchars($texto), $estilo, $estilo);
    }

    protected function addParagrafo(string $texto, bool $negrito = false): void
    {
        $fontStyle = $negrito
            ? ['name' => 'Arial', 'size' => 12, 'bold' => true]
            : ['name' => 'Arial', 'size' => 12];
        $this->section->addText(
            htmlspecialchars($texto), $fontStyle, 'Normal'
        );
    }

    protected function addBullet(string $texto, ?string $prefixoNegrito = null): void
    {
        $paragrafo = $this->section->addListItem('', 0, 'Normal');
        // PHPWord adiciona bullet automaticamente com addListItem
        $this->section->addText('• ' . htmlspecialchars($texto),
            ['name' => 'Arial', 'size' => 12],
            ['lineHeight' => 1.5, 'spaceAfter' => Converter::pointToTwip(6)]
        );
    }

    protected function addQuebraDePagina(): void
    {
        $this->section->addPageBreak();
    }

    abstract public function gerar($project): string;

    protected function salvar(string $nomeArquivo): string
    {
        $path = storage_path("app/generated/{$nomeArquivo}");
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        $writer = IOFactory::createWriter($this->phpWord, 'Word2007');
        $writer->save($path);
        return $path;
    }
}
```

---

## Controller de Projetos

```php
<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use App\Models\Project;
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_interno' => 'required|string',
            'nome_obra'      => 'required|string',
            // ... demais campos
        ]);

        // Campos JSON precisam ser decodificados
        foreach (['ocupacoes', 'medidas_selecionadas', 'sdai_componentes'] as $campo) {
            if ($request->has($campo)) {
                $data[$campo] = json_decode($request->input($campo), true);
            }
        }

        $project = Project::create($data);
        return redirect()->route('projects.documents', $project)
                         ->with('success', 'Projeto criado com sucesso!');
    }

    public function edit(Project $project)
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->all();
        foreach (['ocupacoes', 'medidas_selecionadas', 'sdai_componentes',
                  'chuveiro_vgas', 'chuveiro_bombas', 'isolamentos'] as $campo) {
            if (isset($data[$campo]) && is_string($data[$campo])) {
                $data[$campo] = json_decode($data[$campo], true);
            }
        }
        $project->update($data);
        return back()->with('success', 'Projeto atualizado!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Projeto excluído.');
    }

    public function documents(Project $project)
    {
        return view('projects.documents', compact('project'));
    }
}
```

---

## Controller de Documentos

```php
<?php
// app/Http/Controllers/DocumentController.php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;
use App\Services\Documents\MemorialDescritivo;
use App\Services\Documents\PlanoEmergencia;
use App\Services\Documents\OficioMemorialBasico;
use App\Services\Documents\MemorialIndustrial;
use App\Services\Documents\MemorialExecutivo;
use App\Services\Documents\ManualTecnico;
use App\Services\Documents\Termos;
use App\Services\Documents\DocumentosEspeciais;

class DocumentController extends Controller
{
    private array $geradores = [
        'memorial'                => MemorialDescritivo::class,
        'oficio'                  => OficioMemorialBasico::class,
        'plano-emergencia'        => PlanoEmergencia::class,
        'memorial-industrial'     => MemorialIndustrial::class,
        'memorial-executivo'      => MemorialExecutivo::class,
        'manual-tecnico'          => ManualTecnico::class,
        'termo-compromisso'       => [Termos::class, 'gerarTermoCompromisso'],
        'termo-entrega-projetos'  => [Termos::class, 'gerarTermoEntregaProjetos'],
        'termo-saidas-emergencia' => [Termos::class, 'gerarTermoSaidasEmergencia'],
        'termo-sindico'           => [Termos::class, 'gerarTermoSindico'],
        'termo-inquilino'         => [Termos::class, 'gerarTermoInquilino'],
        'comprovacao-existencia'  => [DocumentosEspeciais::class, 'gerarComprovacaoExistencia'],
        'requerimento-substituicao' => [DocumentosEspeciais::class, 'gerarRequerimentoSubstituicao'],
    ];

    public function gerar(Project $project, string $tipo)
    {
        if (!isset($this->geradores[$tipo])) {
            abort(404, "Tipo de documento não encontrado: {$tipo}");
        }

        try {
            $gerador = $this->geradores[$tipo];
            if (is_array($gerador)) {
                [$classe, $metodo] = $gerador;
                $path = (new $classe())->$metodo($project);
            } else {
                $path = (new $gerador())->gerar($project);
            }

            $filename = $project->codigo_interno . '-' . $tipo . '.docx';
            return response()->download($path, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao gerar documento: ' . $e->getMessage());
        }
    }
}
```

---

## Rotas

```php
<?php
// routes/web.php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DocumentController;

Route::middleware(['auth'])->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Projetos
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/documents', [ProjectController::class, 'documents'])
         ->name('projects.documents');

    // Geração de documentos
    Route::get('/documents/{project}/{tipo}', [DocumentController::class, 'gerar'])
         ->name('documents.gerar');
});

require __DIR__.'/auth.php';
```

---

## Formulário Multi-etapas (Alpine.js + Blade)

```html
{{-- resources/views/projects/create.blade.php --}}
<div x-data="{
    step: 1,
    totalSteps: 7,
    form: {
        codigo_interno: '',
        nome_obra: '',
        mes_ano: '',
        nome_proprietario: '',
        tipo_documento: 'CNPJ',
        cpf_cnpj: '',
        endereco: '',
        cidade: 'Cascavel',
        estado: 'PR',
        area_total: '',
        altura: '',
        num_pavimentos: '',
        ocupacoes: [],
        medidas_selecionadas: [],
        tem_glp: false,
        tem_gerador: false,
    },
    addOcupacao() {
        this.form.ocupacoes.push({ divisao: 'F-11', area: '', ci: 600 });
    },
    removeOcupacao(i) {
        this.form.ocupacoes.splice(i, 1);
    },
    toggleMedida(key) {
        const idx = this.form.medidas_selecionadas.indexOf(key);
        if (idx === -1) this.form.medidas_selecionadas.push(key);
        else this.form.medidas_selecionadas.splice(idx, 1);
    },
    ciMedia() {
        const total = this.form.ocupacoes.reduce((s, o) => s + (parseFloat(o.area) || 0), 0);
        if (!total) return 0;
        return this.form.ocupacoes.reduce((s, o) => s + (parseFloat(o.ci)||0) * (parseFloat(o.area)||0), 0) / total;
    },
    tipoEdificacao() {
        const h = parseFloat(this.form.altura) || 0;
        if (h <= 0)  return 'TIPO I — Edificação Térrea';
        if (h <= 6)  return 'TIPO II — Edificação Baixa';
        if (h <= 12) return 'TIPO III — Edificação de Baixa-Média Altura';
        if (h <= 23) return 'TIPO IV — Edificação de Média Altura';
        if (h <= 30) return 'TIPO V — Edificação Mediamente Alta';
        return 'TIPO VI — Edificação Alta';
    }
}">
    {{-- Indicador de etapas --}}
    <div class="flex gap-2 mb-8">
        <template x-for="s in totalSteps">
            <div :class="step === s ? 'bg-[#912F46] text-white' : step > s ? 'bg-green-500 text-white' : 'bg-gray-100'"
                 class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium cursor-pointer"
                 @click="if(step > s) step = s" x-text="s"></div>
        </template>
    </div>

    <form method="POST" action="{{ route('projects.store') }}">
        @csrf

        {{-- Campos JSON ocultos --}}
        <input type="hidden" name="ocupacoes" :value="JSON.stringify(form.ocupacoes)">
        <input type="hidden" name="medidas_selecionadas" :value="JSON.stringify(form.medidas_selecionadas)">

        {{-- ETAPA 1: Identificação --}}
        <div x-show="step === 1">
            <h2 class="text-xl font-semibold mb-6">Identificação do Projeto</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">Código Interno *</label>
                    <input type="text" name="codigo_interno" x-model="form.codigo_interno"
                           placeholder="25_027" class="input">
                </div>
                <div class="col-span-2">
                    <label class="label">Nome da Obra *</label>
                    <input type="text" name="nome_obra" x-model="form.nome_obra" class="input">
                </div>
                {{-- ... demais campos --}}
            </div>
        </div>

        {{-- ETAPA 3: Ocupações --}}
        <div x-show="step === 3">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Ocupações</h2>
                <button type="button" @click="addOcupacao()"
                        class="btn-primary">+ Adicionar</button>
            </div>
            <template x-for="(oc, i) in form.ocupacoes" :key="i">
                <div class="border rounded-lg p-4 mb-3 grid grid-cols-4 gap-3">
                    <div>
                        <label class="label text-xs">Divisão</label>
                        <select x-model="oc.divisao" class="input text-sm">
                            <option value="F-11">F-11 – Clubes sociais</option>
                            <option value="C-2">C-2 – Comércio média CI</option>
                            {{-- ... todas as divisões --}}
                        </select>
                    </div>
                    <div>
                        <label class="label text-xs">Área (m²)</label>
                        <input type="number" x-model="oc.area" class="input text-sm" step="0.01">
                    </div>
                    <div>
                        <label class="label text-xs">CI (MJ/m²)</label>
                        <input type="number" x-model="oc.ci" class="input text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="button" @click="removeOcupacao(i)"
                                class="text-red-500 hover:text-red-700">Remover</button>
                    </div>
                </div>
            </template>
            {{-- CI calculada --}}
            <div x-show="form.ocupacoes.length > 0"
                 class="bg-green-50 border border-green-200 rounded p-3 text-sm text-green-700">
                CI média: <strong x-text="ciMedia().toFixed(2).replace('.',',') + ' MJ/m²'"></strong>
                | <span x-text="tipoEdificacao()"></span>
            </div>
        </div>

        {{-- Navegação --}}
        <div class="flex justify-between mt-8">
            <button type="button" @click="step = Math.max(1, step-1)"
                    x-show="step > 1" class="btn-secondary">Anterior</button>
            <button type="button" @click="step = Math.min(totalSteps, step+1)"
                    x-show="step < totalSteps" class="btn-primary">Próximo</button>
            <button type="submit" x-show="step === totalSteps" class="btn-primary">
                Criar Projeto
            </button>
        </div>
    </form>
</div>
```

---

## View de Documentos

```html
{{-- resources/views/projects/documents.blade.php --}}

<div class="grid grid-cols-2 gap-4">

    <div class="col-span-2 font-semibold text-gray-500 uppercase text-xs tracking-wider mt-4">
        Fase 1 — Entrega Inicial
    </div>

    @foreach([
        ['memorial',            'Memorial Descritivo',          true],
        ['oficio',              'Ofício + Memorial Básico',     true],
        ['plano-emergencia',    'Plano de Emergência',          in_array('plano_emergencia', $project->medidas_selecionadas ?? [])],
        ['memorial-industrial', 'Memorial Industrial',          true],
        ['comprovacao-existencia', 'Comprovação de Existência', $project->edificacao_existente],
        ['requerimento-substituicao', 'Requerimento de Substituição', $project->substituicao_projeto],
    ] as [$tipo, $nome, $disponivel])
        <div class="border rounded-lg p-4 {{ !$disponivel ? 'opacity-40' : '' }}">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 bg-pink-50 rounded flex items-center justify-center">
                    📄
                </div>
                <div>
                    <div class="font-medium text-sm">{{ $nome }}</div>
                    <div class="text-xs text-gray-400">Fase 1</div>
                </div>
            </div>
            @if($disponivel)
                <a href="{{ route('documents.gerar', [$project, $tipo]) }}"
                   class="btn-primary w-full text-center text-sm block">
                    ⬇ Gerar e Baixar
                </a>
            @else
                <div class="text-xs text-amber-500 italic">Condição não atendida</div>
            @endif
        </div>
    @endforeach

    {{-- Fase 2 similar --}}
</div>
```

---

## Deploy no Hostgator (Shared Hosting)

```
1. Acesse cPanel → File Manager
2. Faça upload do projeto na pasta public_html/ignea/ (ou subdomínio)
3. Aponte o document root para public_html/ignea/public
4. Configure o .env com as credenciais do banco MySQL do Hostgator
5. No Terminal SSH (se disponível): php artisan migrate
6. Certifique-se de que as pastas storage/ e bootstrap/cache/ têm permissão 775
```

### .env para Hostgator

```env
APP_NAME="Ígnea PCI"
APP_ENV=production
APP_KEY=             # gerar com: php artisan key:generate
APP_DEBUG=false
APP_URL=https://pci.igneaengenharia.com.br

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=seu_banco_hostgator
DB_USERNAME=seu_usuario_hostgator
DB_PASSWORD=sua_senha_hostgator

FILESYSTEM_DISK=local
```

---

## Diferenças em relação à versão Python

| Aspecto | Python (versão atual) | Laravel (esta versão) |
|---|---|---|
| Geração .docx | `python-docx` | `phpoffice/phpword` |
| Deploy | Railway + Vercel | Hostgator (já tem conta) |
| Banco | SQLite/PostgreSQL | MySQL (Hostgator) |
| Frontend | React (SPA) | Blade + Alpine.js |
| Curva de aprendizado | Médio | Baixo (se conhece PHP) |
| Custo | ~$5/mês (Railway) | $0 (já tem Hostgator) |

---

## Ordem de implementação sugerida

1. `php artisan new ignea-pci --breeze` — projeto base com autenticação
2. Criar migration e model `Project`
3. Implementar `ProjectController` (CRUD)
4. Implementar `BaseDocument` com papel timbrado
5. Implementar `MemorialDescritivo` (documento mais complexo — serve de referência)
6. Implementar `PlanoEmergencia`
7. Implementar `OficioMemorialBasico`
8. Implementar `Termos` (5 termos)
9. Implementar `MemorialExecutivo` e `ManualTecnico`
10. Copiar imagens das sinalizações para `resources/sinalizacao/`
11. Implementar views Blade (formulário multi-etapas e tela de documentos)
12. Deploy no Hostgator

---

*Especificação gerada com base no sistema Python já desenvolvido. Toda a lógica de negócio, textos dos documentos e mapeamentos (CSCIP, NPT 020, etc.) estão documentados no código Python e devem ser portados campo a campo.*
