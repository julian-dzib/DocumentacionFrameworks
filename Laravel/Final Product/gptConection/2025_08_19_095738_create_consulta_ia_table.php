<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('consulta_ia')) {
            Schema::create('consulta_ia', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->longText('contexto');
                $table->string('rol');
                $table->foreignId('tabla_id'); 
                $table->foreign('tabla_id')->references('id')->on('modulo'); 
                $table->foreignId('database_id'); 
                $table->foreign('database_id')->references('id')->on('databases'); 
                $table->boolean(column: 'estatus')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consulta_ia');
    }
};
