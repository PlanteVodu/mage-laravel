<?php

namespace Tests\Feature;

use App\Actor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActorTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_actor_can_be_added()
    {
        $response = $this->post('/actors', [
            'name' => 'Cool name',
            'note' => 'Some notes',
        ]);

        $response->assertOk();
        $this->assertCount(1, Actor::all());
    }

    public function test_a_name_is_required()
    {
        $response = $this->post('/actors', [
            'name' => '',
            'note' => 'Some notes',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_note_is_optionnal()
    {
        $response = $this->post('/actors', [
            'name' => 'Cool name',
            'note' => '',
        ]);

        $response->assertOk();
        $this->assertCount(1, Actor::all());

        $response = $this->post('/actors', [
            'name' => 'Another name',
        ]);

        $response->assertOk();
        $this->assertCount(2, Actor::all());
    }

    public function test_an_actor_can_be_updated()
    {
        $this->post('/actors', [
            'name' => 'Cool name',
            'note' => 'Some notes',
        ]);

        $actor = Actor::first();

        $response = $this->patch('/actors/' . $actor->id, [
            'name' => 'New name',
            'note' => 'New notes',
        ]);

        $this->assertEquals('New name', Actor::first()->name);
        $this->assertEquals('New notes', Actor::first()->note);
    }
}
