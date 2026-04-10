<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'codigo_sap',
        'nombre',
        'nit',
        'email',
        'telefono',
        'direccion',
        'ciudad',
        'pais',
        'contacto',
        'cargo_contacto',
        'telefono_contacto',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function proveedorItems(): HasMany
    {
        return $this->hasMany(ProveedorItem::class);
    }

    /**
     * Retorna los Items (BD remota) asociados a este proveedor.
     */
    public function items(): Collection
    {
        $ids = $this->proveedorItems()->pluck('item_id');

        if ($ids->isEmpty()) {
            return new Collection();
        }

        return Item::whereIn('ID', $ids)->orderBy('codarticulo')->get();
    }

    /**
     * IDs de items ya asociados.
     */
    public function itemIds(): array
    {
        return $this->proveedorItems()->pluck('item_id')->map(fn($id) => (int)$id)->toArray();
    }
}
