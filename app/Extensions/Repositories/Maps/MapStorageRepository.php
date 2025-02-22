<?php

namespace App\Extensions\Repositories\Maps;

use App\Extensions\Services\Maps\MapHandler\UploadedMap;
use Illuminate\Support\Facades\Storage;

class MapStorageRepository implements MapRepositoryInterface
{
    public function mapFileExists($path)
    {
        return Storage::exists($path);
    }

    public function putMapFile(string $path, UploadedMap $map)
    {
        Storage::put($path, $map->getBuffer());
    }
}