<?php

namespace App\Extensions\Services\Maps;

use App\Extensions\Repositories\Maps\MapRepositoryInterface;
use App\Extensions\Services\Maps\MapHandler\MapHandler;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class MapService
{
    private MapRepositoryInterface $repository;

    public function __construct(MapRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function uploadMap(string $game, UploadedFile $file) {

        $mapSha1 = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filepath = $game . '/' . $mapSha1 . '.zip';

        if($this->repository->mapFileExists($filepath)) {
            abort(400, 'Map already exists. But Thanks anyway.');
        }

        if($this->isMapBanned($mapSha1)) {
            // Purposely being sneaky with this error message. Prevent cheat maps without giving too much away.
            abort(400, 'Map already exists. But Thanks anyway.');

        }

        $buffer = $this->handleMap($game, $file);

        $this->repository->putMapFile($filepath, $buffer);


    }

    private function handleMap(string $game, UploadedFile $file): string {
        $verifiers = config('cncnet.map_verifiers');
        $verifier = $verifiers[$game];

        if (!is_subclass_of($verifier, MapHandler::class)) {
            abort(400, 'Game not supported.');
        }

        $v = new $verifier();
        return $v->handle($file);
    }

    private function isMapBanned(string $sha1): bool {
        try
        {
            $bannedList = $this->getBannedMapHashes();
            Log::debug("Got list of banned sha1s: " . count($bannedList));

            $isBanned = in_array($sha1, $bannedList);
            if ($isBanned)
            {
                // Fetch client IP from the X-Forwarded-For header
                $clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'IP not available';

                // Log the attempt to share a banned map along with the client IP
                Log::warning('Attempted to share banned map: ' . $sha1 . ' from IP: ' . $clientIP);
            }
            return $isBanned;
        }
        catch (\Exception $e)
        {
            Log::error($e->getMessage());
            return false;
        }
    }

    private function getBannedMapHashes()
    {
        $bannedMapHashes = [];

        try
        {
            $options = [
                'http' => [
                    'method' => 'GET',
                    'timeout' => 15
                ]
            ];

            $bannedMapListUrl = "https://raw.githubusercontent.com/CnCNet/maps-banned/main/hashes.txt";
            $context = stream_context_create($options);
            $response = @file_get_contents($bannedMapListUrl, false, $context);

            if ($response === false)
            {
                return $bannedMapHashes;
            }

            // Split the response into an array by newline characters
            $responseSha1s = explode("\n", $response);

            // Trim each hash to remove any extra whitespace
            $responseSha1s = array_map("trim", $responseSha1s);

            // Remove any empty elements
            $bannedMapHashes = array_filter($responseSha1s);

            return $bannedMapHashes;
        }
        catch (\Exception $e)
        {
            Log::error("getBannedMapHashes ** " . $e->getMessage());
            return $bannedMapHashes;
        }
    }
}