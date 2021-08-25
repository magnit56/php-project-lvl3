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

    protected function setUp(): void
    {
        parent::setUp();
        $this->name = 'https://google.com';
        $data = ['name' => $this->name];
        $this->id = DB::table('urls')->insertGetId($data);
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
        Http::fake([
            "{$this->name}" => Http::response('Hello World', 200),
        ]);
        $data = [
            'id' => 1,
            'status_code' => 200,
        ];
        StoreSeoInformation::dispatch($this->id, $this->name);
        $this->assertDatabaseHas('url_checks', $data);
    }
}
