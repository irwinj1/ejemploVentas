<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class CtlImageProductos extends Model
{
    //
    use HasFactory, Notifiable, HasRoles;

    protected $table = 'ctl_image_productos';
    protected $fillable = [

        'nombre',
        'path',
        'producto_id'
    ];

    public function productos(){
        $this->belongsTo(CtlProductos::class,'id','producto_id');
    }
}
