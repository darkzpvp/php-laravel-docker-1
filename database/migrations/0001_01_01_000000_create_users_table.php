<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('free_prompts')->default(10)->nullable();
            $table->string('imagen')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('estado')->default('Desconectado');
            $table->string('ultima_sesion')->nullable();
            $table->boolean('rol')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('suscripciones', function (Blueprint $table) {

            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo', ['basico', 'estandar', 'premium']);
            $table->integer('prompts_disponibles')->default(0);
            $table->integer('precio')->default(0);
            $table->timestamps();
            $table->timestamp('fecha_expiracion')->nullable()->default(Carbon::now()->addMonth());

        });


        Schema::create('prompts', function (Blueprint $table) {

            $table->id();
            $table->text('texto');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {

            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

        });

        Schema::create('informacion_personal', function (Blueprint $table) {

            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('numero_telefono');
            $table->string('pais');
            $table->string('poblacion');
            $table->string('provincia');
            $table->string('direccion');
            $table->string('cp');
            $table->string('nif_nie');
            $table->timestamps();
        });


        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
