<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('reference', 24)->unique();
            $table->string('contact_name', 120);
            $table->string('company_name', 160);
            $table->string('email', 160);
            $table->string('phone', 40);
            $table->string('headcount_band', 40);
            $table->json('pain_points');
            $table->text('notes')->nullable();
            $table->date('preferred_date')->nullable();
            $table->string('preferred_slot', 40)->nullable();
            $table->string('status', 20)->default('new');
            $table->string('source', 40)->default('web');
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_requests');
    }
};
