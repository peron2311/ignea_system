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
            $table->string('rt_nome')->nullable()->change();
            $table->string('rt_crea')->nullable()->change();
            $table->string('rt_email')->nullable()->change();
            $table->string('rt_telefone')->nullable()->change();
            $table->string('cidade')->nullable()->change();
            $table->string('estado')->nullable()->change();
            $table->string('tipo_brigadista')->nullable()->change();
            $table->string('gerador_combustivel')->nullable()->change();
            $table->string('glp_tipo_central')->nullable()->change();
            $table->string('reservatorio_tipo')->nullable()->change();
            $table->string('hidrante_recalque_tipo')->nullable()->change();
            $table->string('sdai_local_central')->nullable()->change();
            $table->string('sdai_classe')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Revertendo para as restrições originais se necessário
            $table->string('rt_nome')->nullable(false)->change();
            $table->string('rt_crea')->nullable(false)->change();
        });
    }
};
