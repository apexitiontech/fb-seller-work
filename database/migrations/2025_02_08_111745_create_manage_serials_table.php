<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('manage_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->string('batch_number')->unique();
            $table->string('serial_number');
            $table->string('barcode_path')->nullable();
            $table->boolean('is_link')->default(0);
            $table->integer('uploaded_by');
            $table->timestamps();

            $table->unique(['vendor_id', 'serial_number']);
        });
    }

  
    public function down(): void
    {
        Schema::dropIfExists('manage_serials');
    }
};
