<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed'])->default('confirmed')->after('notes');
            $table->unsignedBigInteger('confirmed_by')->nullable()->after('status');
            $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
            $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
        });

        DB::table('payments')->update([
            'status' => 'confirmed',
            'confirmed_at' => DB::raw('payment_date'),
        ]);
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropColumn(['confirmed_at', 'confirmed_by', 'status']);
        });
    }
};
