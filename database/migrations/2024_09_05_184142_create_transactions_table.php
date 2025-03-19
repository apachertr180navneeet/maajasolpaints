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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('message')->nullable();
            $table->integer('points');
            $table->string('transaction_id')->nullable();
            $table->enum('transaction_type', ['gift', 'cash', 'upi', 'account', 'qr','product'])->default('qr');
            $table->string('remark')->nullable();

            $table->enum('status', ['pending', 'approved', 'completed', 'rejected'])->default('pending');
            $table->enum('type', ['credit', 'debit']);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
