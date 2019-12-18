<?php

namespace Tests\Feature;

use App\Actor;
use App\Reference;
use App\Kinship;
use Tests\Feature\ReferenceTest;
// use Tests\Feature\KinshipTest;
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

        $data = self::data(['kinships' => Kinship::all()->modelKeys()]);
        $response = $this->post('/actors', $data);

        $response->assertOk();
        $this->assertCount(3, Actor::all());
        // $this->assertCount(1, Actor::first()->kinships);
        $this->assertCount(2, Actor::find(3)->kinships);
        // $this->assertEquals(Kinship::first()->id, Actor::first()->kinships[0]->id);
    }

    public function test_kinships_can_be_removed()
    {
        $this->post('/kinships', KinshipTest::data());
        $this->post('/kinships', KinshipTest::data(['name' => 'Another name']));

        $response = $this->post('/actors', self::data(['kinships' => Kinship::all()->modelKeys()]));

        $actor = Actor::first();

        $response = $this->patch('/actors/' . $actor->id,
                                 self::data(['kinships' => [Kinship::first()->id]]));

        $response->assertOk();
        $this->assertCount(1, Actor::first()->kinships);

        $response = $this->patch('/actors/' . $actor->id,
                                 self::data(['kinships' => []]));

        $response->assertOk();
        $this->assertCount(0, Actor::first()->kinships);
    }
}
