<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('prenom',50);
            $table->string('nom',50);
            $table->string('telephone',10)->nullable();
            $table->string('ville',50)->nullable();
            $table->string('code_postal',50)->nullable();
            $table->string('pays',50)->default('France');
            $table->string('adresse_postale')->nullable();
            $table->foreignId('role_id')->constrained('roles')->default('3');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
