@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Auditoria de Preços: {{ $produto->nome }}</h2>
    <a href="{{ route('produtos.index') }}" class="btn btn-secondary">Voltar</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <p><strong>Categoria:</strong> {{ $produto->categoria }}</p>
        <p><strong>Preço Atual:</strong> R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
        <p class="mb-0"><strong>Estoque Atual:</strong> {{ $produto->estoque }} unidades</p>
    </div>
</div>

<h4>Histórico de Alterações (Trigger: <code>trg_auditar_preco_produto</code>)</h4>
<div class="card">
    <div class="card-body">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Preço Antigo</th>
                    <th>Preço Novo</th>
                    <th>Data da Alteração</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produto->auditorias as $auditoria)
                <tr>
                    <td>R$ {{ number_format($auditoria->preco_antigo, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format($auditoria->preco_novo, 2, ',', '.') }}</td>
                    <td>{{ $auditoria->alterado_em }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">Nenhuma alteração de preço registrada ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection