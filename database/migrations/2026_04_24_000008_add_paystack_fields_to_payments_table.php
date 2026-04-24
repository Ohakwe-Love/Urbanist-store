<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('payment_reference');
            $table->string('currency', 10)->default('USD')->after('gateway');
            $table->text('authorization_url')->nullable()->after('currency');
            $table->string('access_code')->nullable()->after('authorization_url');
            $table->json('gateway_response')->nullable()->after('access_code');
            $table->timestamp('verified_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'gateway',
                'currency',
                'authorization_url',
                'access_code',
                'gateway_response',
                'verified_at',
            ]);
        });
    }
};
