<?php

namespace Tests\Feature;

use App\Reference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceTest extends TestCase
{
    use RefreshDatabase;

    public static function data($array = [])
    {
        $data = [
            'category' => 'source',
            'name' => 'Cool name',
            'note' => 'Some notes',
        ];

        return array_merge($data, $array);
    }

    public function test_a_reference_can_be_added()
    {
        $reference = factory(Reference::class)->make();
        $response = $this->post('/references', $reference->toArray());

        $response->assertOk();
        $this->assertCount(1, Reference::all());
    }

    public function test_a_category_is_required()
    {
        $reference = factory(Reference::class)->make(['category' => '']);
        $response = $this->post('/references', $reference->toArray());

        $response->assertSessionHasErrors('category');
    }

    public function test_category_is_an_enumeration()
    {
        $allowedCategories = ['source', 'bibliography'];

        foreach ($allowedCategories as $category) {
            $reference = factory(Reference::class)->make(['category' => $category]);
            $response = $this->post('/references', $reference->toArray());
            $response->assertOk();
        }

        $reference = factory(Reference::class)->make(['category' => 'not a category']);
        $response = $this->post('/references', $reference->toArray());
        $response->assertSessionHasErrors('category');
    }

    public function test_a_name_is_required()
    {
        $reference = factory(Reference::class)->make(['name' => '']);
        $response = $this->post('/references', $reference->toArray());

        $response->assertSessionHasErrors('name');
    }

    public function test_a_reference_can_be_updated()
    {
        factory(Reference::class)->create();
        $reference = Reference::first();

        $response = $this->patch('/references/' . $reference->id, [
            'category' => 'bibliography',
            'name' => 'New name',
            'note' => 'New notes',
        ]);

        $response->assertOk();
        $this->assertEquals('bibliography', Reference::first()->category);
        $this->assertEquals('New name', Reference::first()->name);
        $this->assertEquals('New notes', Reference::first()->note);
    }
}
