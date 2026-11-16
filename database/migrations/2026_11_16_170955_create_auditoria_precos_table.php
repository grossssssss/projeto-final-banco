<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('auditoria_precos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
        $table->decimal('preco_antigo', 10, 2);
        $table->decimal('preco_novo', 10, 2);
        $table->timestamp('alterado_em')->useCurrent();
    });
}
};
