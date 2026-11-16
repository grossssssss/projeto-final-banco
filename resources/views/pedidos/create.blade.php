@extends('layouts.app')

@section('content')
<h2>Abrir Novo Pedido</h2>
<div class="card">
    <div class="card-body">
        <form action="{{ route('pedidos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Selecione o Cliente</label>
                <select name="cliente_id" class="form-select" required>
                    <option value="">Escolha um cliente...</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nome }} ({{ $cliente->cpf_cnpj }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success">Criar Pedido</button>
            <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@endsection