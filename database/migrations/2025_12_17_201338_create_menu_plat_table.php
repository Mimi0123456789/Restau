<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('menu_plat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')
                ->constrained('menus')
                ->cascadeOnDelete();

            $table->foreignId('plat_id')
                ->constrained('plats')
                ->cascadeOnDelete();

            $table->unique(['menu_id', 'plat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_plat');
    }
};
