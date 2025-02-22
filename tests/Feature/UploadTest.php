<?php
namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadTest extends TestCase
{
    private function check($game, $sha) {
        Storage::fake();

        $file = UploadedFile::fake()->createWithContent(
            $sha . '.zip',
            Storage::disk('tests')->get($game . '/' . $sha . '.zip')
        );

        $response = $this->post('/upload', [
            'game' => $game,
            'file' => $file
        ]);

        $response->assertSuccessful();
        Storage::assertExists($game . '/' . $sha .'.zip');
        $this->assertStringContainsString($sha, Storage::get($game . '/maps.txt'));
    }

    public function test_upload_yuri_map(): void
    {
        $this->check('yr', '889ca47cfcbe6005eac846ed1dcd14a1eefe6fa3');
    }

    public function test_upload_ra_map(): void
    {
        $this->check('ra', '88f8b6aa18364d1b8c36a0e34020555acfb444e7');
    }

    public function test_upload_d2_map(): void
    {
        $this->check('d2', '2673186cef2e60e4a176da8d655f59bd06e452b0');
    }

    public function test_upload_dta_map(): void
    {
        $this->check('dta', 'c53c82aef27a95a586022f42ac45659c02c688f4');
    }

    public function test_upload_td_map(): void
    {
        $this->check('td', '8d9aef5272feff9f213fe0ec2b6be0551fe7421b');
    }

    public function test_upload_ts_map(): void
    {
        $this->check('ts', '66071e9c9ee751d79903a7b865e2608a48076145');
    }
}
