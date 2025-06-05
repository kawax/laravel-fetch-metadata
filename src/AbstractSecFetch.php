<?php

declare(strict_types=1);

namespace Revolution\FetchMetadata\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

abstract class AbstractSecFetch
{
    protected array $default = [];

    protected string $name = '';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): mixed  $next
     *
     * @throws BadRequestHttpException
     */
    public function handle(Request $request, Closure $next, string ...$allow): mixed
    {
        if (
            Collection::wrap($allow)
                ->whenEmpty(fn (Collection $collection) => $collection->push(...$this->default))
                ->doesntContain($request->header($this->name))
        ) {
            throw new BadRequestHttpException("Invalid $this->name.");
        }

        return $next($request);
    }
}
