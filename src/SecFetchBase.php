<?php
declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\InvalidMetadataException;

abstract class SecFetchBase
{
    protected string $default = '';
    protected string $name = '';

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): mixed  $next
     * @param  string  ...$allow
     * @return mixed
     * @throws InvalidMetadataException
     */
    public function handle(Request $request, Closure $next, string ...$allow): mixed
    {
        if (
            Collection::wrap($allow)
                ->whenEmpty(fn (Collection $collection) => $collection->push($this->default))
                ->doesntContain($request->header($this->name))
        ) {
            throw new InvalidMetadataException("Invalid $this->name.");
        }

        return $next($request);
    }
}
