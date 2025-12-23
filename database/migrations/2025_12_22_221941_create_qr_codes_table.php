<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['check_in', 'check_out']);
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('code');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};