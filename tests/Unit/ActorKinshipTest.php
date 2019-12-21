<?php

namespace Tests\Feature;

use App\Actor;
use App\Kinship;
use App\ActorKinship;
use Tests\Feature\ActorTest;
use Tests\Feature\KinshipTest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActorKinshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_actor_kinship_actor_relative() {
        Actor::create(ActorTest::data());
        Actor::create(ActorTest::data());
        Actor::create(ActorTest::data());

        Kinship::create(KinshipTest::data());
        Kinship::create(KinshipTest::data());

        ActorKinship::firstOrCreate([
            'actor_id' => 1,
            'kinship_id' => 1,
            'relative_id' => 2,
        ]);

        $this->assertCount(1, ActorKinship::all());

        $this->assertEquals(1, ActorKinship::first()->actor()->id);
        $this->assertEquals(2, ActorKinship::first()->relative()->id);

        $this->assertEquals(1, ActorKinship::first()->actor(1)->id);
        $this->assertEquals(2, ActorKinship::first()->relative(1)->id);

        $this->assertEquals(2, ActorKinship::first()->actor(2)->id);
        $this->assertEquals(1, ActorKinship::first()->relative(2)->id);
    }
}
