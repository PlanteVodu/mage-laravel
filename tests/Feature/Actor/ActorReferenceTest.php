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

    protected function setUp() : void
    {
        parent::setUp();
        factory(Reference::class, 5)->create();
    }

    public function test_references_can_be_added()
    {
        $actor = factory(Actor::class)->make(['references' => Reference::all()->modelKeys()]);
        $response = $this->post('/actors', $actor->toArray());
        $response->assertOk();
        $this->assertCount(1, Actor::all());
        $this->assertCount(5, Actor::first()->references);
        $this->assertEquals(Reference::first()->id, Actor::first()->references[0]->id);

        $actor = factory(Actor::class)->make(['references' => Reference::first()->getKey()]);
        $this->post('/actors', $actor->toArray());
        $response->assertOk();
        $this->assertCount(2, Actor::all());
        $this->assertCount(5, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);
        $this->assertEquals(1, Actor::find(2)->references[0]->id);
    }

    public function test_references_can_be_removed()
    {
        $actor = factory(Actor::class)->make(['references' => Reference::all()->modelKeys()]);
        $this->post('/actors', $actor->toArray());
        $actor = factory(Actor::class)->make(['references' => 2]);
        $this->post('/actors', $actor->toArray());

        $actor = factory(Actor::class)->make(['references' => 1]);
        $response = $this->patch('/actors/1', $actor->toArray());
        $response->assertOk();
        $this->assertCount(1, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);

        $actor = factory(Actor::class)->make(['references' => '']);
        $response = $this->patch('/actors/1', $actor->toArray());
        $response->assertOk();
        $this->assertCount(0, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);
    }

    public function test_references_must_be_distinct()
    {
        $actor = factory(Actor::class)->make(['references' => [1,1]]);
        $response = $this->post('/actors', $actor->toArray());
        $response->assertSessionHasErrors('references.0', 'references.1');
        $this->assertCount(0, Actor::all());
    }
}
