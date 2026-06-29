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
        Schema::create('course_master', function (Blueprint $table) {
            $table->id('course_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->foreignId('programme_id')->nullable()->constrained('programme_master', 'programme_id')->nullOnDelete();

            $table->foreignId('created_by')->nullable()->constrained('users', 'id')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users', 'id')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_master');
    }
};
