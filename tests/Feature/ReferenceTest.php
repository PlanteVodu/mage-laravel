<?php

namespace Tests\Feature;

use App\Reference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_reference_can_be_added()
    {
        $response = $this->post('/references', [
            'category' => 'source',
            'name' => 'Cool name',
            'note' => 'Some notes',
        ]);

        $response->assertOk();
        $this->assertCount(1, Reference::all());
    }

    public function test_a_category_is_required()
    {
        $response = $this->post('/references', [
            'category' => '',
            'name' => 'Cool name',
            'note' => 'Some notes',
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_category_is_an_enumeration()
    {
        $allowedCategories = ['source', 'bibliography'];

        foreach ($allowedCategories as $category) {
            $response = $this->post('/references', [
                'category' => 'source',
                'name' => 'Cool name',
                'note' => 'Some notes',
            ]);

            $response->assertOk();
        }

        $response = $this->post('/references', [
            'category' => 'other',
            'name' => 'Cool name',
            'note' => 'Some notes',
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_a_name_is_required()
    {
        $response = $this->post('/references', [
            'category' => 'source',
            'name' => '',
            'note' => 'Some notes',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_note_is_optionnal()
    {
        $response = $this->post('/references', [
            'category' => 'source',
            'name' => 'Cool name',
            'note' => '',
        ]);

        $response->assertOk();
    }

    public function test_a_reference_can_be_updated()
    {
        $response = $this->post('/references', [
            'category' => 'source',
            'name' => 'Cool name',
            'note' => 'Some notes',
        ]);

        $reference = Reference::first();

        $response = $this->patch('/references/' . $reference->id, [
            'category' => 'bibliography',
            'name' => 'New name',
            'note' => 'New notes',
        ]);

        $this->assertEquals('bibliography', Reference::first()->category);
        $this->assertEquals('New name', Reference::first()->name);
        $this->assertEquals('New notes', Reference::first()->note);
    }
}
