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
        Schema::table('claims', function (Blueprint $table) {
            // Owner information fields
            $table->string('full_name')->nullable()->after('bar_id');
            $table->string('phone_number')->nullable()->after('full_name');
            $table->string('email_address')->nullable()->after('phone_number');
            $table->enum('role', ['Owner', 'Manager', 'Marketing Lead'])->nullable()->after('email_address');
            
            // Relationship proof and documents
            $table->string('relationship_proof')->nullable()->after('role'); // file upload path
            $table->text('comments')->nullable()->after('relationship_proof');
            
            // System generated fields
            $table->string('claim_request_id')->unique()->nullable()->after('id');
            
            // Admin verification fields
            $table->text('admin_notes')->nullable()->after('status');
            $table->json('admin_documents')->nullable()->after('admin_notes'); // for storing multiple document paths
            $table->enum('verification_status', ['pending', 'needs_info', 'approved', 'rejected'])->default('pending')->after('admin_notes');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('claims', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'phone_number',
                'email_address',
                'role',
                'relationship_proof',
                'comments',
                'claim_request_id',
                'admin_notes',
                'admin_documents',
                'verification_status',
                'verified_at',
                'verified_by'
            ]);
        });
    }
};
