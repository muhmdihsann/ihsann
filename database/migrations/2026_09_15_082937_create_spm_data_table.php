<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spm_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regency_id')->constrained()->cascadeOnDelete();
            $table->foreignId('import_history_id')->nullable()->constrained()->nullOnDelete();
            $table->year('year');

            // Infrastruktur
            $table->integer('jml_kecamatan')->nullable()->default(0);
            $table->integer('jml_pos')->nullable()->default(0);
            $table->float('pr_pos_kecamatan')->nullable()->default(0);

            // SDM
            $table->integer('total_sdm')->nullable()->default(0);
            $table->integer('sdm_sertifikat')->nullable()->default(0);
            $table->float('pr_sdm_sertifikat')->nullable()->default(0);

            // REDKAR (Pemberdayaan Masyarakat)
            $table->integer('jml_desa')->nullable()->default(0);
            $table->integer('jml_redkar')->nullable()->default(0);
            $table->float('pr_redkar')->nullable()->default(0);

            // Dimensi Penilaian
            $table->float('dimensi_kelembagaan')->nullable()->default(0);
            $table->float('dimensi_perencanaan')->nullable()->default(0);
            $table->float('dimensi_capaian')->nullable()->default(0);
            $table->float('dimensi_sarpras')->nullable()->default(0);
            $table->float('dimensi_sdm_sertifikat')->nullable()->default(0);
            $table->float('dimensi_pemberdayaan')->nullable()->default(0);

            // Hasil Akhir
            $table->float('nilai_akhir')->nullable()->default(0);
            $table->enum('kategori', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang'])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spm_data');
    }
};
