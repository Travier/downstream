<?php

namespace App\Http\Controllers;

use Auth;
use Route;
use DB;
use App\Models\User;
use App\Models\Media;
use App\Models\UserMedia;
use App\Models\Media\YouTube;
use App\Models\MediaRemoteReference;
use App\Models\Media\YouTubeV2;
use App\Models\MediaResolver;
use Illuminate\Http\Request;

class MediaAPIController extends Controller
{
    public $types = [
      "youtube" => [
        //sub type
        'video',
        'live',
        'playlist'
      ],
      'soundcloud' => [
        'track'
      ]
    ];
    private $userId = false;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware(function ($request, $next) {
            $this->userId = Auth::user()->id;
            $this->resolver = new MediaResolver($this->userId);
            $this->YouTube = new YouTube($this->userId);

            return $next($request);
        });
    }

    public function testVideo()
    {
        $this->YouTube->testVideoInfo();
    }

    public function getUserDiscoverables(Request $request)
    {
        $userId = Auth::user()->id;

        $collectionIds = UserMedia::where('user_id', $userId)
          ->orderBy('created_at', 'DESC')
          ->take(20)
          ->pluck('media_id');


        $discoveredResults = [];
        foreach($collectionIds as $recentCollectedItemId) {
            $discoveredItems = MediaRemoteReference::where('media_id', $recentCollectedItemId)->get();

            if(count($discoveredItems) <= 0) {
                continue;
            }

            foreach($discoveredItems as &$discoveredItem) {
                $discoveredItem->collected = DB::table('user_media')
                  ->join('media', 'user_media.media_id', '=', 'media.id')
                  ->where('media.index', $discoveredItem->index)
                  ->exists();
            }

            $discoveredResults[] = [
              'media_id' => $recentCollectedItemId,
              'media_item' => Media::find($recentCollectedItemId),
              'items' => $discoveredItems
            ];
        }

        return response()->json([
          'items' => $discoveredResults
        ], 200);
    }

    public function profile(Request $request, $hash)
    {

        $user = User::where('hash', $hash)->first();

        if(!$user) {
            return response()->json([
               'code'      =>  401,
               'message'   =>  "Unknown user hash: $userHash"
             ], 401);
        }

        $user->media_count = UserMedia::where('user_id', $user->id)->count();
        $joined = date_create($user->created_at);
        $today = date_create(Date("Y-m-d H:i:s"));

        //difference between two dates
        $diff = date_diff($joined, $today);
        $user->days_since_joined = $diff->format("%a");


        return response()->json([
          'code'      =>  200,
          'collection' => $this->resolver->collection($user->id, true),
          'user' => $user
        ], 200);
    }

    public function collection(Request $request, $isRandom = false)
    {

        $items = [];
        $items = $this->YouTube->collection(false, $isRandom);

        return response()->json([
          'items' => $items
        ], 200);
    }

    public function discover(Request $request)
    {
        $userId = Auth::user()->id;
        $videoId = $request->input('videoId');
        $spotifyId = $request->input('spotifyId', false);

        $meta = [];
        if($spotifyId) {
            $meta = [
              'spotify_id' => $spotifyId
            ];
        }

        YouTubeV2::discover($userId, $videoId, $meta);
    }

    public function resolve(Request $request)
    {
        $uri = Route::current()->uri;
        $action = $this->getURIAction($uri);

        $type = $request->type;

        if(!$type or !$action) {
            return response()->json([
              'code'      =>  401,
              'message'   =>  "Invalid query parameters action or type"
            ], 401);
        }

        $this->resolver->dispatch($type, $action, $request->input());
        return $this->resolver->getJSONResponse();
    }

    private function getURIAction($uri)
    {
        $routeVars = explode("/", $uri);

        if(count($routeVars) <= 2) {
            return false;
        }

        return $routeVars[2];
    }
}
