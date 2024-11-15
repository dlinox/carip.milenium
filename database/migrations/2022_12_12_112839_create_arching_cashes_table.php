<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchingCashesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arching_cashes', function (Blueprint $table) {
            $table->id();
            $table->integer('idcaja');
            $table->integer('idusuario');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->decimal('monto_inicial', 10 , 2);
            $table->decimal('monto_final', 10 , 2)->nullable();
            $table->integer('total_ventas');
            $table->integer('estado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arching_cashes');
    }
}
