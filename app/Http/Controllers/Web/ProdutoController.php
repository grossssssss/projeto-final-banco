<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use App\Services\ProdutoService;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function __construct(private ProdutoService $produtoService)
    {
    }

    public function index()
    {
        $produtos = $this->produtoService->listarTodos();
        $categorias = Produto::select('categoria')->distinct()->pluck('categoria');

        return view('produtos.index', compact('produtos', 'categorias'));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
        ]);

        $this->produtoService->criar($dados);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function show(Produto $produto)
    {
        // Carrega o histórico gravado pela Trigger trg_auditar_preco_produto
        $produto->load(['auditorias' => fn ($q) => $q->orderByDesc('alterado_em')]);

        return view('produtos.show', compact('produto'));
    }

    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'categoria' => 'required|string|max:100',
            'preco' => 'required|numeric|min:0.01',
            'estoque' => 'required|integer|min:0',
        ]);

        $this->produtoService->atualizar($produto, $dados);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        $this->produtoService->excluir($produto);

        return redirect()->route('produtos.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    public function reajustar(Request $request)
    {
        $dados = $request->validate([
            'categoria' => 'required|string',
            'percentual' => 'required|numeric',
        ]);

        // Executa a Stored Procedure sp_reajustar_preco_categoria no PostgreSQL
        $this->produtoService->reajustarPrecoCategoria($dados['categoria'], (float) $dados['percentual']);

        return redirect()->route('produtos.index')
            ->with('success', "Reajuste de {$dados['percentual']}% aplicado na categoria {$dados['categoria']}!");
    }
}