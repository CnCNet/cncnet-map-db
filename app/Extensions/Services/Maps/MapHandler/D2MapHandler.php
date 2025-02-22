<?php

namespace App\Extensions\Services\Maps\MapHandler;

use Illuminate\Http\UploadedFile;

class D2MapHandler extends MapHandler
{
    public function handle(UploadedFile $file): UploadedMap
    {
        $zip = $this->openZip($file);

        $mapData = null;
        $iniData = null;
        $misData = null;

        $isValidMapData = 0;
        $isValidIniData = 0;
        $isValidMisData = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $tmp = $zip->statIndex($i);

            if ($tmp['size'] > 128 * 128 * 8) {
                abort(400, 'Map file larger than expected.');
            }

            $fileContent = $zip->getFromIndex($i);

            if (is_array($tmp) && preg_match('/\.map$/i', $tmp['name'])) {
                $mapData = $fileContent;
                if ($mapData !== null) {
                    $isValidMapData = $this->isDuneMapFileValid($mapData, $tmp['size']);
                }
            } elseif (is_array($tmp) && preg_match('/\.ini$/i', $tmp['name'])) {
                $iniData = $fileContent;
                if ($iniData !== null) {
                    $isValidIniData = $this->isDuneIniFileValid($iniData, $tmp['size']);
                }
            } elseif (is_array($tmp) && preg_match('/\.mis$/i', $tmp['name'])) {
                $misData = $fileContent;
                if ($misData !== null) {
                    $isValidMisData = $this->isDuneMisFileValid($misData, $tmp['size']);
                }
            }
        }

        $zip->close();

        if ($isValidMapData === 0) {
            abort(400, 'Valid map file not found in Zip.');
        }

        if ($isValidIniData === 0) {
            abort(400, 'Valid map ini file not found in Zip.');
        }

        if ($misData !== null && $isValidMisData === 0) {
            abort(400, 'Valid map mis file not found in Zip.');
        }

        if ($this->isTextFile($iniData) === 0) {
            abort(400, 'No valid map file.');
        }

        $sha1 = $this->getSha1($file);
        if ($sha1 != sha1($mapData . $iniData . $misData)) {
            abort(400, 'Map file checksum differs from Zip name, rejected.');
        }

        $zipArchive = $this->newZip();
        $zipArchive->addFromString($sha1 . '.map', $mapData);
        $zipArchive->addFromString($sha1 . '.ini', $iniData);

        if ($misData) {
            $zipArchive->addFromString('_' . $sha1 . '.mis', $misData);
        }

        $filename = $zipArchive->filename;
        $zipArchive->close();

        $zipContent = file_get_contents($filename);
        unlink($filename);

        $name = $this->getMapName($iniData);

        return new UploadedMap($name, $zipContent, $sha1);
    }

    private function isDuneMapFileValid($mapData, $fileSize)
    {
        $values = unpack('v*', $mapData);
        $valuesSize = sizeof($values);

        if ($valuesSize < 2) {
            return 0;
        }

        $height = $values[1];
        $width = $values[2];

        if ($height > 128 || $height <= 0 || $width > 128 || $width <= 0) {
            return 0;
        }

        if (($height * $width * 4) + 4 != $fileSize) {
            return 0;
        }

        $iter = 3;
        while ($iter <= $valuesSize) {
            $tile = $values[$iter++];
            if ($tile >= 800 || $tile < 0) {
                return 0;
            }

            $special = $values[$iter++];
            if ($special >= 1000 || $special < 0) {
                return 0;
            }
        }

        return 1;
    }

    private function isDuneIniFileValid($iniData, $fileSize)
    {
        if ($fileSize > 100 * 1024) {
            return 0;
        }

        if (strpos($iniData, '[Basic]') === false || strpos($iniData, 'Name') === false) {
            return 0;
        }

        return 1;
    }

    private function isDuneMisFileValid($misData, $fileSize)
    {
        if ($fileSize != 68066) {
            return 0;
        }

        $tmp = unpack('C*', $misData);
        $tmpUint = unpack('V*', $misData);

        for ($i = 3; $i < 10; $i++) {
            if ($tmpUint[$i] > 70000) {
                return 0;
            }
        }

        for ($i = 1; $i < 8; $i++) {
            if ($tmp[$i] > 9) {
                return 0;
            }
        }

        for ($i = 66978; $i < 67167; $i++) {
            if ($tmp[$i] != 0) {
                return 0;
            }
        }

        for ($i = 67179; $i < 67353; $i++) {
            if ($tmp[$i] != 0) {
                return 0;
            }
        }

        return 1;
    }
}
