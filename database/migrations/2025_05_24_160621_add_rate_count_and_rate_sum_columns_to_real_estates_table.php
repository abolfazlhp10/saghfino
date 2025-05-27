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
        Schema::table('real_estates', function (Blueprint $table) {
            
            $table->integer('rate_count')->default(0)->after('image');
            $table->integer('rate_sum')->default(0)->after('rate_count');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_estates', function (Blueprint $table) {
            $table->dropColumn('rate_count');
            $table->dropColumn('rate_sum');

        });
    }
};
