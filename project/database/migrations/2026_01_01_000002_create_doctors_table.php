<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('specialization');
            $table->string('qualification');
            $table->tinyInteger('experience_years')->unsigned()->default(0);
            $table->decimal('consultation_fee', 10, 2)->default(0.00);
            $table->text('bio')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
