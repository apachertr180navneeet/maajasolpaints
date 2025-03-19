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
        Schema::table('qr_records', function (Blueprint $table) {
            $table->enum('qr_type', ['cash', 'gift', 'product'])->default('cash')->after('used_by');
            $table->boolean('is_product')->default(false)->after('qr_type');
            $table->unsignedBigInteger('product_id')->nullable()->after('is_product');
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->timestamp('scanned_at')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_records', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn(['qr_type', 'product_id', 'is_product', 'scanned_at']);
        });
    }
};
