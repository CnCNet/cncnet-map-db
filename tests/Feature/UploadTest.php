<?php
namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_upload_yuri_map(): void
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent(
            '889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip',
            Storage::disk('tests')->get('yr/889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip')
        );

        $response = $this->post('/upload', [
            'game' => 'yr',
            'file' => $file
        ]);

        $response->assertSuccessful();
        Storage::disk('local')->assertExists('yr/889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip');
    }
}
