<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\StoreSeoInformation;

class UrlCheckTest extends TestCase
{
    use RefreshDatabase;

    protected mixed $id;
    protected mixed $name;
    protected mixed $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->name = 'https://sports.com';
        $data = ['name' => $this->name];
        $this->id = DB::table('urls')->insertGetId($data);
        $this->fixture = './tests/fixtures/sportscom';
    }

    public function testStore(): void
    {
        Queue::fake();

        $response = $this->post(route('url_checks.store', ['id' => $this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        Queue::assertPushed(StoreSeoInformation::class);
    }

    public function testStoreSeoInformation(): void
    {
        $html = file_get_contents(strval(realpath($this->fixture)));

        Http::fake([
            "{$this->name}" => Http::response(strval($html), 200),
        ]);
        $data = [
            'id' => 1,
            'status_code' => 200,
            'h1' => 'Olympic games news',
            'keywords' => 'Sport, football, hockey',
            'description' => 'All about sport',
        ];
        StoreSeoInformation::dispatch($this->id, $this->name);
        $this->assertDatabaseHas('url_checks', $data);
    }
}
