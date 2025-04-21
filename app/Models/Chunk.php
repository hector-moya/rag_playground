<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pgvector\Laravel\HasNeighbors;
use Pgvector\Laravel\Vector;

class Chunk extends Model
{
    use HasFactory, HasNeighbors;

    protected $casts = [
        'embedding' => Vector::class,
    ];

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
