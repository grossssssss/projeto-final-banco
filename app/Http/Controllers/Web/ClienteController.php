<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Services\PedidoService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'cpf_cnpj' => 'required|string|max:18|unique:clientes,cpf_cnpj',
            'telefone' => 'nullable|string|max:20',
        ]);

        Cliente::create($dados);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Cliente $cliente, PedidoService $pedidoService)
    {
        $cliente->load('pedidos');
        // Chama a Function fn_total_gasto_cliente no PostgreSQL
        $totalGasto = $pedidoService->totalGastoCliente($cliente->id);

        return view('clientes.show', compact('cliente', 'totalGasto'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
            'cpf_cnpj' => 'required|string|max:18|unique:clientes,cpf_cnpj,' . $cliente->id,
            'telefone' => 'nullable|string|max:20',
        ]);

        $cliente->update($dados);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente excluído com sucesso!');
    }
}