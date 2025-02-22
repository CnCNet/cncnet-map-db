<?php

namespace App\Extensions\Services\Maps;

use App\Extensions\Repositories\Maps\MapRepositoryInterface;
use App\Extensions\Services\Maps\MapHandler\MapHandler;
use App\Extensions\Services\Maps\MapHandler\UploadedMap;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MapService
{
    private MapRepositoryInterface $mapFileRepository;

    public function __construct(MapRepositoryInterface $repository)
    {
        $this->mapFileRepository = $repository;
    }

    public function uploadMap(string $game, UploadedFile $file) {

        $mapSha1 = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filepath = $game . '/' . $mapSha1 . '.zip';

        if($this->mapFileRepository->mapFileExists($filepath)) {
            abort(400, 'Map already exists. But Thanks anyway.');
        }

        if($this->isMapBanned($mapSha1)) {
            // Purposely being sneaky with this error message. Prevent cheat maps without giving too much away.
            abort(400, 'Map already exists. But Thanks anyway.');

        }

        $map = $this->handleMap($game, $file);

        $this->mapFileRepository->putMapFile($filepath, $map);

        $this->addToMapList($game, $map->getSha1(), $map->getName());
    }

    private function addToMapList($game, $sha1, $name) {

        $mapListPath = $game . '/maps.txt';
        if(isset($name)) {
            file_put_contents(
                Storage::path($mapListPath),
                $sha1 . ' ' .
                time() . ' ' .
                strip_tags($name) . "\n",
                FILE_APPEND | LOCK_EX
            );
        }
    }

    private function handleMap(string $game, UploadedFile $file): UploadedMap {
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

    public function search(string $game, string $query, ?int $age): array {

        $output = [];

        $raw = false;

        $handle = fopen(Storage::path($game . '/maps.txt'), "r");
        if ($handle)
        {
            while (($line = fgets($handle)) !== false)
            {

                if (stripos($line, $query, 40 + 10 + 2) === false)
                    continue;

                list($sha1, $date, $name) = explode(' ', $line, 3);

                if ($age > 0 && $age < 312 && intval($date) + ($age * 2592000) < time())
                    continue;

                $output[] = [
                    'raw' => $line,
                    'date' => date('Y-m-d H:i', $date),
                    'sha1' => $sha1,
                    'url' => $game . '/' . $sha1 . '.zip'
                ];

//                if ($raw)
//                {
//                    echo strip_tags($line);
//                }
//                else
//                {
//                    echo date('Y-m-d H:i', $date) . ' &emsp;/map ' . $sha1 . '&emsp; <A href="./' . $game . '/' . $sha1 . '.zip' . '">' .
//                        strip_tags($name) .
//                        '</a><br /><br />';
//                }
            }

            fclose($handle);

            return $output;
        }
        else
        {
            abort(500);
        }
    }
}