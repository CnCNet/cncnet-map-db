<?php

namespace App\Extensions\Repositories\Maps;

use Illuminate\Support\Facades\Storage;

class MapStorageRepository implements MapRepositoryInterface
{
    public function mapFileExists($path)
    {
        return Storage::exists($path);
    }

    public function putMapFile(string $path, string $content)
    {
        Storage::put($path, $content);
    }
}