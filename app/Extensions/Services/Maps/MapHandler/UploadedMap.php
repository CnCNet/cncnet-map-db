<?php

namespace App\Extensions\Services\Maps\MapHandler;

class UploadedMap
{
    private string $buffer;
    private string | null $name;
    private string $sha1;

    public function __construct(string | null $name, string $buffer, string $sha1)
    {
        $this->name = $name;
        $this->buffer = $buffer;
        $this->sha1 = $sha1;
    }

    /**
     * @return string
     */
    public function getBuffer(): string
    {
        return $this->buffer;
    }

    /**
     * @return string
     */
    public function getName(): string | null
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSha1(): string
    {
        return $this->sha1;
    }
}