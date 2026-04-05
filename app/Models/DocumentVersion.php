<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentVersionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'document_id',
        'created_by',
        'version',
        'change_summary',
        'file_path',
        'file_name',
        'file_size',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
