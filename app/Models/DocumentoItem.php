<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentoItem extends Model
{
    protected $table = 'documento_items';

    protected $fillable = [
        'documento_id',
        'item_id',
        'codarticulo',
        'descripcion',
        'unidadmedida',
        'cantidad',
    ];

    protected function casts(): array
    {
        return ['cantidad' => 'decimal:2'];
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class);
    }
}
