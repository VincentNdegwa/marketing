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
        Schema::create('crm_deals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('currency')->default('USD');
            $table->string('stage')->default('prospecting'); // prospecting, qualification, proposal, negotiation, closed_won, closed_lost
            $table->date('expected_close_date')->nullable();
            $table->integer('probability')->nullable(); // 0-100
            $table->foreignId('contact_id')->nullable()->constrained('crm_contacts')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('crm_companies')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('source')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->json('products')->nullable();
            $table->json('tags')->nullable();
            $table->string('status')->default('active'); // active, inactive, won, lost
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_deals');
    }
};
