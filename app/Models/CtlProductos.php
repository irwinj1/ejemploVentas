<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class CtlProductos extends Model
{
    //
    use HasFactory, Notifiable, HasRoles;
    protected $table = 'ctl_productos';
    protected $fillable = [
        'id',
        'nombre',
        'precio',
        'image',
        'categoria_id',
        'path_image',
        'activo',
       
    ];


    public function productosCategoria(){
        return $this->belongsTo(CtlCategoriasProductos::class,'id','producto_id');
    }
    public function inventario(){
        return $this->belongsTo(CtlInventerio::class,'id','product_id');
    }
    public function detallePedido(){
        return $this->hasMany(MntDetallePedidos::class,'producto_id','id');
    }
    public function imageProductos(){
        return $this->belongsTo(CtlImageProductos::class,'id','producto_id');
    }
}
