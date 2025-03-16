<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class MntDetallePedidos extends Model
{
    //
    use HasFactory, Notifiable, HasRoles;
    protected $table = '_mnt_detalle_pedido';
    protected $fillable = [
        'pedido_id',
        'product_id',
        'cantidad',
        'sub_total'
        ];
    public function pedidos(){
        return $this->belongsTo(MntPedidos::class,'id','product_id');
    }
    public function product(){
        return $this->belongsTo(CtlProductos::class,'id','product_id');
    }
}
