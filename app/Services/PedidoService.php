<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;

class PedidoService
{
    public function criarPedido(int $clienteId): Pedido
    {
        return Pedido::create([
            'cliente_id' => $clienteId,
            'status' => 'pendente',
            'valor_total' => 0,
        ]);
    }

    public function adicionarItem(int $pedidoId, int $produtoId, int $quantidade): PedidoItem
    {
        $produto = Produto::findOrFail($produtoId);

        // Ao inserir em pedido_itens, a trigger trg_atualizar_estoque_pedido_item
        // valida o estoque, dá baixa e recalcula valor_total via fn_calcular_total_pedido
        return PedidoItem::create([
            'pedido_id' => $pedidoId,
            'produto_id' => $produto->id,
            'quantidade' => $quantidade,
            'preco_unitario' => $produto->preco,
        ]);
    }

    public function removerItem(PedidoItem $item): void
    {
        // A trigger estorna o estoque e recalcula o total automaticamente
        $item->delete();
    }

    public function finalizarPedido(int $pedidoId): void
    {
        // Executa a Stored Procedure no PostgreSQL
        DB::statement('CALL sp_finalizar_pedido(?)', [$pedidoId]);
    }

    public function totalGastoCliente(int $clienteId): float
    {
        // Executa a Function no PostgreSQL
        $resultado = DB::selectOne('SELECT fn_total_gasto_cliente(?) AS total', [$clienteId]);
        return (float) ($resultado->total ?? 0);
    }
}