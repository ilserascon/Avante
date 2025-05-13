<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('insumo', function (Blueprint $table) {
            $table->string('campo9')->nullable();
            $table->string('campo10')->nullable();
            $table->string('campo11')->nullable();
            $table->string('campo12')->nullable();
            $table->string('campo13')->nullable();
            $table->string('campo14')->nullable();
            $table->string('campo15')->nullable();
        });
    }

    public function down()
    {
        Schema::table('insumo', function (Blueprint $table) {
            $table->dropColumn([
                'campo9', 'campo10', 'campo11', 'campo12',
                'campo13', 'campo14', 'campo15'
            ]);
        });
    }

};
