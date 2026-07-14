<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Crear tabla roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->timestamps();
        });

        // Insertar roles por default
        DB::table('roles')->insert([
            ['nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Cotizador',     'created_at' => now(), 'updated_at' => now()],
        ]);

        // Agregar campo role_id a la tabla users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('email')
                  ->constrained('roles')->onDelete('set null');
        });

        // Obtener ID del rol Administrador
        $adminRoleId = DB::table('roles')->where('nombre', 'Administrador')->value('id');

        // Crear usuario admin
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@avante.com',
            'password' => bcrypt('admin'),
            'role_id' => $adminRoleId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }

    public function down()
    {
        // Quitar columna de users primero
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        // Luego eliminar tabla roles
        Schema::dropIfExists('roles');
    }
};
