<?php

namespace App\Console\Commands;

use App\Models\DetalleCotizacion;
use App\Models\Existencia;
use App\Models\Insumo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UnificarTelasTergalCommand extends Command
{
    protected $signature = 'insumos:unificar-telas
                            {--dry-run : Solo muestra el plan, no escribe}
                            {--ejecutar : Aplica los cambios}';

    protected $description = 'Si un Tergal es el mismo que una Tela, deja solo la tela y remapea cotizaciones, entradas y existencias. Los tergales distintos se conservan.';

    public function handle(): int
    {
        $ejecutar = (bool) $this->option('ejecutar');
        $dryRun = (bool) $this->option('dry-run') || ! $ejecutar;

        $tipoTela = Insumo::idTipoTelas();
        $tipoTergal = Insumo::idTipoTergal();

        if (! $tipoTela || ! $tipoTergal) {
            $this->error('No se encontraron los tipos Telas y Tergal.');

            return self::FAILURE;
        }

        $telas = Insumo::with('tipoInsumo')
            ->where('id_tipo_insumo', $tipoTela)
            ->get()
            ->keyBy(fn (Insumo $insumo) => $insumo->claveEquivalenciaTextil());

        $tergales = Insumo::with('tipoInsumo')
            ->where('id_tipo_insumo', $tipoTergal)
            ->orderBy('id')
            ->get();

        if ($tergales->isEmpty()) {
            $this->info('No hay insumos tipo Tergal. Nada que unificar.');

            return self::SUCCESS;
        }

        $conservar = [];
        $fusionar = [];

        foreach ($tergales as $tergal) {
            $clave = $tergal->claveEquivalenciaTextil();
            $tela = $telas->get($clave);

            if ($tela && (int) $tela->id !== (int) $tergal->id) {
                $fusionar[] = [$tergal, $tela];
            } else {
                $conservar[] = $tergal;
            }
        }

        $this->info('Tipo Telas id=' . $tipoTela . ' | Tipo Tergal id=' . $tipoTergal);
        $this->info('Tergales duplicados de una tela (se deja solo la tela): ' . count($fusionar));
        $this->info('Tergales distintos (se conservan como Tergal): ' . count($conservar));

        foreach ($fusionar as [$tergal, $tela]) {
            $this->line(sprintf(
                '  Fusionar %d → %d  %s',
                $tergal->id,
                $tela->id,
                $tergal->etiquetaEntrada()
            ));
        }

        foreach ($conservar as $tergal) {
            $this->line(sprintf('  Conservar tergal %d  %s', $tergal->id, $tergal->etiquetaEntrada()));
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('Dry-run: no se escribió nada. Para aplicar: php artisan insumos:unificar-telas --ejecutar');

            return self::SUCCESS;
        }

        if (! $this->confirm('¿Aplicar estos cambios en la base de datos actual?', false)) {
            $this->info('Cancelado.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($fusionar) {
            foreach ($fusionar as [$tergal, $tela]) {
                $this->remapearInsumo((int) $tergal->id, (int) $tela->id);
                $tergal->borrado = 1;
                $tergal->save();
            }
        });

        $this->info('Duplicados unificados. Los tergales distintos no se modificaron.');

        return self::SUCCESS;
    }

    private function remapearInsumo(int $origen, int $destino): void
    {
        if ($origen === $destino) {
            return;
        }

        DetalleCotizacion::where('tela_id', $origen)->update(['tela_id' => $destino]);
        DetalleCotizacion::where('tergal_id', $origen)->update(['tergal_id' => $destino]);
        DetalleCotizacion::where('forro_id', $origen)->update(['forro_id' => $destino]);

        DB::table('detalle_entradas')->where('id_insumo', $origen)->update(['id_insumo' => $destino]);

        $this->fusionarExistencias($origen, $destino);
        $this->fusionarCotizacionInsumo($origen, $destino);
        $this->fusionarProductoInsumo($origen, $destino);
        $this->remapearMaterialesVarios($origen, $destino);
    }

    private function fusionarExistencias(int $origen, int $destino): void
    {
        $existencias = Existencia::where('id_insumo', $origen)->get();

        foreach ($existencias as $existencia) {
            $destinoEx = Existencia::where('id_almacen', $existencia->id_almacen)
                ->where('id_insumo', $destino)
                ->first();

            if ($destinoEx) {
                $destinoEx->cantidad = (float) $destinoEx->cantidad + (float) $existencia->cantidad;
                $destinoEx->save();
                $existencia->delete();
            } else {
                $existencia->id_insumo = $destino;
                $existencia->save();
            }
        }
    }

    private function fusionarCotizacionInsumo(int $origen, int $destino): void
    {
        $filas = DB::table('cotizacion_insumo')->where('insumo_id', $origen)->get();

        foreach ($filas as $fila) {
            $yaExiste = DB::table('cotizacion_insumo')
                ->where('cotizacion_id', $fila->cotizacion_id)
                ->where('insumo_id', $destino)
                ->first();

            if ($yaExiste) {
                DB::table('cotizacion_insumo')->where('id', $yaExiste->id)->update([
                    'cantidad' => (float) $yaExiste->cantidad + (float) $fila->cantidad,
                    'subtotal' => (float) $yaExiste->subtotal + (float) $fila->subtotal,
                ]);
                DB::table('cotizacion_insumo')->where('id', $fila->id)->delete();
            } else {
                DB::table('cotizacion_insumo')->where('id', $fila->id)->update(['insumo_id' => $destino]);
            }
        }
    }

    private function fusionarProductoInsumo(int $origen, int $destino): void
    {
        $filas = DB::table('producto_insumo')->where('id_insumo', $origen)->get();

        foreach ($filas as $fila) {
            $yaExiste = DB::table('producto_insumo')
                ->where('id_producto', $fila->id_producto)
                ->where('id_insumo', $destino)
                ->first();

            if ($yaExiste) {
                DB::table('producto_insumo')
                    ->where('id_producto', $fila->id_producto)
                    ->where('id_insumo', $origen)
                    ->delete();
            } else {
                DB::table('producto_insumo')
                    ->where('id_producto', $fila->id_producto)
                    ->where('id_insumo', $origen)
                    ->update(['id_insumo' => $destino]);
            }
        }
    }

    private function remapearMaterialesVarios(int $origen, int $destino): void
    {
        $detalles = DetalleCotizacion::query()
            ->whereNotNull('materiales_varios')
            ->get();

        foreach ($detalles as $detalle) {
            $filas = $detalle->materiales_varios ?? [];
            $cambio = false;

            foreach ($filas as $indice => $fila) {
                if ((int) ($fila['insumo_id'] ?? 0) === $origen) {
                    $filas[$indice]['insumo_id'] = $destino;
                    $cambio = true;
                }
            }

            if ($cambio) {
                $detalle->materiales_varios = $filas;
                $detalle->save();
            }
        }
    }
}
