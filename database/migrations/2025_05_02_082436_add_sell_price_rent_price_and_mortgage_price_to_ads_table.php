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
        Schema::table('ads', function (Blueprint $table) {
            $table->bigInteger('sell_price')->nullable()->after('property_type');
            $table->bigInteger('rent_price')->nullable()->after('property_type');
            $table->bigInteger('mortgage_price')->nullable()->after('property_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {

            $table->dropColumn('sell_price');
            $table->dropColumn('rent_price');
            $table->dropColumn('mortgage_price');
        });
    }
};
