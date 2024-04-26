<?php
declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

class SecFetchUser extends SecFetchBase
{
    protected string $default = '?1';
    protected string $name = 'Sec-Fetch-User';
}
