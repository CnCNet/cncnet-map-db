<?php
namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    public function test_upload_yuri_map(): void
    {
        Storage::fake();

        $file = UploadedFile::fake()->createWithContent(
            '889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip',
            Storage::disk('tests')->get('yr/889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip')
        );

        $response = $this->post('/upload', [
            'game' => 'yr',
            'file' => $file
        ]);

        $response->assertSuccessful();
        Storage::assertExists('yr/889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3.zip');
        $mapListContent = Storage::get('yr/maps.txt');
        $this->assertStringContainsString('889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3', $mapListContent);

    }

    public function test_upload_ra_map(): void
    {
        Storage::fake();

        $file = UploadedFile::fake()->createWithContent(
            '88f8b6aa18364d1b8c36a0e34020555acfb444e7.zip',
            Storage::disk('tests')->get('ra/88f8b6aa18364d1b8c36a0e34020555acfb444e7.zip')
        );

        $response = $this->post('/upload', [
            'game' => 'ra',
            'file' => $file
        ]);

        $response->assertSuccessful();
        Storage::assertExists('ra/88f8b6aa18364d1b8c36a0e34020555acfb444e7.zip');
        $mapListContent = Storage::get('ra/maps.txt');
        $this->assertStringContainsString('88f8b6aa18364d1b8c36a0e34020555acfb444e7', $mapListContent);

    }
}
