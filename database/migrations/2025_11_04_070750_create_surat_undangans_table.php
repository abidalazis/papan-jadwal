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
    Schema::create('surat_undangans', function (Blueprint $table) {
        $table->id();
        $table->string('nomor_surat')->unique();
        $table->string('judul');
        $table->date('tanggal_acara');
        $table->string('lokasi')->nullable();
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_undangans');
    }
};
