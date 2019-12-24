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

    public function addActorKinshipData($array = [])
    {
        $this->post('/kinships', KinshipTest::data());
        $this->post('/kinships', KinshipTest::data());

        $this->post('/actors', self::data());
        $this->post('/actors', self::data());
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

    public function test_an_actor_can_have_dates()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/actors', self::data([
            'date_start' => '1/1/1000',
            'date_end' => '1/1/1050',
            'date_start_accuracy' => 'exactly',
            'date_end_accuracy' => 'exactly',
        ]));

        $response->assertOk();
        $this->assertCount(1, Actor::all());
        $this->assertEquals('1/1/1000', Actor::first()->date_start);
        $this->assertEquals('1/1/1050', Actor::first()->date_end);
        $this->assertEquals('exactly', Actor::first()->date_start_accuracy);
        $this->assertEquals('exactly', Actor::first()->date_end_accuracy);
    }

    public function test_actor_dates_must_be_dates()
    {
        $response = $this->post('/actors', self::data([
            'date_start' => 'not_a_date',
            'date_end' => 'not_a_date',
            'date_start_accuracy' => 'exactly',
            'date_end_accuracy' => 'exactly',
        ]));

        $response->assertSessionHasErrors('date_start');
        $response->assertSessionHasErrors('date_end');
    }

    public function test_actor_dates_accuracies_are_enum()
    {
        // $this->withoutExceptionHandling();
        $accuracies = ['exactly', 'circa', 'before', 'after'];

        foreach (array_values($accuracies) as $i => $accuracy) {
            $response = $this->post('/actors', self::data([
                'date_start' => '1/1/1050',
                'date_end' => '1/1/1050',
                'date_start_accuracy' => $accuracy,
                'date_end_accuracy' => $accuracy,
            ]));

            $response->assertOk();
            $this->assertCount($i + 1, Actor::all());
        }

        $response = $this->post('/actors', self::data([
            'date_start' => '1/1/1050',
            'date_end' => '1/1/1050',
            'date_start_accuracy' => 'not_an_allowed_value',
            'date_end_accuracy' => 'not_an_allowed_value',
        ]));

        $response->assertSessionHasErrors('date_start_accuracy');
        $response->assertSessionHasErrors('date_end_accuracy');
        $this->assertCount($i + 1, Actor::all());
    }

    public function test_actor_dates_must_be_set_when_accuracies()
    {
        $response = $this->post('/actors', self::data([
            'date_start_accuracy' => 'exactly',
            'date_end_accuracy' => 'exactly',
        ]));

        $response->assertSessionHasErrors('date_start');
        $response->assertSessionHasErrors('date_end');
    }
}
