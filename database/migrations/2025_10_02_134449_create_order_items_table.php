<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();   // -> orders.id
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); // -> products.id (nullable)
            $table->string('name');                        // product name snapshot
            $table->decimal('price', 12, 2)->default(0);   // unit price at order time
            $table->unsignedInteger('qty')->default(1);    // quantity
            $table->decimal('total', 12, 2)->default(0);   // price * qty
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
