<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('horaires', function (Blueprint $table) {
            $table->id();
            $table->string('jour', 50);
            $table->string('heure_ouverture', 50);
            $table->string('heure_fermeture', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horaires');
    }
};
