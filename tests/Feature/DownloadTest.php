<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_map_download(): void
    {
        Storage::fake();
        Storage::put('yr/889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip', Storage::disk('tests')->get('yr/889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip'));

        $response = $this->get('/yr/889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/zip');
    }
}
