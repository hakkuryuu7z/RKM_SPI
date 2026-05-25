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
        Schema::create('tbmaster_member', function (Blueprint $table) {
            // Kita jadikan 'kode' sebagai Primary Key karena unik dari API
            $table->string('kode', 20)->primary();

            $table->string('cus_kodeigr', 10)->nullable();
            $table->string('status', 20)->nullable();
            $table->string('no_ktp', 50)->nullable();
            $table->string('nama')->nullable();
            $table->char('jenis_kelamin', 1)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('hp', 20)->nullable();
            $table->string('contact_person1', 20)->nullable();
            $table->string('contact_person2', 20)->nullable();
            $table->text('alamat_2')->nullable();
            $table->string('kota_2', 100)->nullable();
            $table->string('kode_pos_2', 10)->nullable();
            $table->string('kelurahan_2', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->char('flag_member_khusus', 1)->nullable();
            $table->string('kode_outlet', 10)->nullable();
            $table->string('nama_outlet')->nullable();
            $table->string('nama_sub_outlet')->nullable();
            $table->dateTime('tgl_registrasi')->nullable();
            $table->dateTime('kunjungan_pertama')->nullable();
            $table->dateTime('kunjungan_terakhir')->nullable();
            $table->integer('jumlah_kunjungan')->nullable();
            $table->string('segmen_id')->nullable();
            $table->string('nama_segmen')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('koordinat')->nullable();

            // Lat dan Lng diset string biar aman nangkap format apa aja dari API
            $table->string('lat', 50)->nullable();
            $table->string('lng', 50)->nullable();

            // created_at & updated_at bawaan Laravel buat nandain kapan data ini disinkron
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbmaster_member');
    }
};
