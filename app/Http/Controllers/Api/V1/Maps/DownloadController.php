<?php

namespace App\Http\Controllers\Api\V1\Maps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function __invoke(string $game, string $map)
    {
        $path = $game . '/' . $map;
        if(!Storage::exists($path)) {
            abort(404);
        }
        return Storage::download($path);
    }
}
