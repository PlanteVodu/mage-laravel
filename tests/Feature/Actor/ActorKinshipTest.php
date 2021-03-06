<?php

namespace Tests\Feature;

use App\Actor;
use App\Reference;
use App\Kinship;
use App\ActorKinship;
use Tests\Feature\ActorTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActorKinshipFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        factory(Kinship::class, 2)->create();
        factory(Reference::class, 2)->create();
        factory(Actor::class, 2)->create();

        $this->actorsKeys = Actor::all()->modelKeys();
        $this->kinshipsKeys = Kinship::all()->modelKeys();
    }

    protected function getActor(...$kinships)
    {
        return factory(Actor::class)
            ->make(['kinships' => $this->getActorKinshipsArray(...$kinships)])
            ->toArray();
    }

    protected function getActorKinshipsArray(...$kinships)
    {
        $actorKinships = [];
        foreach ($kinships as $kinship) {
            $actorKinships[]= $this->getKinshipArray(...$kinship);
        }
        return $actorKinships;
    }

    protected function getKinshipArray($kinshipId, $relativeId, $primary = false, $references = [])
    {
        return [
            'kinship_id' => $this->kinshipsKeys[$kinshipId],
            'relative_id' => $this->kinshipsKeys[$relativeId],
            'references' => $references,
            'primary' => $primary,
        ];
    }

    public function test_kinships_can_be_added()
    {
        $actor = $this->getActor([0, 1], [1, 0]);
        $response = $this->post('/actors', $actor);

        $response->assertOk();
        $this->assertCount(2, Actor::find(3)->kinships);
        $this->assertEquals(2, Actor::find(3)->kinships[0]->relative(3)->id);
        $this->assertEquals(1, Actor::find(3)->kinships[1]->relative(3)->id);
        $this->assertEquals(1, Actor::find(3)->kinships[0]->kinship()->id);
        $this->assertEquals(2, Actor::find(3)->kinships[1]->kinship()->id);
    }

    public function test_kinships_direction_can_be_modified()
    {
        $actor = $this->getActor([0, 1, true], [1, 0, true]);
        $response = $this->post('/actors', $actor);

        $response->assertOk();
        $this->assertCount(2, Actor::find(3)->kinships);
        $this->assertEquals(2, Actor::find(3)->kinships[0]->relative(3)->id);
        $this->assertEquals(1, Actor::find(3)->kinships[1]->relative(3)->id);
        $this->assertEquals(1, Actor::find(3)->kinships[0]->kinship()->id);
        $this->assertEquals(2, Actor::find(3)->kinships[1]->kinship()->id);

        $this->assertEquals(3, Actor::find(3)->kinships[0]->relative_id);
        $this->assertEquals(2, Actor::find(3)->kinships[0]->actor_id);
        $this->assertEquals(3, Actor::find(3)->kinships[1]->relative_id);
        $this->assertEquals(1, Actor::find(3)->kinships[1]->actor_id);
    }

    public function test_kinships_can_be_updated()
    {
        $actor = $this->getActor([0, 1], [1, 0]);
        $this->post('/actors', $actor);

        $actor = $this->getActor([1, 1], [0, 0]);
        $response = $this->patch('/actors/3', $actor);

        $response->assertOk();
        $kinships = Actor::find(3)->kinships()->get()->sortBy('kinship_id');
        $this->assertEquals(2, $kinships->count());
        $this->assertEquals(1, $kinships->values()->get(0)->kinship()->id);
        $this->assertEquals(1, $kinships->values()->get(0)->relative(3)->id);
        $this->assertEquals(2, $kinships->values()->get(1)->kinship()->id);
        $this->assertEquals(2, $kinships->values()->get(1)->relative(3)->id);
    }

    public function test_kinships_direction_can_be_updated()
    {
        $actor = $this->getActor([0, 1], [1, 0]);
        $this->post('/actors', $actor);

        $actor = $this->getActor([0, 1, true], [1, 0, true]);
        $response = $this->patch('/actors/3', $actor);

        $response->assertOk();
        $this->assertCount(2, Actor::find(3)->kinships);
        // dd(ActorKinship::all());
        $this->assertCount(2, ActorKinship::all());
        $this->assertEquals(2, Actor::find(3)->kinships[0]->relative(3)->id);
        $this->assertEquals(1, Actor::find(3)->kinships[1]->relative(3)->id);

        $this->assertEquals(3, Actor::find(3)->kinships[0]->relative_id);
        $this->assertEquals(2, Actor::find(3)->kinships[0]->actor_id);
        $this->assertEquals(3, Actor::find(3)->kinships[1]->relative_id);
        $this->assertEquals(1, Actor::find(3)->kinships[1]->actor_id);
    }

    public function test_kinships_can_be_removed()
    {
        $actor = $this->getActor([0, 1], [1, 0]);
        $this->post('/actors', $actor);

        $actor = $this->getActor([1, 0]);
        $response = $this->patch('/actors/3', $actor);

        $response->assertOk();
        $kinships = Actor::find(3)->kinships()->get()->sortBy('kinship_id');
        $this->assertEquals(1, Actor::find(3)->kinships->count());
        $this->assertEquals(2, Actor::find(3)->kinships[0]->kinship()->id);
        $this->assertEquals(1, Actor::find(3)->kinships[0]->relative(3)->id);

        $this->assertCount(1, Actor::find(1)->kinships);
        $this->assertEquals(2, Actor::find(1)->kinships[0]->kinship()->id);

        $this->assertCount(0, Actor::find(2)->kinships);

        $actor = $this->getActor();
        $response = $this->patch('/actors/3', $actor);

        $response->assertOk();
        $this->assertCount(0, Actor::find(3)->kinships);
        $this->assertCount(0, Actor::find(2)->kinships);
    }

    public function test_kinships_kinship_is_required()
    {
        $data = ActorTest::data([
            'kinships' => [ 0 => [ 'relative_id' => 1 ] ]
        ]);
        $response = $this->post('/actors', $data);
        $response->assertSessionHasErrors('kinships.0.kinship_id');
        $this->assertCount(0, ActorKinship::all());
    }

    public function test_kinships_relative_is_required()
    {
        $data = ActorTest::data([
            'kinships' => [ 0 => [ 'kinship_id' => 1 ] ]
        ]);
        $response = $this->post('/actors', $data);
        $response->assertSessionHasErrors('kinships.0.relative_id');
        $this->assertCount(0, ActorKinship::all());
    }

    public function test_actor_kinship_references_can_be_added()
    {
        $actor = $this->getActor([1 , 0, true, [1, 2]], [0, 1, true, [2]]);
        $response = $this->post('/actors', $actor);
        $response->assertOk();
        $this->assertCount(2, Actor::find(3)->kinships);
        $this->assertCount(2, Actor::find(3)->kinships[0]->references);
        $this->assertCount(1, Actor::find(3)->kinships[1]->references);
        $this->assertEquals(1, Actor::find(3)->kinships[0]->references[0]->id);
        $this->assertEquals(2, Actor::find(3)->kinships[0]->references[1]->id);
        $this->assertEquals(2, Actor::find(3)->kinships[1]->references[0]->id);
    }

    public function test_actor_kinship_references_can_be_removed()
    {
        $actor = $this->getActor([1, 0, true, [1, 2]]);
        $response = $this->post('/actors', $actor);

        $actor = $this->getActor([1, 0]);
        $response = $this->patch('/actors/3', $actor);
        $response->assertOk();
        $this->assertCount(0, Actor::find(3)->kinships[0]->references);
    }

    public function test_references_are_removed_when_actor_kinship_is_removed()
    {
        $actor = $this->getActor([1, 0, true, [1, 2]], [0, 1, true, [2]]);
        $this->post('/actors', $actor);

        $actor = $this->getActor([0, 1, true, [2]]);
        $response = $this->patch('/actors/3', $actor);

        $response->assertOk();
        $this->assertCount(1, Actor::find(3)->kinships);
        $this->assertCount(1, Actor::find(3)->kinships[0]->references);
        $this->assertCount(1, DB::table('referencables')->get());
    }

    public function test_actor_kinships_are_deleted_when_actor_is_deleted()
    {
        $kinships = $this->getActorKinshipsArray(
            [1, 0, true, [1, 2]],
            [0, 1, true, [2]]
        );

        $actor = factory(Actor::class)->make([
            'references' => [1, 2],
            'kinships' => $kinships,
        ])->toArray();

        $this->post('/actors', $actor);

        $this->assertCount(3, Actor::all());
        $this->assertCount(2, ActorKinship::all());
        $this->assertCount(5, DB::table('referencables')->get());

        $response = $this->delete('/actors/3');

        $this->assertCount(2, Actor::all());
        $this->assertCount(0, ActorKinship::all());
        $this->assertCount(0, DB::table('referencables')->get());
    }
}
