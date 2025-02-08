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
        Schema::create('label_details', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Adds foreign key to 'users' table
            $table->string('from_name')->nullable();
            $table->string('from_company')->nullable();
            $table->string('from_phone')->nullable();
            $table->string('from_address1')->nullable();
            $table->string('from_address2')->nullable();
            $table->string('from_city')->nullable();
            $table->string('from_state')->nullable();
            $table->string('from_postcode')->nullable();
            $table->string('from_country')->nullable();
            $table->string('to_name')->nullable();
            $table->string('to_company')->nullable();
            $table->string('to_phone')->nullable();
            $table->string('to_address1')->nullable();
            $table->string('to_address2')->nullable();
            $table->string('to_city')->nullable();
            $table->string('to_state')->nullable();
            $table->string('to_postcode')->nullable();
            $table->string('to_country')->nullable();
            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('notes')->nullable();
            $table->string('barcode_path_gs128')->nullable();
            $table->string('barcode_path_gs1_datamatrix')->nullable();
            $table->boolean('is_link')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_details');
    }
};
