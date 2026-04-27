<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('allergene_plat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plat_id')
                ->constrained('plats')
                ->cascadeOnDelete();

            $table->foreignId('allergene_id')
                ->constrained('allergenes')
                ->cascadeOnDelete();

            $table->unique(['plat_id', 'allergene_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allergene_plat');
    }
};
