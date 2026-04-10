<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Serie extends Model
{
    protected $connection = 'mysql_remote';
    protected $table = 'series';
    protected $primaryKey = 'serie';

    public $incrementing = false;
    public $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'serie',
        'descripcion',
        'punto_sap',
        'empresa_sap',
        'cebe_sap',
        'ceco_sap',
        'centro_sap',
        'zona_sap',
        'cuentaventa_sap',
        'codpostal_sap',
        'codciudad_sap',
        'nomciudad_sap',
        'region_sap',
        'storageloc_sap',
        'cuentacaja_sap',
        'cunetacaja_sap',
        'glaccount_sap',
        'glaccountnc_sap',
    ];
}
