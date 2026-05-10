<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->text('rota_bombeiros_path')->nullable();
            $table->text('rota_hospital_path')->nullable();
            $table->text('hidrante_tipo_tubulacao')->nullable();
            $table->integer('hidrante_dn_tubulacao')->nullable();
            $table->integer('hidrante_dn_recalque_tub')->default(63);
            $table->decimal('hidrante_vazao_dimensionamento_lmin', 10, 2)->nullable();
            $table->text('subst_grupamento')->nullable();
            $table->text('subst_tipo')->nullable();
            $table->boolean('subst_aumento_area')->default(false);
            $table->boolean('subst_diminuicao_area')->default(false);
            $table->text('subst_nota_pranchas')->nullable();
            $table->text('memorial_ind_empresa')->nullable();
            $table->text('me_excel_path')->nullable();
            $table->json('recomendacoes_selecionadas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'rota_bombeiros_path',
                'rota_hospital_path',
                'hidrante_tipo_tubulacao',
                'hidrante_dn_tubulacao',
                'hidrante_dn_recalque_tub',
                'hidrante_vazao_dimensionamento_lmin',
                'subst_grupamento',
                'subst_tipo',
                'subst_aumento_area',
                'subst_diminuicao_area',
                'subst_nota_pranchas',
                'memorial_ind_empresa',
                'me_excel_path',
                'recomendacoes_selecionadas'
            ]);
        });
    }
};
