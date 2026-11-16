<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Services\ProdutoService;
use Illuminate\Http\Request;

class ProdutoApiController extends Controller
{
    public function __construct(private ProdutoService $produtoService)
    {
    }

    public function index()
    {
        return response()->json($this->produtoService->listarTodos());
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
        ]);

        return response()->json($this->produtoService->criar($dados), 201);
    }

    public function show(Produto $produto)
    {
        $produto->load(['auditorias' => fn ($q) => $q->orderByDesc('alterado_em')]);

        return response()->json($produto);
    }

    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
        ]);

        return response()->json($this->produtoService->atualizar($produto, $dados));
    }

    public function destroy(Produto $produto)
    {
        $this->produtoService->excluir($produto);

        return response()->json(['message' => 'Produto removido com sucesso.']);
    }

    public function reajustar(Request $request)
    {
        $dados = $request->validate([
            'categoria' => 'required|string',
            'percentual' => 'required|numeric',
        ]);

        $this->produtoService->reajustarPrecoCategoria($dados['categoria'], (float) $dados['percentual']);

        return response()->json([
            'message' => "Reajuste de {$dados['percentual']}% aplicado via sp_reajustar_preco_categoria.",
        ]);
    }
}