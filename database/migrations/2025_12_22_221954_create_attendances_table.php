<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->foreignId('check_in_qr_id')->nullable()->constrained('qr_codes')->nullOnDelete();
            $table->foreignId('check_out_qr_id')->nullable()->constrained('qr_codes')->nullOnDelete();
            $table->enum('status', ['on_time', 'late', 'incomplete', 'absent'])->default('absent');
            $table->decimal('total_hours', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->comment('Admin who forced entry')->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
            $table->index('date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};