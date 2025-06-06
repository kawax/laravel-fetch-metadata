<?php

declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

class SecFetchMode extends AbstractSecFetch
{
    protected array $default = ['navigate', 'cors'];

    protected string $name = 'Sec-Fetch-Mode';
}
