<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'unique_id',
        'name',
        'file_path',
        'file_type',
        'file_size',
        'description',
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
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'retention_expiry_date' => 'date',
        'file_size' => 'integer',
    ];

    /**
     * The attributes to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'formatted_size',
        'keywords_array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Document $document) {
            // Generate unique ID if not provided
            if (empty($document->unique_id)) {
                $document->unique_id = 'DOC-' . strtoupper(Str::random(8));
            }
            // Generate integrity hash
            if (empty($document->integrity_hash)) {
                $document->integrity_hash = hash_file('sha256', storage_path('app/private/' . $document->file_path));
            }
        });
    }

    /**
     * Get the user that owns the document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent document (for version linking).
     */
    public function parentDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'parent_document_id');
    }

    /**
     * Get child documents (versions).
     */
    public function childDocuments()
    {
        return $this->hasMany(Document::class, 'parent_document_id');
    }

    /**
     * Get the file size in human readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get keywords as array.
     */
    public function getKeywordsArrayAttribute(): array
    {
        if (empty($this->keywords)) {
            return [];
        }
        return array_map('trim', explode(',', $this->keywords));
    }

    /**
     * Check if document is latest version.
     */
    public function isLatestVersion(): bool
    {
        return !$this->childDocuments()->exists();
    }

    /**
     * Search documents by various criteria.
     */
    public static function search($query, $searchTerm)
    {
        return self::where(function ($q) use ($searchTerm) {
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('unique_id', 'like', "%{$searchTerm}%")
              ->orWhere('subject', 'like', "%{$searchTerm}%")
              ->orWhere('agency', 'like', "%{$searchTerm}%")
              ->orWhere('keywords', 'like', "%{$searchTerm}%")
              ->orWhere('document_reference', 'like', "%{$searchTerm}%")
              ->orWhere('author', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Filter by file type.
     */
    public static function filterByType($query, $fileType)
    {
        return $query->where('file_type', 'like', "%{$fileType}%");
    }

    /**
     * Filter by classification.
     */
    public static function filterByClassification($query, $classification)
    {
        return $query->where('classification', $classification);
    }

    /**
     * Filter by agency.
     */
    public static function filterByAgency($query, $agency)
    {
        return $query->where('agency', 'like', "%{$agency}%");
    }
}