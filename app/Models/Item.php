<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $connection = 'mysql_remote';
    protected $table = 'items';
    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'codarticulo',
        'descripcion',
        'unidadmedida',
        'refproveedor',
        'sector_sap',
        'ref_sap',
    ];
}
