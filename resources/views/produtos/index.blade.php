@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Produtos</h2>
    <a href="{{ route('produtos.create') }}" class="btn btn-primary">Novo Produto</a>
</div>

<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        Reajuste de Preços por Categoria (Stored Procedure)
    </div>
    <div class="card-body">
        <form action="{{ route('produtos.reajustar') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-5">
                <select name="categoria" class="form-select" required>
                    <option value="">Selecione uma categoria...</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" name="percentual" class="form-control" placeholder="Percentual (ex: 10 ou -5)" required>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">Aplicar Reajuste</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produtos as $produto)
                <tr>
                    <td>{{ $produto->id }}</td>
                    <td>{{ $produto->nome }}</td>
                    <td>{{ $produto->categoria }}</td>
                    <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                    <td>{{ $produto->estoque }}</td>
                    <td class="d-flex gap-1">
                        <a href="{{ route('produtos.show', $produto) }}" class="btn btn-sm btn-info text-white">Auditoria</a>
                        <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('produtos.destroy', $produto) }}" method="POST" onsubmit="return confirm('Excluir produto?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Nenhum produto cadastrado.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection