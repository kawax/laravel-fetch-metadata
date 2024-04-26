<?php
declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

class SecFetchMode extends SecFetchBase
{
    protected string $default = 'same-origin';
    protected string $name = 'Sec-Fetch-Mode';
}
