<?php

namespace App\Http\Controllers\Api\V1\Maps;

use App\Extensions\Services\Maps\MapService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Maps\SearchMapRequest;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private MapService $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    public function __invoke(SearchMapRequest $request)
    {
        $inputs = $request->validated();
        $result = $this->mapService->search($inputs['game'], $inputs['search'], $inputs['age'] ?? 0);

        return response()->json($result, 200);
    }
}
