<?php

declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

class SecFetchDest extends AbstractSecFetch
{
    protected array $default = ['document'];

    protected string $name = 'Sec-Fetch-Dest';
}
