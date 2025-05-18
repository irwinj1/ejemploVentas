<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class CtlCategoriasProductos extends Model
{
    //
    use HasFactory, Notifiable, HasRoles;
    protected $table = 'ctl_categorias_productos';
    protected $fillable = [
        'categoria_id',
        'producto_id',

    ];

    public function productos(){
        return $this->belongsTo(CtlProductos::class,'id','producto_id');
    }
    public function categorias(){
        return $this->belongsTo(CtlCategoria::class,'categoria_id','id');
    }
}
