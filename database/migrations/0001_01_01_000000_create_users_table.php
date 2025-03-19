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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile_number')->unique();
            $table->string('password');
            $table->string('bank_name')->nullable(); // bank name
            $table->string('account_number')->nullable()->unique(); // bank account number
            $table->string('ifsc_code')->nullable(); // bank IFSC code
            $table->decimal('balance', 15, 2)->default(0.00); // User balance
            $table->string('otp')->nullable(); // OTP for verification
            $table->timestamp('otp_expires_at')->nullable(); // OTP expiration time
            $table->enum('status', ['active', 'inactive', 'blocked', 'pending'])->default('pending');
            $table->boolean('is_admin')->default(0); // Admin status
            $table->text('address')->nullable(); // User address
            $table->date('dob')->nullable(); // Date of Birth
            $table->string('aadhaar_image')->nullable(); // Aadhaar image file path
            $table->string('profile_image')->nullable(); // Profile image file path
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
