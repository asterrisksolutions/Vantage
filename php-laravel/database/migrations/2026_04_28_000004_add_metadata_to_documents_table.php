<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds unique document identification, version control, and metadata fields.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Unique Document Identification
            $table->string('unique_id')->unique()->nullable()->after('id');
            $table->string('version')->default('1.0')->after('file_size');
            
            // Basic Metadata
            $table->string('document_origin')->nullable()->after('version');
            $table->string('subject')->nullable()->after('document_origin');
            $table->string('agency')->nullable()->after('subject');
            $table->string('classification')->default('Unclassified')->after('agency');
            
            // Descriptive Metadata
            $table->string('author')->nullable()->after('classification');
            $table->string('keywords')->nullable()->after('author');
            $table->string('document_reference')->nullable()->after('keywords');
            
            // Technical Metadata
            $table->string('software_used')->nullable()->after('document_reference');
            $table->string('integrity_hash')->nullable()->after('software_used');
            
            // Structural Metadata
            $table->unsignedBigInteger('parent_document_id')->nullable()->after('integrity_hash');
            $table->foreign('parent_document_id')->references('id')->on('documents')->onDelete('set null');
            $table->string('workflow_status')->default('draft')->after('parent_document_id');
            
            // Administrative/Contextual Metadata
            $table->date('retention_expiry_date')->nullable()->after('workflow_status');
            $table->string('copyright')->nullable()->after('retention_expiry_date');
            $table->string('gps_location')->nullable()->after('copyright');
            
            // Index for search performance
            $table->index('unique_id');
            $table->index('subject');
            $table->index('agency');
            $table->index('classification');
            $table->index('keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['parent_document_id']);
            $table->dropColumn([
                'unique_id',
                'version',
                'document_origin',
                'subject',
                'agency',
                'classification',
                'author',
                'keywords',
                'document_reference',
                'software_used',
                'integrity_hash',
                'parent_document_id',
                'workflow_status',
                'retention_expiry_date',
                'copyright',
                'gps_location',
            ]);
        });
    }
};