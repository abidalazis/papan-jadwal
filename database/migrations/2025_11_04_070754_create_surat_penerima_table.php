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
    Schema::create('surat_penerima', function (Blueprint $table) {
        $table->id();
        $table->foreignId('surat_undangan_id')->constrained('surat_undangans')->onDelete('cascade');
        $table->foreignId('penerima_id')->constrained('penerimas')->onDelete('cascade');
        $table->boolean('status_kirim')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_penerima');
    }
};
