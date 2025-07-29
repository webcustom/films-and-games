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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('slug')->unique();//->default('');
            $table->string('title')->default('');
            // $table->string('img')->nullable();
            $table->string('img_medium')->nullable();
            $table->string('img_thumbnail')->nullable();
            $table->text('description')->nullable();
            // $table->json('resource_id')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamp('published_at')->nullable(true);
            $table->json('sort_elems')->nullable();

            $table->foreignId('category_id')->nullable()->constrained()->cascadeOnUpdate(); //->nullable(); позволяет сделать значение collection_id равным null
            // $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade'); // Внешний ключ на users
            // $table->unsignedBigInteger('collection_id')->nullable(true);
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
