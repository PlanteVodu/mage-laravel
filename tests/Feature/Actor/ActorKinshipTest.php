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
use Tests\TestCase;

class ActorKinshipFeatureTest extends TestCase
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

    public function test_kinships_can_be_added()
    {
        $this->withoutExceptionHandling();

        $this->post('/kinships', KinshipTest::data());
        $this->post('/kinships', KinshipTest::data(['name' => 'Another name']));

        $this->post('/actors', self::data(['name' => 'Another name']));
        $this->post('/actors', self::data(['name' => 'Yet another name']));

        $actorsKeys = Actor::all()->modelKeys();
        $kinshipsKeys = Kinship::all()->modelKeys();

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => $actorsKeys[1],
                    'relative_id' => $kinshipsKeys[0],
                    'inversed' => false,
                ],
                1 => [
                    'kinship_id' => $actorsKeys[0],
                    'relative_id' => $kinshipsKeys[1],
                    'inversed' => false,
                ],
            ],
        ]);

        $response = $this->post('/actors', $data);

        $response->assertOk();
        $this->assertCount(3, Actor::all());
        $this->assertCount(2, ActorKinship::all());
        $this->assertCount(2, Actor::find(3)->kinships);
    }

    public function test_kinships_can_be_updated_and_removed()
    {
        $this->withoutExceptionHandling();

        $this->post('/kinships', KinshipTest::data());
        $this->post('/kinships', KinshipTest::data(['name' => 'Another name']));

        $this->post('/actors', self::data(['name' => 'Another name']));
        $this->post('/actors', self::data(['name' => 'Yet another name']));

        $actorsKeys = Actor::all()->modelKeys();
        $kinshipsKeys = Kinship::all()->modelKeys();

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => $kinshipsKeys[1],
                    'relative_id' => $actorsKeys[0],
                ],
                1 => [
                    'kinship_id' => $kinshipsKeys[0],
                    'relative_id' => $actorsKeys[1],
                ],
            ],
        ]);

        $response = $this->post('/actors', $data);

        // Inverse the 2nd kinship

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => $kinshipsKeys[1],
                    'relative_id' => $actorsKeys[0],
                ],
                1 => [
                    'kinship_id' => $kinshipsKeys[0],
                    'actor_id' => $actorsKeys[1],
                ],
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

        // Removing the 2nd kinship

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => $kinshipsKeys[1],
                    'relative_id' => $actorsKeys[0],
                ],
            ],
        ]);

        $response = $this->patch('/actors/3', $data);

        $response->assertOk();

        $this->assertCount(1, Actor::find(3)->kinships);
        $this->assertEquals(1, Actor::find(3)->kinships[0]->relative(3)->id);

        $this->assertCount(1, Actor::find(1)->kinships);
        $this->assertEquals(3, Actor::find(1)->kinships[0]->relative(1)->id);

        $this->assertCount(0, Actor::find(2)->kinships);

        // Removing the 1st kinship
        $response = $this->patch('/actors/3', self::data());

        $response->assertOk();
        $this->assertCount(0, Actor::find(3)->kinships);

        $this->assertCount(0, Actor::find(1)->kinships);
    }

    public function test_kinships_kinship_is_required()
    {
        $this->post('/kinships', KinshipTest::data());

        $this->post('/actors', self::data(['name' => 'Another name']));

        $actorsKeys = Actor::all()->modelKeys();
        $kinshipsKeys = Kinship::all()->modelKeys();

        $data = self::data([
            'kinships' => [ 0 => [ 'actor_id' => 1 ] ]
        ]);


        $response = $this->post('/actors', $data);

        $response->assertSessionHasErrors('kinships.0.kinship_id');
        $this->assertCount(0, ActorKinship::all());
    }

    public function test_kinships_either_actor_or_relative_is_required()
    {
        $this->post('/kinships', KinshipTest::data());

        $this->post('/actors', self::data(['name' => 'Another name']));

        $actorsKeys = Actor::all()->modelKeys();
        $kinshipsKeys = Kinship::all()->modelKeys();

        $data = self::data([
            'kinships' => [ 0 => [ 'kinship_id' => 1 ] ]
        ]);


        $response = $this->post('/actors', $data);

        $response->assertSessionHasErrors(['kinships.0.actor_id', 'kinships.0.relative_id']);
        $this->assertCount(0, ActorKinship::all());
    }
}
