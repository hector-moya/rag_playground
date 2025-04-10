<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chunk extends Model
{
    protected $fillable = [
        'document_id',
        'content',
        'embedding',
        'chunk_index',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}
