<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('avis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('note');
            $table->text('commentaire')->nullable();
            $table->string('statut', 50)->default('en attente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis');
    }
};
