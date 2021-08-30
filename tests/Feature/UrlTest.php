<?php

namespace Tests\Feature;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class UrlTest extends TestCase
{
    use RefreshDatabase;

    protected int $id;

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

    public function testIndex(): void
    {
        $response = $this->get(route('urls.index'));
        $response->assertOk();
    }

    public function testCreate(): void
    {
        $response = $this->get(route('urls.create'));
        $response->assertOk();
    }

    public function testStore(): void
    {
        $data = ['name' => 'https://ya.ru'];
        $request = ['url' => $data];
        $response = $this->post(route('urls.store'), $request);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('urls', $data);
    }

    public function testEmptyStore(): void
    {
        $data = ['name' => ''];
        $request = ['url' => $data];
        $response = $this->post(route('urls.store'), $request);
        $response->assertSessionHasErrors('name');
        $response->assertRedirect();
    }

    public function testIncorrectStore(): void
    {
        $data = ['name' => 'ya.ru'];
        $request = ['url' => $data];
        $response = $this->post(route('urls.store'), $request);
        $response->assertRedirect();
    }

    public function testShow(): void
    {
        $response = $this->get(route('urls.show', $this->id));
        $response->assertOk();
    }
}
