<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fileName',
        'filePath',
        'zipPath',
        'user_id',
        'archive'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
