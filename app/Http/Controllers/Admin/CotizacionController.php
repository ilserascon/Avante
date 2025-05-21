<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cotizacion;
use App\Models\Insumo;
use Barryvdh\DomPDF\Facade\Pdf; // Usa la facade de DomPDF
use Illuminate\Support\Facades\Storage;

class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Cotizacion::query();

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha', '<=', $request->input('fecha_fin'));
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->input('estatus'));
        }

        $cotizaciones = $query->latest()->paginate(10);

        return view('admin.cotizaciones.index', compact('cotizaciones'));
    }

    public function create()
    {
        $insumos = Insumo::where('id_tipo_insumo', '=', 2)->get();

        $insumosFijos = Insumo::whereIn('nombre', ['Ojillos', 'Cortinero', 'Puntas', 'Mensulas'])->get()->keyBy('nombre');

        return view('admin.cotizaciones.create', compact('insumos', 'insumosFijos'));
    }

    public function store(Request $request)
    {
        // Validación ajustada según la migración y el formulario
        $validated = $request->validate([
            'cliente_id'         => 'required|exists:clientes,id',
            'fecha'              => 'required|date',
            // Los siguientes campos pueden ser opcionales según el formulario
            'detalle'            => 'array',
            'totales'            => 'array',
        ]);

        $detalle = $request->input('detalle', []);
        $totales = $request->input('totales', []);

        $cotizacion = new Cotizacion();
        $cotizacion->cliente_id   = $validated['cliente_id'];
        $cotizacion->fecha        = $validated['fecha'];

        // Flags de tipo de cotización
        $tipos = $request->input('tipo', []);
        $cotizacion->lleva_cortina = in_array('cortina', $tipos);
        $cotizacion->lleva_tergal  = in_array('tergal', $tipos);
        $cotizacion->lleva_forro   = $request->has('lleva_forro');

        // Totales y cálculos
        $cotizacion->total_lienzos     = $totales['total_lienzos'] ?? null;
        $cotizacion->total_m2_forro    = $totales['total_m2_forro'] ?? null;
        $cotizacion->total_m2_tela     = $totales['total_m2_tela'] ?? null;
        $cotizacion->total_m2_tergal   = $totales['total_m2_tergal'] ?? null;
        $cotizacion->costo_cortina     = $totales['costo_cortina'] ?? null;
        $cotizacion->utilidad          = $totales['utilidad'] ?? null;
        $cotizacion->costo_decorador   = $totales['costo_decorador'] ?? null;
        $cotizacion->precio_publico    = $totales['precio_publico'] ?? null;

        $cotizacion->estatus = $request->input('estatus', 'solicitada');

        $cotizacion->save();

        $insumos = $request->input('insumos', []);
        if (!empty($insumos)) {
            $cotizacion->insumos()->attach($insumos);
        }

        $insumosFijos = [
            'ojillos' => Insumo::where('nombre', 'Ojillos')->first(),
            'cortinero' => Insumo::where('nombre', 'Cortinero')->first(),
            'puntas' => Insumo::where('nombre', 'Puntas')->first(),
            'mensulas' => Insumo::where('nombre', 'Mensulas')->first(),
        ];

        $insumosAttach = [];

        // Insumos fijos
        foreach ($insumosFijos as $key => $insumo) {
            if ($insumo) {
                $cantidad = $detalle["{$key}_cantidad"] ?? 0;
                $precio = $detalle["{$key}_precio"] ?? 0;
                if ($cantidad > 0) {
                    $insumosAttach[$insumo->id] = [
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precio,
                        'subtotal' => $cantidad * $precio,
                    ];
                }
            }
        }

        $ojillosId = $detalle['ojillos_id'] ?? null;
        $cantidad = $detalle['ojillos_cantidad'] ?? 0;
        $precio = $detalle['ojillos_precio'] ?? 0;

        // Insumos dinámicos
        foreach ($detalle as $k => $v) {
            if (preg_match('/^otros(\d+)_nombre$/', $k, $matches)) {
                $index = $matches[1];
                $insumoId = $v;
                $cantidad = $detalle["otros{$index}_cantidad"] ?? 0;
                $precio = $detalle["otros{$index}_precio"] ?? 0;
                if ($insumoId && $cantidad > 0) {
                    if (isset($insumosAttach[$insumoId])) {
                        $insumosAttach[$insumoId]['cantidad'] += $cantidad;
                        $insumosAttach[$insumoId]['subtotal'] += $cantidad * $precio;
                    } else {
                        $insumosAttach[$insumoId] = [
                            'cantidad' => $cantidad,
                            'precio_unitario' => $precio,
                            'subtotal' => $cantidad * $precio,
                        ];
                    }
                }
            }
        }

        if (!empty($insumosAttach)) {
            $cotizacion->insumos()->attach($insumosAttach);
        }

        return redirect()->route('admin.cotizaciones.index')->with('success', 'Cotización creada exitosamente.');
    }

    public function show($id)
    {
        $cotizacion = Cotizacion::with(['cliente', 'insumos'])->findOrFail($id);
        return view('admin.cotizaciones.show', compact('cotizacion'));
    }

    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}

    public function cambiarEstatus(Request $request, Cotizacion $cotizacion)
    {
        $cotizacion->estatus = $request->estatus;
        $cotizacion->save();
        return redirect()->back()->with('success', 'Estatus actualizado correctamente.');
    }

    public function generarPdf($id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        $pdf = Pdf::loadView('admin.cotizaciones.pdf', compact('cotizacion'));

        $fileName = 'cotizacion_' . $cotizacion->id . '.pdf';
        $filePath = 'pdfs/' . $fileName;

        // Guarda el PDF en storage/app/public/pdfs
        Storage::disk('public')->put($filePath, $pdf->output());

        // Devuelve la URL pública para descargar
        $url = Storage::url($filePath);

        return response()->download(storage_path('app/public/pdfs/' . $fileName));
    }
}
