<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
        $table->timestamp('data_pedido')->useCurrent();
        $table->string('status', 30)->default('pendente');
        $table->decimal('valor_total', 10, 2)->default(0);
        $table->timestamps();
    });
}
};
