<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('date_commande');
            $table->date('date_prestation');
            $table->time('heure_livraison');

            $table->double('prix_menu');
            $table->integer('nombre_personne');
            $table->double('prix_livraison');

            $table->string('statut');
            $table->boolean('pret_materiel')->default(false);
            $table->boolean('restitution_materiel')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};