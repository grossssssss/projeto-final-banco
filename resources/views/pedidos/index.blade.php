@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Pedidos</h2>
    <a href="{{ route('pedidos.create') }}" class="btn btn-primary">Novo Pedido</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Valor Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $pedido)
                <tr>
                    <td>#{{ $pedido->id }}</td>
                    <td>{{ $pedido->cliente->nome }}</td>
                    <td>{{ $pedido->data_pedido }}</td>
                    <td><span class="badge bg-{{ $pedido->status === 'concluido' ? 'success' : 'warning' }}">{{ $pedido->status }}</span></td>
                    <td>R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-sm btn-info text-white">Gerenciar</a>
                        <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST" onsubmit="return confirm('Excluir pedido?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Nenhum pedido registrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection