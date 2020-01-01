<?php

namespace Tests\Feature;

use App\Actor;
use App\Reference;
use App\Kinship;
use App\ActorKinship;
use Tests\Feature\ReferenceTest;
use Tests\Feature\KinshipTest;
use Tests\Feature\Actor\ActorTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ActorKinshipFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        factory(Kinship::class, 3)->create();
        factory(Reference::class, 3)->create();
        factory(Actor::class, 5)->create();

        $this->actorsKeys = Actor::all()->modelKeys();
        $this->kinshipsKeys = Kinship::all()->modelKeys();
    }

    protected function getKinshipArray($kinshipId, $relativeId, $references = [])
    {
        // dd($references);
        return [
            'kinship_id' => $this->kinshipsKeys[$kinshipId],
            'relative_id' => $this->kinshipsKeys[$relativeId],
            'references' => $references,
        ];
    }

    protected function getActor(...$kinships)
    {
        $actorKinships = [];
        foreach ($kinships as $kinship) {
            $actorKinships[]= $this->getKinshipArray(...$kinship);
        }

        return factory(Actor::class)
            ->make(['kinships' => $actorKinships])
            ->toArray();
    }

    public function test_kinships_can_be_added()
    {
        $actor = $this->getActor([0,1,[1,2]], [1,0,[3]]);
        $response = $this->post('/actors', $actor);

        $response->assertCreated();
        $this->assertCount(2, Actor::find(6)->kinships);
    }

    public function test_kinships_can_be_updated()
    {
        $this->post('/kinships', KinshipTest::data());
        $this->post('/kinships', KinshipTest::data(['name' => 'Another name']));

        $this->post('/actors', self::data(['name' => 'Another name']));
        $this->post('/actors', self::data(['name' => 'Yet another name']));

        $actorsKeys = Actor::all()->modelKeys();
        $kinshipsKeys = Kinship::all()->modelKeys();

        $data = self::data([
            'kinships' => [
                getKinshipArrat($kinshipsKeys[0], $actorsKeys[1]),
                getKinshipArrat($kinshipsKeys[1], $actorsKeys[0]),
            ],
        ]);
        $this->post('/actors', $data);

        // Reset kinships
        $data = self::data([
            'kinships' => [
                getKinshipArrat($kinshipsKeys[0], $actorsKeys[0]),
                getKinshipArrat($kinshipsKeys[1], $actorsKeys[1]),
            ],
        ]);
        $response = $this->patch('/actors/3', $data);
        $response->assertOk();
        $this->assertCount(2, Actor::find(3)->kinships);
        $this->assertEquals(1, Actor::find(3)->kinships[0]->relative(3)->id);
        $this->assertCount(1, Actor::find(1)->kinships);
        $this->assertEquals(3, Actor::find(1)->kinships[0]->relative(1)->id);
        $this->assertCount(1, Actor::find(2)->kinships);
        $this->assertEquals(3, Actor::find(2)->kinships[0]->relative(2)->id);
    }

    public function test_kinships_can_be_removed()
    {
        $this->post('/kinships', KinshipTest::data());
        $this->post('/kinships', KinshipTest::data(['name' => 'Another name']));

        $this->post('/actors', self::data(['name' => 'Another name']));
        $this->post('/actors', self::data(['name' => 'Yet another name']));

        $actorsKeys = Actor::all()->modelKeys();
        $kinshipsKeys = Kinship::all()->modelKeys();

        $data = self::data([
            'kinships' => [
                getKinshipArrat($kinshipsKeys[0], $actorsKeys[1]),
                getKinshipArrat($kinshipsKeys[1], $actorsKeys[0]),
            ],
        ]);
        $this->post('/actors', $data);

        // Removing the 2nd kinship
        $data = self::data([
            'kinships' => [
                getKinshipArrat($kinshipsKeys[0], $actorsKeys[1]),
            ],
        ]);
        $response = $this->patch('/actors/3', $data);
        $response->assertOk();
        $this->assertCount(1, Actor::find(3)->kinships);
        $this->assertEquals(1, Actor::find(3)->kinships[0]->relative(3)->id);
        $this->assertCount(1, Actor::find(1)->kinships);
        $this->assertEquals(3, Actor::find(1)->kinships[0]->relative(1)->id);
        $this->assertCount(0, Actor::find(2)->kinships);
        $this->assertCount(1, ActorKinship::all());

        // Removing the 1st kinship
        $response = $this->patch('/actors/3', self::data());
        $response->assertOk();
        $this->assertCount(0, Actor::find(3)->kinships);
        $this->assertCount(0, Actor::find(1)->kinships);
        $this->assertCount(0, ActorKinship::all());
    }

    public function test_kinships_kinship_is_required()
    {
        $this->post('/kinships', KinshipTest::data());

        $this->post('/actors', self::data(['name' => 'Another name']));

        $data = self::data([
            'kinships' => [ 0 => [ 'relative_id' => 1 ] ]
        ]);
        $response = $this->post('/actors', $data);
        $response->assertSessionHasErrors('kinships.0.kinship_id');
        $this->assertCount(0, ActorKinship::all());
    }

    public function test_kinships_relative_is_required()
    {
        $this->post('/kinships', KinshipTest::data());

        $this->post('/actors', self::data(['name' => 'Another name']));

        $data = self::data([
            'kinships' => [ 0 => [ 'kinship_id' => 1 ] ]
        ]);
        $response = $this->post('/actors', $data);
        $response->assertSessionHasErrors('kinships.0.relative_id');
        $this->assertCount(0, ActorKinship::all());
    }

    public function test_actor_kinship_references_can_be_added()
    {
        $this->post('/kinships', KinshipTest::data());
        $this->post('/actors', self::data(['name' => 'Another name']));

        $this->post('/references', ReferenceTest::data());
        $this->post('/references', ReferenceTest::data(['name' => 'Another name']));

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => 1,
                    'relative_id' => 1,
                    'references' => Reference::all()->modelKeys(),
                ],
            ],
        ]);
        $response = $this->post('/actors', $data);
        $response->assertOk();
        $this->assertCount(1, Actor::find(2)->kinships);
        $this->assertCount(2, Actor::find(2)->kinships[0]->references);
        $this->assertEquals(1, Actor::find(2)->kinships[0]->references[0]->id);
        $this->assertEquals(2, Actor::find(2)->kinships[0]->references[1]->id);
    }

    public function test_actor_kinship_references_can_be_removed()
    {
        $this->withoutExceptionHandling();
        $this->post('/kinships', KinshipTest::data());
        $this->post('/actors', self::data(['name' => 'Another name']));

        $this->post('/references', ReferenceTest::data());
        $this->post('/references', ReferenceTest::data(['name' => 'Another name']));

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => 1,
                    'relative_id' => 1,
                    'references' => Reference::all()->modelKeys(),
                ],
            ],
        ]);
        $this->post('/actors', $data);

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => 1,
                    'relative_id' => 1,
                ],
            ],
        ]);
        $response = $this->patch('/actors/2', $data);
        $response->assertOk();
        $this->assertCount(1, Actor::find(2)->kinships);
        $this->assertCount(0, Actor::find(2)->kinships[0]->references);
    }

    public function test_references_are_removed_when_actor_kinship_is_removed()
    {
        $this->withoutExceptionHandling();
        $this->post('/kinships', KinshipTest::data());
        $this->post('/kinships', KinshipTest::data());

        $this->post('/actors', self::data());
        $this->post('/actors', self::data());

        $this->post('/references', ReferenceTest::data());
        $this->post('/references', ReferenceTest::data());

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => 1,
                    'relative_id' => 2,
                    'references' => Reference::all()->modelKeys(),
                ],
                1 => [
                    'kinship_id' => 2,
                    'relative_id' => 1,
                    'references' => 2,
                ],
            ],
        ]);

        $response = $this->post('/actors', $data);
        $response->assertOk();
        $this->assertCount(2, Actor::find(3)->kinships);
        $this->assertCount(2, Actor::find(3)->kinships[0]->references);
        $this->assertCount(1, Actor::find(3)->kinships[1]->references);

        $response = $this->patch('/actors/3', self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => 2,
                    'relative_id' => 1,
                    'references' => 2,
                ]
            ],
        ]));
        $this->assertCount(1, Actor::find(3)->kinships);
        $this->assertCount(1, Actor::find(3)->kinships[0]->references);
        $this->assertCount(1, DB::table('referencables')->get());
    }
}
