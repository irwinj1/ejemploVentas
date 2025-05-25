<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CtlProductos;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class GeneratePdfController extends Controller
{
    //
    public function generatePdf(Request $request){
       
        $producto = CtlProductos::with([
            'productosCategoria.categorias',
            'inventario',
            'imageProductos'
        ])->where('id', $request->id)->first();
        $productoFormated = [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'precio' => $producto->precio,
            'estado' => $producto->activo,
            'categoria' => [
                'id' => $producto->productosCategoria->categorias->id ?? null,
                'nombre' => $producto->productosCategoria->categorias->nombre ?? null,
            ],
            'inventario' => [
                'id' => $producto->inventario->id ?? null,
                'cantidad' => $producto->inventario->cantidad ?? null,
            ],
            'image' => [
                'id' => $producto->imageProductos->id ?? null,
                'nombre' => $producto->imageProductos->nombre ?? null,
                'path' => $producto->imageProductos->path ?? null,
                'relative_path' => url($producto->imageProductos->relative_path) ?? null,
            ]
        ];

        // return $productoFormated;

        $pdf = new Mpdf();
        $pdf->WriteHTML(view('pdf.generateReporte', compact('productoFormated')));
        return $pdf->Output('invoice.pdf', 'D');
    }
}
