<?php

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
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('reg_number', 10)->unique();
            $table->string('observation_CA', 5000);
            $table->string('observation_ES', 5000);
            $table->string('observation_EN', 5000);
            $table->string('email', 100);
            $table->string('phone', 100);
            $table->string('website', 100);
            $table->enum('accessType', ['B', 'M', 'A']);
            $table->decimal('totalScore', 8, 2)->default(0);
            $table->integer('countScore')->default(0);
            $table->timestamps();
            $table->foreignId('address_id')->constrained()->onUpdate('restrict')->onDelete('restrict');
            $table->foreignId('space_type_id')->constrained()->onUpdate('restrict')->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
