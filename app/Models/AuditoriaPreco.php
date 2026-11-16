<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditoriaPreco extends Model
{
    protected $table = 'auditoria_precos';

    public $timestamps = false;

    protected $fillable = ['produto_id', 'preco_antigo', 'preco_novo', 'alterado_em'];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}