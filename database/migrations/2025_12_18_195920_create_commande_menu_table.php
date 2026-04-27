<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commande_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained('commandes')->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->integer('quantite')->default(1);
            $table->double('prix_unitaire');
            $table->double('prix_total');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_menu');
    }
};