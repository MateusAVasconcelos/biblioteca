<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('isbn', 20)->unique();
            $table->string('publisher')->nullable();
            $table->string('genre')->nullable();
            $table->date('published_at')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('copies')->default(1);
            $table->unsignedInteger('available_copies')->default(1);
            $table->decimal('loan_price', 8, 2)->nullable();
            $table->decimal('sale_price', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
