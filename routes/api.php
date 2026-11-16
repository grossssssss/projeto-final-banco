<?php

use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\PedidoApiController;
use App\Http\Controllers\Api\ProdutoApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('clientes', ClienteApiController::class);

Route::post('produtos/reajustar', [ProdutoApiController::class, 'reajustar']);
Route::apiResource('produtos', ProdutoApiController::class);

Route::post('pedidos/{pedido}/itens', [PedidoApiController::class, 'adicionarItem']);
Route::delete('pedidos/{pedido}/itens/{item}', [PedidoApiController::class, 'removerItem']);
Route::post('pedidos/{pedido}/finalizar', [PedidoApiController::class, 'finalizar']);
Route::apiResource('pedidos', PedidoApiController::class)->except(['update']);