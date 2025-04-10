<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'mime_type',
    ];

    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }
}
