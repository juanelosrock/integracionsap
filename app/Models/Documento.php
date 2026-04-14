<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
        'numero',
        'fecha',
        'proveedor_id',
        'codigo_tienda',
        'nombre_tienda',
        'estado',
        'observaciones',
        'created_by',
        'sap_http_code',
        'sap_respuesta',
        'sap_enviado_at',
    ];

    protected function casts(): array
    {
        return [
            'fecha'          => 'date',
            'sap_respuesta'  => 'array',
            'sap_enviado_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Documento $doc) {
            $doc->numero = static::generarNumero();
        });
    }

    public static function generarNumero(): string
    {
        $anio   = now()->year;
        $ultimo = static::whereYear('created_at', $anio)->max('id') ?? 0;
        return 'DOC-' . $anio . '-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DocumentoItem::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getEstadoBadgeClass(): string
    {
        return match($this->estado) {
            'confirmado' => 'bg-blue-100 text-blue-800',
            'enviado'    => 'bg-green-100 text-green-800',
            default      => 'bg-yellow-100 text-yellow-800',
        };
    }
}
