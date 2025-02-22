<?php

namespace App\Extensions\Services\Maps\MapHandler;

use Illuminate\Http\UploadedFile;

class TsMapHandler extends MapHandler
{
    public function handle(UploadedFile $file): UploadedMap
    {
        $zip = $this->openZip($file);

        $tmp = $zip->statIndex(0);

        if (!is_array($tmp) || (!preg_match('/\.map$/i', $tmp['name']) && !preg_match('/\.mpr$/i', $tmp['name'])))
        {
            abort(400, 'Map file was not the first Zip entry.');
        }

        if ($tmp['size'] > 1280 * 1024)
        {
            abort(400, 'Map file larger than expected.');
        }

        $mapData = $zip->getFromIndex(0);
        $zip->close();

        if ($this->isTiberianSunPreviewValid($mapData, $tmp['size']) === 0)
        {
            abort(400, 'No valid map file.');
        }

        if ($this->isTextFile($mapData) === 0)
        {
            abort(400, 'No valid map file.');
        }

        $sha1 = $this->getSha1($file);
        if ($sha1 != sha1($mapData))
        {
            abort(400, 'Map file checksum differs from Zip name, rejected.');
        }

        $file = $this->newZip();
        $file->addFromString($sha1 . '.map', $mapData);
        $filename = $file->filename;
        $file->close();

        $zipContent = file_get_contents($filename);
        unlink($filename);

        $name = $this->getMapName($mapData);

        return new UploadedMap($name, $zipContent, $sha1);
    }

    private function isTiberianSunPreviewValid($iniData, $fileSize)
    {

        $returnVal = 1;

        $previewPos = stripos($iniData, '[PreviewPack]');

        if ($previewPos !== false)
        {
            $previewEnd = stripos($iniData, '[', $previewPos + 5);

            if ($previewEnd !== false)
            {
                if ($previewEnd - $previewPos > 218 * 1024)
                {
                    $returnVal = 0;
                }
            }
            else if ($fileSize - $previewPos > 218 * 1024)
            {
                $returnVal = 0;
            }
        }

        return $returnVal;
    }
}
