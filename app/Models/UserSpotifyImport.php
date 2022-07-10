<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSpotifyImport extends Model
{
    use HasFactory;

    protected $table = 'user_spotify_imports';
    protected $fillable = [
        'user_id',
        'media_id',
        'track_name',
        'track_artist',
        'search_query',
        'imported',
        'imported_at'
    ];
}
