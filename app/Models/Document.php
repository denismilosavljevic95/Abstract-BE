<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    /**
     * READ SECTION
     */
    public function getOne($documentID) {
        $userID = Auth::user()['id'];
        return Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->firstOrFail();
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * DELETE SECTION
     */
    public function archive($documentID) {
        $userID = Auth::user()['id'];
        return Document::where('id', '=', $documentID)->where('user_id', '=', $userID)->update(['archive' => 1]);
    }
}
