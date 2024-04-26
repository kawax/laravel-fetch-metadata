<?php
declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

class SecFetchDest extends SecFetchBase
{
    protected string $default = 'document';
    protected string $name = 'Sec-Fetch-Dest';
}
