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
    protected $id;
    protected $name;
    protected $fixture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->name = 'https://sports.com';
        $data = ['name' => $this->name];
        $this->id = DB::table('urls')->insertGetId($data);
        $this->fixture = './tests/fixtures/sportscom';
    }

    public function testStore()
    {
        Queue::fake();

        $response = $this->post(route('url_checks.store', ['id' => $this->id]));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        Queue::assertPushed(StoreSeoInformation::class);
    }

    public function testStoreSeoInformation()
    {
        $html = file_get_contents(realpath( $this->fixture));

        Http::fake([
            "{$this->name}" => Http::response($html, 200),
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
