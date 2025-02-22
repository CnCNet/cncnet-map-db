<?php

namespace App\Extensions\Repositories\Maps;

interface MapRepositoryInterface
{
    public function mapFileExists($path);

    public function putMapFile(string $path, string $content);
}