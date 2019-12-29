<?php

namespace Tests\Feature;

use App\Actor;
use App\Reference;
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
        $this->withoutExceptionHandling();

        $actor = factory(Actor::class)->make();
        $response = $this->post('/actors', $actor->toArray());

        $response->assertOk();
        $this->assertCount(1, Actor::all());
    }

    public function test_a_name_is_required()
    {
        $actor = factory(Actor::class)->make(['name' => '']);
        $response = $this->post('/actors', $actor->toArray());

        $response->assertSessionHasErrors('name');
    }

    public function test_an_actor_can_be_updated()
    {
        $actor = factory(Actor::class)->make();

        $this->post('/actors', $actor->toArray());

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
        $actor = factory(Actor::class)->states('with_dates')->make();
        $response = $this->post('/actors', $actor->toArray());

        $response->assertOk();
        $this->assertCount(1, Actor::all());
        $this->assertEquals($actor->date_start, Actor::first()->date_start);
        $this->assertEquals($actor->date_end, Actor::first()->date_end);
        $this->assertEquals($actor->date_start_accuracy, Actor::first()->date_start_accuracy);
        $this->assertEquals($actor->date_end_accuracy, Actor::first()->date_end_accuracy);
    }

    public function test_actor_dates_must_be_dates()
    {
        $actor = factory(Actor::class)->states('with_dates')->make([
            'date_start' => 'not_a_date',
            'date_end' => 'not_a_date',
        ]);
        $response = $this->post('/actors', $actor->toArray());

        $response->assertSessionHasErrors('date_start');
        $response->assertSessionHasErrors('date_end');
    }

    public function test_actor_dates_accuracies_are_enum()
    {
        $accuracies = ['exactly', 'circa', 'before', 'after'];

        foreach (array_values($accuracies) as $i => $accuracy) {
            $actor = factory(Actor::class)->states('with_dates')->make([
                'date_start_accuracy' => $accuracy,
                'date_end_accuracy' => $accuracy,
            ]);
            $response = $this->post('/actors', $actor->toArray());

            $response->assertOk();
            $this->assertCount($i + 1, Actor::all());
        }

        $actor = factory(Actor::class)->states('with_dates')->make([
            'date_start_accuracy' => 'not_an_allowed_value',
            'date_end_accuracy' => 'not_an_allowed_value',
        ]);
        $response = $this->post('/actors', $actor->toArray());
        $response->assertSessionHasErrors('date_start_accuracy');
        $response->assertSessionHasErrors('date_end_accuracy');
        $this->assertCount($i + 1, Actor::all());
    }

    public function test_actor_dates_must_be_set_when_accuracies()
    {
        $actor = factory(Actor::class)->states('with_dates')->make([
            'date_start' => 'not_a_date',
            'date_end' => 'not_a_date',
        ]);
        $response = $this->post('/actors', $actor->toArray());

        $response->assertSessionHasErrors('date_start');
        $response->assertSessionHasErrors('date_end');
    }
}
