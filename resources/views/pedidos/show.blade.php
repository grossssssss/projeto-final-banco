@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Pedido #{{ $pedido->id }} - {{ $pedido->cliente->nome }}</h2>
    <div class="d-flex gap-2">
        @if($pedido->status === 'pendente')
            <form action="{{ route('pedidos.finalizar', $pedido) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">Finalizar Pedido (Procedure)</button>
            </form>
        @endif
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body d-flex justify-content-between">
        <div><strong>Status:</strong> <span class="badge bg-{{ $pedido->status === 'concluido' ? 'success' : 'warning' }}">{{ $pedido->status }}</span></div>
        <div><strong>Valor Total:</strong> <span class="fs-5 fw-bold text-primary">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</span></div>
    </div>
</div>

@if($pedido->status === 'pendente')
<div class="card mb-4">
    <div class="card-header">Adicionar Item (Dispara Trigger de Estoque + Function Total)</div>
    <div class="card-body">
        <form action="{{ route('pedidos.itens.store', $pedido) }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-6">
                <select name="produto_id" class="form-select" required>
                    <option value="">Selecione um produto...</option>
                    @foreach($produtos as $produto)
                        <option value="{{ $produto->id }}">
                            {{ $produto->nome }} - R$ {{ number_format($produto->preco, 2, ',', '.') }} (Estoque: {{ $produto->estoque }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" name="quantidade" class="form-control" min="1" value="1" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Adicionar</button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Qtd</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedido->itens as $item)
                <tr>
                    <td>{{ $item->produto->nome }}</td>
                    <td>{{ $item->quantidade }}</td>
                    <td>R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($item->quantidade * $item->preco_unitario, 2, ',', '.') }}</td>
                    <td>
                        @if($pedido->status === 'pendente')
                        <form action="{{ route('pedidos.itens.destroy', [$pedido, $item]) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Remover</button>
                        </form>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Nenhum item adicionado ao pedido.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection