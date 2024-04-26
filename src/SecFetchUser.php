<?php
declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

class SecFetchUser extends AbstractSecFetch
{
    protected array $default = ['?1'];
    protected string $name = 'Sec-Fetch-User';
}
