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
        Schema::table('fields', function (Blueprint $table) {
            // Modify the column to be nullable
            $table->foreignId('agent_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    

    public function down(): void
    {
        Schema::table('fields', function (Blueprint $table) {
            // Revert back if needed
            $table->foreignId('agent_id')->nullable(false)->change();
        });
    }


    
};