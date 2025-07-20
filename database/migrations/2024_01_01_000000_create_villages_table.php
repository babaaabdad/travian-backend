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
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            // We use unsignedBigInteger for the foreign key to match the 'id' type on the users table.
            $table->unsignedBigInteger('user_id')->unique(); 
            
            // Resources are stored as floats to allow for fractional accrual over time.
            $table->double('wood', 15, 4)->default(100.0000);
            $table->double('clay', 15, 4)->default(100.0000);
            $table->double('iron', 15, 4)->default(100.0000);
            
            // Building levels.
            $table->integer('woodcutter_level')->default(1);
            $table->integer('clay_pit_level')->default(1);
            $table->integer('iron_mine_level')->default(1);
            
            // This timestamp is crucial for calculating resource generation.
            $table->timestamp('last_updated');
            
            $table->timestamps(); // Adds created_at and updated_at columns.

            // Defines the foreign key relationship to the users table.
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};