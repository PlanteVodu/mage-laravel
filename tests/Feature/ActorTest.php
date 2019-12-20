<?php

namespace Tests\Feature;

use App\Actor;
use App\Reference;
use App\Kinship;
use App\ActorKinship;
use Tests\Feature\ReferenceTest;
use Tests\Feature\KinshipTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActorTest extends TestCase
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

    public function test_an_actor_can_be_added()
    {
        $response = $this->post('/actors', self::data());

        $response->assertOk();
        $this->assertCount(1, Actor::all());
    }

    public function test_a_name_is_required()
    {
        $response = $this->post('/actors', self::data(['name' => '']));

        $response->assertSessionHasErrors('name');
    }

    public function test_note_is_optionnal()
    {
        $response = $this->post('/actors', self::data(['note' => '']));

        $response->assertOk();
        $this->assertCount(1, Actor::all());
    }

    public function test_an_actor_can_be_updated()
    {
        $this->post('/actors', self::data());

        $actor = Actor::first();

        $response = $this->patch('/actors/' . $actor->id, [
            'name' => 'New name',
            'note' => 'New notes',
        ]);

        $this->assertEquals('New name', Actor::first()->name);
        $this->assertEquals('New notes', Actor::first()->note);
    }

    public function test_references_can_be_added()
    {
        $this->post('/references', ReferenceTest::data());
        $this->post('/references', ReferenceTest::data(['name' => 'Another name']));

        $data = self::data(['references' => Reference::all()->modelKeys()]);
        $response = $this->post('/actors', $data);

        $response->assertOk();
        $this->assertCount(1, Actor::all());
        $this->assertEquals(Reference::first()->id, Actor::first()->references[0]->id);
    }

    public function test_references_can_be_removed()
    {
        $this->post('/references', ReferenceTest::data());
        $this->post('/references', ReferenceTest::data(['name' => 'Another name']));

        $response = $this->post('/actors', self::data(['references' => Reference::all()->modelKeys()]));

        $actor = Actor::first();

        $response = $this->patch('/actors/' . $actor->id,
                                 self::data(['references' => [Reference::first()->id]]));

        $response->assertOk();
        $this->assertCount(1, Actor::first()->references);

        $response = $this->patch('/actors/' . $actor->id,
                                 self::data(['references' => []]));

        $response->assertOk();
        $this->assertCount(0, Actor::first()->references);
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
                    'kinship_id' => $actorsKeys[0],
                    'relative_id' => $kinshipsKeys[0],
                ],
                1 => [
                    'kinship_id' => $actorsKeys[1],
                    'relative_id' => $kinshipsKeys[1],
                ],
            ],
        ]);

        $response = $this->post('/actors', $data);

        $response->assertOk();
        $this->assertCount(3, Actor::all());
        $this->assertCount(2, ActorKinship::all());
        // dump(ActorKinship::all());
        // dump(Actor::find(3)->kinshipssss[0]->kinship());
        // dump(Actor::find(3)->kinshipssss[0]->actor());
        // dump(Actor::find(3)->kinshipssss[0]->relative());
        // dump(Actor::find(3)->kinships()->getParentKey());
        // dump(Actor::find(3)->kinships());
        // dump(get_object_vars(Actor::find(3)->kinships()));
        // dump(get_class_vars(get_class(Actor::find(3)->kinships())));
        // dump(get_class_methods(get_class(Actor::find(3)->kinships)));
        // dump(Actor::find(3)->kinships()->getParentKey());
        // dump(Actor::find(3)->kinships()->getParent());
        // dump(Actor::find(1)->kinships()->getParent());
        // dump(Actor::find(3)->kinships()->getRelated());
        // dump(get_class(Actor::find(3)->kinships));

        // dump(Actor::find(3)->kinships());
        dump(Actor::find(3)->kinships);


        // $this->assertCount(2, Actor::find(3)->kinships);
        // $this->assertEquals(1, Actor::find(3)->kinships[0]->relative()->id);
        // $this->assertEquals(2, Actor::find(3)->kinships[1]->relative()->id);

        // $this->assertCount(1, Actor::find(1)->kinships);
        // $this->assertEquals(3, Actor::find(1)->kinships[0]->relative()->id);

        // $this->assertCount(1, Actor::find(2)->kinships);
        // $this->assertEquals(3, Actor::find(2)->kinships[0]->relative()->id);
    }

    public function test_kinships_can_be_removed()
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
                    'kinship_id' => $actorsKeys[0],
                    'relative_id' => $kinshipsKeys[0],
                ],
                1 => [
                    'kinship_id' => $actorsKeys[1],
                    'relative_id' => $kinshipsKeys[1],
                ],
            ],
        ]);

        $response = $this->post('/actors', $data);

        // Removing the 2nd kinship

        $data = self::data([
            'kinships' => [
                0 => [
                    'kinship_id' => $actorsKeys[0],
                    'relative_id' => $kinshipsKeys[0],
                ],
            ],
        ]);

        $response = $this->patch('/actors/' . Actor::find(3)->id, $data);

        $response->assertOk();
        $this->assertCount(1, Actor::find(3)->kinships);
        $this->assertEquals(1, Actor::find(3)->kinships[0]->kinship->relative()->id);

        $this->assertCount(1, Actor::find(1)->kinships);
        $this->assertEquals(3, Actor::find(1)->kinships[0]->kinship->relative()->id);

        $this->assertCount(0, Actor::find(2)->kinships);

        // Removing the 1st kinship

        $response = $this->patch('/actors/' . Actor::find(3)->id, self::data());

        $response->assertOk();
        $this->assertCount(0, Actor::find(3)->kinships);

        $this->assertCount(0, Actor::find(1)->kinships);
    }
}
