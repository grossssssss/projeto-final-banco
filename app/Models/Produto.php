<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    protected $fillable = ['nome', 'categoria', 'preco', 'estoque'];

    public function itens(): HasMany
    {
        return $this->hasMany(PedidoItem::class);
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(AuditoriaPreco::class);
    }
}