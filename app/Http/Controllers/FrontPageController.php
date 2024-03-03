<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Media;
use App\Models\GlobalQueue;
use App\Models\MediaTempItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FrontPageController extends Controller
{
    private $rowRenderIndex = [];

    public function getMediaItem($mediaIndex)
    {
        $media = Media::findByType('youtube', $mediaIndex)->first();

        if($media) {
            $media = $this->prepareMediaItem($media);
        }

        return view('link.view-media', [
          'media' => $media
        ]);
    }

    public function getLanding()
    {
        $items = GlobalQueue::getActiveMedia();
        $items = $items->take(6);

        if(Auth::check()) {
            Media::addUserCollectedProp($items);
        }

        return view('landing.main', [
          'items' => $items,
          'videos' => []
        ]);
    }

    public function getGlobalPage()
    {
        $activeMedia = GlobalQueue::getAllMedia();

        foreach($activeMedia as $media) {
            $media = $media->prepareItemMeta($media);
        }

        if(Cache::get('showLatestVideos', 'yes') == 'no') {
            $activeMedia = [];
        }

        return view("frontpage.global", [
          'activeItems' => $activeMedia
        ]);
    }

    public function index()
    {
        $toplist = MediaTempItem::where('visible', 1)->get();

        $latestVideos = Media::byType('youtube')
          ->limit(16)
          ->orderBy('id', 'DESC')
          ->get();

        if(Cache::get('showLatestVideos', 'yes') == 'yes') {
            $this->createCustomRow("Most Recent User Collected Items:", $latestVideos);
        }

        //hack for now till I can merge temp and media items better
        if(count($toplist) >= 2) {
            $rows = [];
        }

        return view('frontpage.index', [
          'toplist' => [],
          'rows' => $this->rowRenderIndex
        ]);
    }

    private function prepareMediaItem($item)
    {
        if(Auth::check()) {
            $item = [$item];
            $item = Media::addUserCollectedProp($item)[0];
        }

        $item->meta = json_decode($item->meta);

        return $item;
    }

    private function prepareMediaItems($items = [])
    {
        if(Auth::check()) {
            $items = Media::addUserCollectedProp($items);
        }

        foreach($items as &$item) {
            $item->meta = json_decode($item->meta);

            /*$user = User::find($item->user_id);
            $user->smallHash = $user->shrinkHash();
            $user->profileLink = "/user/" . $user->hash . "/profile";

            $item->user = $user;*/
        }

        return $items;
    }

    private function createCustomRow($title = false, $media = false)
    {
        if(!$media) {
            return false;
        }

        $media = $this->prepareMediaItems($media);

        $row = [
          'title' => $title,
          'media' => $media
        ];

        $this->rowRenderIndex[] = $row;
    }

    private function createRow($title, $mediaIds)
    {
        //basic struct
        $row = [
          'title' => $title,
          'media' => []
        ];

        $orderedIds = implode(',', $mediaIds);

        //get media items by id
        $row['media'] = Media::whereIn('id', array_values($mediaIds))
          ->orderByRaw(DB::raw("FIELD(id, $orderedIds)"))
          ->get();

        if(count($row['media']) <= 0) {
            //no media items to display
            return false;
        }

        $row['media'] = $this->prepareMediaItems($row['media']);

        //push to render index
        $this->rowRenderIndex[] = $row;
    }
}
