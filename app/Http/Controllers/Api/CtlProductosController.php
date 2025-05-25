<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;
use App\Models\CtlCategoriasProductos;
use App\Models\CtlImageProductos;
use App\Models\CtlInventerio;
use App\Models\CtlProductos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CtlProductosController extends Controller
{
    //
    public function index(){
        try {
        $products = CtlProductos::with([
            "productosCategoria.categorias",
            "inventario",
            'imageProductos'
        ])->where('activo',true)->paginate(10);
        $productsFormated = $products->map(function($row){
            return [
                'id'=>$row->id,
                'nombre'=>$row->nombre,
                'precio'=>$row->precio,
                'estado'=>$row->activo,
                'categoria'=>[
                    'id'=>$row->productos_categoria->categorias->id??null,
                    'nombre'=>$row->productos_categoria->categorias->nombre??null,
                ],
                'inventario'=>[
                    'id'=>$row->inventario->id??null,
                    'cantidad'=>$row->inventario->cantidad??null,
                ],
                'image'=>[
                    'id'=>$row->imageProductos->id??null,
                    'nombre'=>$row->imageProductos->nombre??null,
                    'path'=> url($row->imageProductos->path) ?? null,
                ]
            ];
        }

        );

        return ApiResponse::success('Productos',200,$productsFormated);
        } catch (\Exception $e) {
            //throw $th;
            return $e->getMessage();
        }
    }
   public function store(Request $request)
{
    try {
       
        $messages = [
            "nombre.required" => "Nombre es requerido",
            "nombre.max" => "Nombre no debe pasar de 255 caracteres",
            "nombre.unique" => "Nombre ya existe",
            "precio.required" => "Precio es requerido",
            "precio.numeric" => "Precio debe ser un número",
            "image.required" => "Imagen es requerida",
            "image.image" => "El archivo debe ser una imagen",
            "image.mimes" => "La imagen debe ser jpeg, png o jpg",
            "image.max" => "La imagen no debe superar los 8MB",
            "cantidad.required" => "Cantidad es requerida",
            "cantidad.numeric" => "Cantidad debe ser un número",
            "categoria_id.required" => "Categoría es requerida",
            "categoria_id.exists" => "La categoría no existe"
        ];

        $validator = Validator::make($request->all(), [
            "nombre" => "required|max:255|unique:ctl_productos,nombre",
            "precio" => "required|numeric",
            "image" => "required|image|mimes:jpeg,png,jpg|max:8192", // 8192 KB = 8MB
            "cantidad" => "required|numeric",
            "categoria_id" => "required" // Asegúrate que la tabla y campo son correctos
        ], $messages);

        if ($validator->fails()) {
            return ApiResponse::error($validator->errors(), 422);
        }

        DB::beginTransaction();

        // Guardar imagen
        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . $originalName;
        $path = public_path('images');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file->move($path, $fileName);
        $absolutePath = $path . '/' . $fileName;

        // Crear producto
        $producto = CtlProductos::create([
            'nombre' => $request->nombre,
            'precio' => $request->precio,
            'activo' => true,
        ]);

        // Crear inventario
        $inventario = CtlInventerio::create([
            'cantidad' => $request->cantidad,
            'product_id' => $producto->id
        ]);

        // Relación categoría-producto
        CtlCategoriasProductos::create([
            'producto_id' => $producto->id,
            'categoria_id' => $request->categoria_id
        ]);

        // Guardar imagen en tabla
        CtlImageProductos::create([
            'nombre' => $originalName,
            'path' => $absolutePath,
            'producto_id' => $producto->id,
            'relative_path' => 'images/'. $fileName
        ]);

        DB::commit();

        return ApiResponse::success('Se creó el producto correctamente', 200, $producto);

    } catch (\Exception $e) {
        DB::rollBack();
        return ApiResponse::error("Error interno: " . $e->getMessage(), 500);
    }
}


    public function updateInventario(Request $request, $id){
        try {
            //code...
            //return $request->all();
            $inventario = CtlInventerio::find($id);
            $inventario->cantidad =$inventario->cantidad +  $request->cantidad;
            if ($inventario->save()) {
                # code...
                return ApiResponse::success('Actualizo inventario',200);
            }
        } catch (\Exception $th) {
            //throw $th;
            return ApiResponse::error($th->getMessage(),422);
        }
    }
    public function deleteProducto($id){
        try {
            //code...
            $producto = CtlProductos::find($id);
            $producto->activo = !$producto->activo;
            if ($producto->save()) {
                return ApiResponse::success('Se actualizo el producto',200, $producto);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
