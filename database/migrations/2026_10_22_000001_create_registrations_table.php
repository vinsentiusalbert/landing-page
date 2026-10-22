<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('jenis_kelamin', 10)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp', 20)->index();
            $table->string('jenis_usaha', 50)->nullable();
            $table->unsignedTinyInteger('lama_usaha_thn')->nullable();
            $table->decimal('omset_bulanan', 14, 2)->nullable();
            $table->decimal('pinjaman_eksisting', 14, 2)->nullable();
            $table->string('fasilitas_diajukan', 30)->nullable();
            $table->decimal('pinjaman_diajukan', 14, 2)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
