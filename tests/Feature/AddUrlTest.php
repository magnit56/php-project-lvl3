<?php

namespace Tests\Feature;
use Tests\TestCase;

class AddUrlTest extends TestCase
{
//    protected function setUp(): void
//    {
//    }

    public function testIndex()
    {
        $response = $this->get(route('index'));
        $response->assertOk();
    }
}
