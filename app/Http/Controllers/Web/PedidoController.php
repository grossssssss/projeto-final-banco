<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use App\Services\PedidoService;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function __construct(private PedidoService $pedidoService)
    {
    }

    public function index()
    {
        $pedidos = Pedido::with('cliente')->orderByDesc('id')->get();
        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('pedidos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
        ]);

        $pedido = $this->pedidoService->criarPedido((int) $dados['cliente_id']);

        return redirect()->route('pedidos.show', $pedido)
            ->with('success', 'Pedido criado! Adicione os itens abaixo.');
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'itens.produto']);
        $produtos = Produto::orderBy('nome')->get();

        return view('pedidos.show', compact('pedido', 'produtos'));
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return redirect()->route('pedidos.index')
            ->with('success', 'Pedido excluído!');
    }

    public function adicionarItem(Request $request, Pedido $pedido)
    {
        $dados = $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        try {
            $this->pedidoService->adicionarItem(
                $pedido->id,
                (int) $dados['produto_id'],
                (int) $dados['quantidade']
            );

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Item adicionado! Estoque e total atualizados pela Trigger.');
        } catch (\Exception $e) {
            return redirect()->route('pedidos.show', $pedido)
                ->with('error', 'Erro do PostgreSQL: ' . $e->getMessage());
        }
    }

    public function removerItem(Pedido $pedido, PedidoItem $item)
    {
        $this->pedidoService->removerItem($item);

        return redirect()->route('pedidos.show', $pedido)
            ->with('success', 'Item removido e estoque estornado pela Trigger.');
    }

    public function finalizar(Pedido $pedido)
    {
        try {
            $this->pedidoService->finalizarPedido($pedido->id);

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Pedido finalizado via Stored Procedure!');
        } catch (\Exception $e) {
            return redirect()->route('pedidos.show', $pedido)
                ->with('error', 'Erro do PostgreSQL: ' . $e->getMessage());
        }
    }
}