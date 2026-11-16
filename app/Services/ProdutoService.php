<?php

namespace App\Services;

use App\Models\Produto;
use Illuminate\Support\Facades\DB;

class ProdutoService
{
    public function listarTodos()
    {
        return Produto::orderBy('nome')->get();
    }

    public function criar(array $dados): Produto
    {
        return Produto::create($dados);
    }

    public function atualizar(Produto $produto, array $dados): Produto
    {
        // Ao alterar 'preco', a trigger trg_auditar_preco_produto grava o histórico automaticamente
        $produto->update($dados);
        return $produto->fresh();
    }

    public function excluir(Produto $produto): void
    {
        $produto->delete();
    }

    public function reajustarPrecoCategoria(string $categoria, float $percentual): void
    {
        // Chama a Stored Procedure e dispara a Trigger de auditoria em lote
        DB::statement('CALL sp_reajustar_preco_categoria(?, ?)', [$categoria, $percentual]);
    }
}