@extends('layouts.app')

@section('content')
<h2>Editar Produto</h2>
<div class="card">
    <div class="card-body">
        <form action="{{ route('produtos.update', $produto) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="{{ old('nome', $produto->nome) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <input type="text" name="categoria" class="form-control" value="{{ old('categoria', $produto->categoria) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" name="preco" class="form-control" value="{{ old('preco', $produto->preco) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Estoque</label>
                <input type="number" name="estoque" class="form-control" value="{{ old('estoque', $produto->estoque) }}" required>
            </div>
            <button type="submit" class="btn btn-success">Atualizar</button>
            <a href="{{ route('produtos.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@endsection