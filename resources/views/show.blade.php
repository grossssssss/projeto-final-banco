@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Cliente: {{ $cliente->nome }}</h2>
    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Voltar</a>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p><strong>E-mail:</strong> {{ $cliente->email }}</p>
                <p><strong>CPF/CNPJ:</strong> {{ $cliente->cpf_cnpj }}</p>
                <p class="mb-0"><strong>Telefone:</strong> {{ $cliente->telefone ?? '-' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-body">
                <h5 class="card-title text-success">Total Gasto em Pedidos Concluídos</h5>
                <p class="display-6 mb-1">R$ {{ number_format($totalGasto, 2, ',', '.') }}</p>
                <small class="text-muted">Calculado via PostgreSQL Function: <code>fn_total_gasto_cliente()</code></small>
            </div>
        </div>
    </div>
</div>

<h4>Histórico de Pedidos</h4>
<div class="card">
    <div class="card-body">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Pedido #</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Valor Total</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cliente->pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->id }}</td>
                    <td>{{ $pedido->data_pedido }}</td>
                    <td><span class="badge bg-{{ $pedido->status === 'concluido' ? 'success' : 'warning' }}">{{ $pedido->status }}</span></td>
                    <td>R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</td>
                    <td><a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-primary">Abrir</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Nenhum pedido para este cliente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection