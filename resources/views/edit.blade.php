@extends('layouts.app')

@section('content')
<h2>Editar Cliente</h2>
<div class="card">
    <div class="card-body">
        <form action="{{ route('clientes.update', $cliente) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="{{ old('nome', $cliente->nome) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $cliente->email) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">CPF/CNPJ</label>
                <input type="text" name="cpf_cnpj" class="form-control" value="{{ old('cpf_cnpj', $cliente->cpf_cnpj) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $cliente->telefone) }}">
            </div>
            <button type="submit" class="btn btn-success">Atualizar</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Voltar</a>
        </form>
    </div>
</div>
@endsection