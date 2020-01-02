<?php

namespace Tests\Feature;

use App\Actor;
use App\Kinship;
use App\ActorKinship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActorKinshipTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        factory(Actor::class, 3)->create();
        factory(Kinship::class, 2)->create();
    }

    public function test_actor_kinship_actor_relative() {
        ActorKinship::firstOrCreate([
            'actor_id' => 1,
            'kinship_id' => 1,
            'relative_id' => 2,
        ]);

        $this->assertCount(1, ActorKinship::all());

        // Check ActorKinship values as they are stored in database
        $this->assertEquals(1, ActorKinship::first()->actor()->id);
        $this->assertEquals(2, ActorKinship::first()->relative()->id);

        // Check ActorKinship values relative to the kinship's 'actor_id'
        $this->assertEquals(1, ActorKinship::first()->actor(1)->id);
        $this->assertEquals(2, ActorKinship::first()->relative(1)->id);

        // Check ActorKinship values relative to the kinship's 'relative_id'
        $this->assertEquals(2, ActorKinship::first()->actor(2)->id);
        $this->assertEquals(1, ActorKinship::first()->relative(2)->id);
    }

    public function test_retrieve_actor_relatives()
    {
        ActorKinship::firstOrCreate([
            'actor_id' => 1,
            'kinship_id' => 1,
            'relative_id' => 2,
        ]);
        ActorKinship::firstOrCreate([
            'actor_id' => 1,
            'kinship_id' => 1,
            'relative_id' => 3,
        ]);

        //====================================================
        // *** Check for Actor #1 ***
        //====================================================
        $actor1 = Actor::find(1);

        // Check the number of relatives
        $this->assertCount(2, $actor1->kinships);
        $this->assertCount(2, $actor1->getRelatives());

        // Check Actor is not a relative of itself
        $this->assertFalse($actor1->hasRelative(1));
        $this->assertNull($actor1->getKinshipWith(1));

        // Check Actor hasRelative method
        $this->assertTrue($actor1->hasRelative(2));
        $this->assertTrue($actor1->hasRelative(3));
        $this->assertFalse($actor1->hasRelative(4));

        // Check retrieving ActorKinships for a given relative
        $this->assertEquals(ActorKinship::find(1), $actor1->getKinshipWith(2));
        $this->assertEquals(ActorKinship::find(2), $actor1->getKinshipWith(3));
        $this->assertNull($actor1->getKinshipWith(4));

        //====================================================
        // *** Check for Actor #2 ***
        //====================================================
        $actor2 = Actor::find(2);

        // Check the number of relatives
        $this->assertCount(1, $actor2->kinships);
        $this->assertCount(1, $actor2->getRelatives());

        // Check Actor is not a relative of a non-related Actor
        $this->assertFalse($actor2->hasRelative(3));
        $this->assertNull($actor2->getKinshipWith(3));

        // Check Actor hasRelative method
        $this->assertTrue($actor2->hasRelative(1));

        // Check retrieving ActorKinships for a given relative
        $this->assertEquals(ActorKinship::find(1), $actor2->getKinshipWith(1));
    }
}
