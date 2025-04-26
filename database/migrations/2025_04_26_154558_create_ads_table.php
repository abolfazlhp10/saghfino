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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->text('address');
            $table->integer('area');
            $table->integer('floor');
            $table->integer('number_of_floors');
            $table->integer('rooms');
            $table->string('city');
            $table->enum('type_of_wc', ['farangi', 'wc', 'both']);
            $table->boolean('elevator');
            $table->boolean('parking');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
