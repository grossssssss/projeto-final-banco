<?php

use App\Http\Controllers\Web\ClienteController;
use App\Http\Controllers\Web\PedidoController;
use App\Http\Controllers\Web\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('produtos.index');
});

Route::resource('clientes', ClienteController::class);

Route::post('produtos/reajustar', [ProdutoController::class, 'reajustar'])->name('produtos.reajustar');
Route::resource('produtos', ProdutoController::class);

Route::post('pedidos/{pedido}/itens', [PedidoController::class, 'adicionarItem'])->name('pedidos.itens.store');
Route::delete('pedidos/{pedido}/itens/{item}', [PedidoController::class, 'removerItem'])->name('pedidos.itens.destroy');
Route::post('pedidos/{pedido}/finalizar', [PedidoController::class, 'finalizar'])->name('pedidos.finalizar');
Route::resource('pedidos', PedidoController::class)->except(['edit', 'update']);