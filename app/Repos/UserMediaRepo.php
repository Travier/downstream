<?php

namespace App\Repos;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Repos\Types\Media\MediaItem;

class UserMediaRepo
{
    /**
     * @param integer $userId
     * @param integer $start
     * @param integer $limit
     * @return Collection<MediaItem>
     */
    public function getCollectionSlice(int $userId, int $start = 0, int $limit = 4): Collection
    {
        $results = DB::table('media')
            ->join('user_media', 'user_media.media_id', '=', 'media.id')
            ->where('user_media.user_id', $userId)
            ->whereNull('user_media.deleted_at')
            ->skip($start)
            ->take($limit)
            ->orderBy('user_media.created_at', 'DESC')
            ->get();

        return $results;
    }
}
