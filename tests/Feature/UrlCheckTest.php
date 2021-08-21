<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class UrlCheckTest extends TestCase
{
    use RefreshDatabase;
    protected $id;

    protected function setUp(): void
    {
        parent::setUp();
        $name = 'https://google.com';
        DB::table('urls')->insert(
            [
                'name' => $name,
            ]
        );
        $this->id = DB::table('urls')
            ->where('name', $name)
            ->first()->id;
    }

    public function testStore()
    {
        $response = $this->post(route('url_checks.store', ['id' => $this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('url_checks', ['id' => 1]);
    }
}
