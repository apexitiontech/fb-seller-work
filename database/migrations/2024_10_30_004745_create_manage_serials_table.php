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
        Schema::create('manage_serials', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number');
            $table->string('serial_number');
            $table->string('barcode_path')->nullable();
            $table->boolean('is_link')->default(0);
            $table->integer('uploaded_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_serials');
    }
};
