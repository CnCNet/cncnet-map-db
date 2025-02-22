<?php

namespace App\Extensions\Services\Maps\MapHandler;

use Illuminate\Http\UploadedFile;

class TdMapHandler extends MapHandler
{
    public function handle(UploadedFile $file): UploadedMap
    {
        $zip = $this->openZip($file);

        $iniData = false;
        $binData = false;

        for ($i = 0; $i < 2; $i++)
        {
            $tmp = $zip->statIndex($i);

            if ($tmp['size'] > 128 * 1024)
            {
                abort(400, 'Map file larger than expected.');
            }

            if (is_array($tmp) && preg_match('/\.ini$/i', $tmp['name']))
            {
                $iniData = $zip->getFromIndex($i);
            }
            else if (is_array($tmp) && preg_match('/\.bin$/i', $tmp['name']))
            {
                if ($tmp['size'] === 8192)
                {
                    $binData = $zip->getFromIndex($i);
                }
            }
        }

        if ($iniData === false || $binData === false)
        {
            abort(400, 'Map files not found in Zip.');
        }

        if ($this->isTextFile($iniData) === 0)
        {
            abort(400, 'No valid map file.');
        }

        $sha1 = $this->getSha1($file);
        if ($sha1 != sha1($iniData . $binData))
        {
            abort(400, 'Map file checksum differs from Zip name, rejected.');
        }

        $file = $this->newZip();
        $file->addFromString($sha1 . '.ini', $iniData);
        $file->addFromString($sha1 . '.bin', $binData);
        $filename = $file->filename;
        $file->close();

        $zipContent = file_get_contents($filename);
        unlink($filename);

        $name = $this->getMapName($iniData);

        return new UploadedMap($name, $zipContent, $sha1);
    }
}
