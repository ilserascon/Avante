<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DbResetDevCommand extends Command
{
    protected $signature = 'db:reset-dev {--force : Ejecutar sin confirmación}';

    protected $description = 'Reinicia la base de datos en entorno local (migrate:fresh --seed). Borra todos los datos.';

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('Este comando solo está permitido con APP_ENV=local.');
            $this->line('En producción no uses migrate:refresh ni migrate:fresh.');

            return self::FAILURE;
        }

        if (! $this->option('force') && ! $this->confirm('Se borrarán TODAS las tablas y datos. ¿Continuar?', false)) {
            $this->info('Operación cancelada.');

            return self::SUCCESS;
        }

        $this->warn('Ejecutando migrate:fresh --seed...');

        $this->call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ]);

        $this->newLine();
        $this->info('Base de datos reiniciada.');
        $this->line('Usuario admin (migración de roles): admin@avante.com / admin');

        return self::SUCCESS;
    }
}
