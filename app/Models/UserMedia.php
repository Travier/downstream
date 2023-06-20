<?php

namespace App\Models;

use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserMedia extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'user_media';

    protected $fillable = ['media_id', 'user_id', 'pushed_at'];

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public static function findById($mediaId, $userId)
    {
        return self::where('media_id', $mediaId)
          ->where('user_id', $userId);
    }

    public static function pluckMediaIds($userId = false)
    {
        if (! $userId) {
            $userId = Auth::user()->id;
        }

        return self::where('user_id', $userId)
          ->pluck('media_id');
    }

    public static function collection($type = false)
    {
        $mediaIds = self::pluckMediaIds();

        $query = new Media();
        if ($type) {
            $query = Media::where('type', $type);
        }

        return $query->find($mediaIds);
    }

    public static function didCollect($mediaId)
    {
        if (@! Auth::user()->id) {
            return false;
        }

        return self::where('media_id', $mediaId)
          ->where('user_id', Auth::user()->id)
          ->exists();
    }
}
