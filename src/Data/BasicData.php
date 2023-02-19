<?php

namespace Riclep\StoryblokCli\Data;

use Illuminate\Support\Collection;

class BasicData
{
    protected array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getCollectionFromResponse($name): Collection
    {
        if (! array_key_exists($name, $this->response)) {
            return collect([]);
        }

        return collect($this->response[$name]);
    }
}
