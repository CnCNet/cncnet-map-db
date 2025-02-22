<?php

namespace App\Extensions\Repositories\Maps;

use App\Extensions\Services\Maps\MapHandler\UploadedMap;

interface MapRepositoryInterface
{
    public function mapFileExists($path);

    public function putMapFile(string $path, UploadedMap $map);
}