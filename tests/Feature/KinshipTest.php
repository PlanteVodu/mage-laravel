<?php

namespace Tests\Feature;

use App\Kinship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KinshipTest extends TestCase
{
    use RefreshDatabase;

    public static function data($array = [])
    {
        $data = [
            'name' => 'Cool kinship',
            'coefficient' => 1,
        ];

        return array_merge($data, $array);
    }

    public function test_a_kinship_can_be_added()
    {
        $response = $this->post('kinships', self::data());

        $response->assertOk();
        $this->assertCount(1, Kinship::all());
    }

    public function test_a_name_is_required()
    {
        $response = $this->post('/kinships', self::data(['name' => '']));

        $response->assertSessionHasErrors('name');
    }

    public function test_coefficient_is_optionnal()
    {
        $response = $this->post('/kinships', self::data(['kinship' => NULL]));

        $response->assertOk();
    }

    public function test_a_reference_can_be_updated()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/kinships', self::data());

        $kinship = Kinship::first();

        $response = $this->patch('/kinships/' . $kinship->id, [
            'name' => 'New name',
            'coefficient' => 2,
        ]);

        $this->assertEquals('New name', Kinship::first()->name);
        $this->assertEquals(2, Kinship::first()->coefficient);
    }

    public function test_a_kinship_can_be_deleted()
    {
        $kinship = factory(Kinship::class)->create();

        $this->assertCount(1, Kinship::all());

        $response = $this->delete('/kinships/' . $kinship->id);

        $this->assertCount(0, Kinship::all());
    }

}
