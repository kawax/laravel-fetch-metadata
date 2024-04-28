<?php

declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

class SecFetchSite extends AbstractSecFetch
{
    protected array $default = ['same-origin', 'none'];
    protected string $name = 'Sec-Fetch-Site';
}
