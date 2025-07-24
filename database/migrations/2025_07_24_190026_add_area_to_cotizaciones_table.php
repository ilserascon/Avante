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
        Schema::table('cotizaciones', function ($table) {
            $table->string('area')->nullable()->after('fecha');
        });
    }

    public function down()
    {
        Schema::table('cotizaciones', function ($table) {
            $table->dropColumn('area');
        });
    }
};
