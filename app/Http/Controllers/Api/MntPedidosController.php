<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePedidoRequest;
use App\Http\Response\ApiResponse;
use App\Models\MntDetallePedidos;
use App\Models\MntPedidos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MntPedidosController extends Controller
{
    //
    public function index(){}
    public function store(Request $request, $id){
        $message = [
            "fecha_pedido.required" => "La fecha de pedido es obligatoria",
            "fecha_pedido.date" => "La fecha debe ser formato de fecha",
            "detalle" => "El detalle debe de ser un arreglo",
            "client_id.required" => "El cliente es requerido",
            "client_id.exists" => "El cliente debe estar registrado",
            "detalle.*.product_id.required" => "El producto es obligatorio",
            "detalle.*.product_id.exists" => "Seleccione un producto existente",
            "detalle.*.cantidad.required" => "La cantidad es obligatoria",
            "detalle.*.cantidad.numeric" => "La cantidad debe de ser un numero",
            "detalle.*.precio.required" => "El precio es obligatorio",
            "detalle.*.precio.numeric" => "El precio debe de ser un numero"
        ];

        $validator = Validator::make($request->all(), [
            "fecha_pedido" => "required|date",
            "client_id" => "required|exists:mnt_clientes,id",
            "detalle" => "array",
            "detalle.*.product_id" => "required|exists:ctl_productos,id",
            "detalle.*.precio" => "required|numeric",
            "detalle.*.cantidad" => "required|numeric",
        ], $message);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $pedido = new MntPedidos();
            $pedido->fecha_pedido = $request->fecha_pedido; // Correct assignment
            $pedido->client_id = $request->client_id;

            if ($pedido->save()) {
                $totalF = 0;

                foreach ($request->detalle as $d) {
                    $detalle = new MntDetallePedidos();
                    $detalle->cantidad = $d['cantidad'];
                    $detalle->pedido = $pedido->id;
                    $detalle->precio = $d['precio'];
                    $detalle->subtotal = $d['cantidad'] * $d['precio'];
                    $detalle->save();

                    $totalF += $detalle->subtotal;
                }

                $pedido->total = $totalF;
                $pedido->save();
                DB::commit();

                return ApiResponse::success('Pedido creado', 200, $pedido);
            } else {
                DB::rollBack();
                return ApiResponse::error('Error al crear el pedido', 422);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error($e->getMessage(), 422);
        }
    }
}
