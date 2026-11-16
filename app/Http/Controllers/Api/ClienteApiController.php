<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Services\PedidoService;
use Illuminate\Http\Request;

class ClienteApiController extends Controller
{
    public function index()
    {
        return response()->json(Cliente::orderBy('nome')->get());
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'cpf_cnpj' => 'required|string|max:18|unique:clientes,cpf_cnpj',
            'telefone' => 'nullable|string|max:20',
        ]);

        $cliente = Cliente::create($dados);

        return response()->json($cliente, 201);
    }

    public function show(Cliente $cliente, PedidoService $pedidoService)
    {
        $cliente->load('pedidos');

        return response()->json([
            'cliente' => $cliente,
            'total_gasto_concluido' => $pedidoService->totalGastoCliente($cliente->id),
        ]);
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

        return response()->json($cliente);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json(['message' => 'Cliente removido com sucesso.']);
    }
}