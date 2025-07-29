<?php

use App\Models\Collection;
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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->timestamps();


            // $table->foreignId('category_id')->nullable()->constrained('categories');

            // $table->json('category_id')->nullable();

            $table->string('title')->default('');
            $table->string('slug')->unique();
            // $table->string('img')->nullable();
            $table->string('img_medium')->nullable();
            $table->string('img_thumbnail')->nullable();

            $table->json('additional_imgs')->nullable();

            $table->text('iframe_video')->nullable();


            $table->text('description')->nullable();

            $table->string('rating_imdb')->nullable();
            $table->string('rating_kinopoisk')->nullable();
            $table->string('release')->nullable();
            $table->string('duration')->nullable();

            $table->string('genre')->nullable();
            $table->string('country')->nullable();
            $table->string('budget')->nullable();
            $table->string('fees_usa')->nullable();
            $table->string('fees_world')->nullable();
            $table->string('director')->nullable();
            $table->json('cast')->nullable();

            // $table->binary('image')->nullable();
            $table->boolean('published')->default(true);
            $table->timestamp('published_at')->nullable(true);
            // $table->foreignIdFor(Collection::class)->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            // $table->foreignIdFor(Collection::class)->nullable()->constrained()->cascadeOnUpdate(); //->nullable(); позволяет сделать значение collection_id равным null


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
