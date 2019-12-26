<?php

namespace Tests\Feature;

use App\Actor;
use App\Reference;
use Tests\Feature\ReferenceTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActorReferenceTest extends TestCase
{
    use RefreshDatabase;

    public static function data($array = [])
    {
        $data = [
            'name' => 'Cool name',
            'note' => 'Some notes',
        ];

        return array_merge($data, $array);
    }

    public function test_references_can_be_added()
    {
        $this->post('/references', ReferenceTest::data());
        $this->post('/references', ReferenceTest::data(['name' => 'Another name']));

        $data = self::data(['references' => Reference::all()->modelKeys()]);
        $response = $this->post('/actors', $data);
        $response->assertOk();
        $this->assertCount(1, Actor::all());
        $this->assertCount(2, Actor::first()->references);
        $this->assertEquals(Reference::first()->id, Actor::first()->references[0]->id);

        $this->post('/actors', self::data(['references' => Reference::first()->getKey()]));
        $response->assertOk();
        $this->assertCount(2, Actor::all());
        $this->assertCount(2, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);
        $this->assertEquals(1, Actor::first()->references[0]->id);
        $this->assertEquals(2, Actor::first()->references[1]->id);
        $this->assertEquals(1, Actor::find(2)->references[0]->id);
    }

    public function test_references_can_be_removed()
    {
        $this->post('/references', ReferenceTest::data());
        $this->post('/references', ReferenceTest::data(['name' => 'Another name']));

        $this->post('/actors', self::data(['references' => Reference::all()->modelKeys()]));
        $this->post('/actors', self::data(['references' => 2]));

        $actor = Actor::first();

        $response = $this->patch('/actors/1', self::data(['references' => [Reference::first()->getKey()]]));
        $response->assertOk();
        $this->assertCount(1, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);

        $response = $this->patch('/actors/1', self::data(['references' => []]));
        $response->assertOk();
        $this->assertCount(0, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);
    }

    public function test_references_must_be_distinct()
    {
        $this->post('/references', ReferenceTest::data());

        $data = self::data(['references' => [1, 1]]);
        $response = $this->post('/actors', $data);
        $response->assertSessionHasErrors('references.0', 'references.1');
        $this->assertCount(0, Actor::all());
    }
}
