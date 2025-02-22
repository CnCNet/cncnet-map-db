<?php

namespace App\Extensions\Services\Maps\MapHandler;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

abstract class MapHandler
{

    public abstract function handle(UploadedFile $file): UploadedMap;

    protected function getSha1(UploadedFile $file): string {
        return pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    }

    protected function newZip(): \ZipArchive {
        $zip = new \ZipArchive();
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');
        Log::debug("Creating temporary file at $tempFile");
        $res = $zip->open($tempFile, \ZipArchive::OVERWRITE);
        Log::debug("res $res");
        if($res !== true) {
            throw new \Exception("Failed to open zip file.");
        }
        return $zip;
    }

    protected function openZip(UploadedFile $file): \ZipArchive {
        $zip = new \ZipArchive();
        $res = $zip->open($file->getRealPath());

        if($res !== true) {
            throw new \Exception("Failed to open zip file.");
        }

        return $zip;
    }

    protected function isTextFile($fileData)
    {
        $returnVal = 1;

        $tmp = unpack('C*', $fileData);

        // map files for westwood games don't have any low bytes below 9 (dos??? encoding)
        foreach ($tmp as $value)
        {
            if ($value < 9)
            {
                $returnVal = 0;
                break;
            }
        }

        return $returnVal;
    }

    protected function getMapName($mapData): string | null
    {
        $basicPos = strpos($mapData, '[Basic]');
        if ($basicPos !== false) {

            $namePos = strpos($mapData, 'Name=', $basicPos + 5);
            if ($namePos !== false) {

                $nameEnd = strpos($mapData, "\n", $namePos);
                if ($nameEnd !== false) {

                    $mapName = substr($mapData, $namePos + 5, $nameEnd - ($namePos + 5));
                    if ($mapName !== false && strlen($mapName) < 150) {

                        return strip_tags($mapName);
                    }
                }
            }
        }
        return null;
    }
}