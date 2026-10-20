<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('import_histories', function (Blueprint $table): void {
            $table->string('status')->default('draft')->change();
        });

        DB::table('import_histories')
            ->whereNotIn('status', ['draft', 'published'])
            ->update(['status' => 'draft']);

        Schema::table('import_histories', function (Blueprint $table): void {
            $table->enum('status', ['draft', 'published'])->default('draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('import_histories', function (Blueprint $table): void {
            $table->string('status')->default('draft')->change();
        });
    }
};
