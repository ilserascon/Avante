<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function foreignKeyReferencesTable(string $table, string $column, string $referencedTable): bool
    {
        $database = Schema::getConnection()->getDatabaseName();

        $result = DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME = ?',
            [$database, $table, $column, $referencedTable]
        );

        return ! empty($result);
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        $database = Schema::getConnection()->getDatabaseName();

        $constraints = DB::select(
            'SELECT CONSTRAINT_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$database, $table, $column]
        );

        foreach ($constraints as $constraint) {
            DB::statement(sprintf(
                'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
                $table,
                $constraint->CONSTRAINT_NAME
            ));
        }
    }

    private function ensureForeignKeyToProductos(string $column): void
    {
        if (! Schema::hasColumn('detalle_cotizacion', $column)) {
            return;
        }

        if ($this->foreignKeyReferencesTable('detalle_cotizacion', $column, 'productos')) {
            return;
        }

        $this->dropForeignKeyIfExists('detalle_cotizacion', $column);

        Schema::table('detalle_cotizacion', function (Blueprint $table) use ($column) {
            $table->foreign($column)
                ->references('id')
                ->on('productos')
                ->nullOnDelete();
        });
    }

    public function up(): void
    {
        $this->ensureForeignKeyToProductos('cortinero_id');
        $this->ensureForeignKeyToProductos('cortinero_tergal_id');
    }

    public function down(): void
    {
        foreach (['cortinero_id', 'cortinero_tergal_id'] as $column) {
            if (! Schema::hasColumn('detalle_cotizacion', $column)) {
                continue;
            }

            if ($this->foreignKeyReferencesTable('detalle_cotizacion', $column, 'insumo')) {
                continue;
            }

            $this->dropForeignKeyIfExists('detalle_cotizacion', $column);

            Schema::table('detalle_cotizacion', function (Blueprint $table) use ($column) {
                $table->foreign($column)
                    ->references('id')
                    ->on('insumo')
                    ->nullOnDelete();
            });
        }
    }
};
