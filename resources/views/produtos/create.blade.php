@extends('layouts.app')

@section('content')
<h2>Novo Produto</h2>
<div class="card">
    <div class="card-body">
        <form action="{{ route('produtos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="{{ old('nome') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <input type="text" name="categoria" class="form-control" value="{{ old('categoria') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Preço (R$)</label>
                <input type="number" step="0.01" name="preco" class="form-control" value="{{ old('preco') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Estoque Inicial</label>
                <input type="number" name="estoque" class="form-control" value="{{ old('estoque', 0) }}" required>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="{{ route('produtos.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@endsection