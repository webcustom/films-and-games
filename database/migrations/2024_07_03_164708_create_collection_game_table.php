<?php

use App\Models\Collection;
use App\Models\Game;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    // создаем миграцию для связи двух таблиц collections и films по отношению belongsToMany (collection_film в названии должно быть по алфавиту collections идет раньше чем films )
    public function up(): void
    {
        Schema::create('collection_game', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Collection::class)->nullable()->constrained()->cascadeOnUpdate(); //->nullable(); позволяет сделать значение collection_id равным null
            $table->foreignIdFor(Game::class)->nullable()->constrained()->cascadeOnUpdate(); //->nullable(); позволяет сделать значение collection_id равным null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_game');
    }
};
