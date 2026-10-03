<?php

namespace App\Models;

use App\Domain\Clinical\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClinicalDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'clinical_record_id',
        'encounter_id',
        'document_type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'description',
        'is_sensitive',
        'uploaded_by',
    ];

    protected $casts = [
        'document_type' => DocumentType::class,
        'is_sensitive' => 'boolean',
        'file_size' => 'integer',
    ];

    public function clinicalRecord(): BelongsTo
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    public function encounter(): BelongsTo
    {
        return $this->belongsTo(ClinicalEncounter::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }
}
