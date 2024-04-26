<?php
declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

class SecFetchSite extends SecFetchBase
{
    protected string $default = 'same-origin';
    protected string $name = 'Sec-Fetch-Site';
}
