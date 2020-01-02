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

    public function getActor(...$references)
    {
        return factory(Actor::class)
            ->make(['references' => $references])
            ->toArray();
    }

    public function test_references_can_be_added()
    {
        $actor = $this->getActor(...Reference::all()->modelKeys());
        $response = $this->post('/actors', $actor);
        $response->assertOk();
        $this->assertCount(1, Actor::all());
        $this->assertCount(5, Actor::first()->references);
        $this->assertEquals(Reference::first()->id, Actor::first()->references[0]->id);

        $actor = $this->getActor(Reference::first()->id);
        $this->post('/actors', $actor);
        $response->assertOk();
        $this->assertCount(2, Actor::all());
        $this->assertCount(5, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);
        $this->assertEquals(1, Actor::find(2)->references[0]->id);
    }

    public function test_references_can_be_removed()
    {
        $actor = $this->getActor(...Reference::all()->modelKeys());
        $this->post('/actors', $actor);
        $actor = $this->getActor(2);
        $this->post('/actors', $actor);

        $actor = $this->getActor(1);
        $response = $this->patch('/actors/1', $actor);
        $response->assertOk();
        $this->assertCount(1, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);

        $actor = $this->getActor();
        $response = $this->patch('/actors/1', $actor);
        $response->assertOk();
        $this->assertCount(0, Actor::find(1)->references);
        $this->assertCount(1, Actor::find(2)->references);
    }

    public function test_references_must_be_distinct()
    {
        $actor = $this->getActor(1, 1);
        $response = $this->post('/actors', $actor);
        $response->assertSessionHasErrors('references.0', 'references.1');
        $this->assertCount(0, Actor::all());
    }
}
