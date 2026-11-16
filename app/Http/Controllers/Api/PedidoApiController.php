<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Services\PedidoService;
use Illuminate\Http\Request;

class PedidoApiController extends Controller
{
    public function __construct(private PedidoService $pedidoService)
    {
    }

    public function index()
    {
        return response()->json(Pedido::with(['cliente', 'itens.produto'])->orderByDesc('id')->get());
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        $pedido = $this->pedidoService->criarPedido((int) $dados['cliente_id']);

        return response()->json($pedido, 201);
    }

    public function show(Pedido $pedido)
    {
        return response()->json($pedido->load(['cliente', 'itens.produto']));
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return response()->json(['message' => 'Pedido removido com sucesso.']);
    }

    public function adicionarItem(Request $request, Pedido $pedido)
    {
        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        try {
            $item = $this->pedidoService->adicionarItem(
                $pedido->id,
                (int) $dados['produto_id'],
                (int) $dados['quantidade']
            );

            return response()->json([
                'message' => 'Item adicionado e estoque atualizado pela trigger.',
                'item' => $item,
                'pedido' => $pedido->fresh('itens'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function removerItem(Pedido $pedido, PedidoItem $item)
    {
        $this->pedidoService->removerItem($item);

        return response()->json([
            'message' => 'Item removido e estoque estornado pela trigger.',
            'pedido' => $pedido->fresh('itens'),
        ]);
    }

    public function finalizar(Pedido $pedido)
    {
        try {
            $this->pedidoService->finalizarPedido($pedido->id);

            return response()->json([
                'message' => 'Pedido finalizado via sp_finalizar_pedido.',
                'pedido' => $pedido->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}