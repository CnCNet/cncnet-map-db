<?php

namespace App\Http\Controllers\Api\V1\Maps;

use App\Extensions\Services\Maps\MapService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Maps\UploadMapRequest;

class UploadController extends Controller
{
    private MapService $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    public function __invoke(UploadMapRequest $request)
    {
        $inputs = $request->validated();
        $this->mapService->uploadMap($inputs['game'], $request->file('file'));
        return response()->json([
            'success' => true,
        ], 200);
    }
}
