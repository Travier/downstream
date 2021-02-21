<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

global $user;

class MediaCollectionTest extends TestCase
{
    public function setUp(): void
    {
        global $user;

        parent::setUp();

        if (!$user) {
            $user = User::factory()->make();
        }
    }

    public function testCanFetchUserCollection()
    {
        global $user;
        $response = $this->actingAs($user)->get('/api/collection');
        $response->assertStatus(200);
    }

    public function testCanCollectItem()
    {
        global $user;
        // Collect
        $response = $this->actingAs($user)->post('/api/media/collect', [
            // Kid Cudi - Tequila Shots
            'videoId' => 'lZcRSy0sk5w'
        ]);

        $mediaId = $response->json()['mediaId'];
        $this->assertNotEmpty($mediaId, 'Got mediaId back from route');
        $response->assertStatus(200);

        return $mediaId;
    }

    /**
     * @depends testCanCollectItem
     */
    public function testCanSeeCollectedItemInCollection($mediaId)
    {
        global $user;

        $response = $this->actingAs($user)->get('/api/collection/');
        $response->assertStatus(200);

        $collection = $response->json();
        if(!$collection) {
            dd($collection);
        }
        
        $response->assertJsonFragment([
            'media_id' => $mediaId
        ]);

        return $mediaId;
    }

    /**
     * @depends testCanSeeCollectedItemInCollection
     */
    public function testCanRemoveItemFromCollection($mediaId)
    {
        global $user;

        $response = $this->actingAs($user)->delete('/api/media/collection/' . $mediaId);
        $response->assertStatus(200);
    }

    /**
     * @depends testCanRemoveItemFromCollection
     */
    public function testCollectionIsEmpty($mediaId)
    {
        global $user;

        $response = $this->actingAs($user)->get('/api/collection/');
        $response->assertStatus(200);

        $collection = $response->json();
        $this->assertTrue(count($collection['items']) == 0);
    }

    public function testCanSeeCollectedItemAtTopOfCollection()
    {
        global $user;

        $this->actingAs($user)->post('/api/media/collect', [
            // Kid Cudi - Tequila Shots
            'videoId' => 'lZcRSy0sk5w'
        ]);

        sleep(1);

        $this->actingAs($user)->post('/api/media/collect', [
            // Drake - Laugh Now Cry Later 
            'videoId' => 'JFm7YDVlqnI'
        ]);

        $response = $this->actingAs($user)->get('/api/collection/');
        $response->assertStatus(200);

        $collection = $response->json();

        $firstItemIndex = $collection['items'][0]['index'];
        $this->assertEquals('JFm7YDVlqnI', $firstItemIndex, 'last collected item is not at top of list');
    }
}
