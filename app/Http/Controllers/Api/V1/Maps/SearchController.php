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
        $raw = $inputs['raw'] ?? false;
        $result = $this->mapService->search($inputs['game'], $inputs['search'], $inputs['age'] ?? 0);

        if($raw) {
            return join(PHP_EOL, array_map(fn($m) => $m['raw'], $result));
        }
        else {
            $out = 'Use the /map command to select or suggest the map on CnCNet (you must be inside of a game room)<br /><br /><br /><br />';

            foreach ($result as $map) {
                $out .= $map['date'] . ' &emsp;/map ' . $map['hash'] . '&emsp; <A href="./' . $map['game'] . '/' . $map['hash'] . '.zip' . '">' .
                    strip_tags($map['name']) .
                    '</a><br /><br />';
            }
            return $out;
        }

    }
}
