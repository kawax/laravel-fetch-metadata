# Fetch metadata middleware for Laravel

https://developer.mozilla.org/en-US/docs/Glossary/Fetch_metadata_request_header

## Requirement

- PHP ^8.2
- Laravel ^11.x

## Installation

```bash
composer require revolution/laravel-fetch-metadata
```

### Uninstall

```bash
composer remove revolution/laravel-fetch-metadata
```

### (Optional) Add middleware alias to `bootstrap/app.php`

```php
use Illuminate\Foundation\Configuration\Middleware;
use Revolution\FetchMetadata\Middleware\SecFetchSite;
use Revolution\FetchMetadata\Middleware\SecFetchMode;
use Revolution\FetchMetadata\Middleware\SecFetchDest;
use Revolution\FetchMetadata\Middleware\SecFetchUser;

->withMiddleware(function (Middleware $middleware) {
     $middleware->alias([
        'sec-fetch-site' => SecFetchSite::class,
        'sec-fetch-mode' => SecFetchMode::class,
        'sec-fetch-dest' => SecFetchDest::class,
        'sec-fetch-user' => SecFetchUser::class,
    ]);
})
```

You can use only some of the middleware.

```php
use Illuminate\Foundation\Configuration\Middleware;
use Revolution\FetchMetadata\Middleware\SecFetchSite;

->withMiddleware(function (Middleware $middleware) {
     $middleware->alias([
        'sec-fetch-site' => SecFetchSite::class,
    ]);
})
```

The alias name is arbitrary and can be shortened.

```php
use Illuminate\Foundation\Configuration\Middleware;
use Revolution\FetchMetadata\Middleware\SecFetchSite;

->withMiddleware(function (Middleware $middleware) {
     $middleware->alias([
        'sec-site' => SecFetchSite::class,
    ]);
})
```

## Usage in routing
Default behavior only allows `same-origin` and `none`(user-originated operation).

```php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('user/update-password', function (Request $request){
    //
})->middleware('sec-fetch-site');
```

You can specify allowed values via middleware parameters.

```php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('user/update-password', function (Request $request){
    //
})->middleware('sec-fetch-site:cross-site');
```

You can also use multiple middleware parameters.

```php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::post('user/update-password', function (Request $request){
    //
})->middleware('sec-fetch-site:same-origin,cross-site');
```

When not using an alias.

```php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Revolution\FetchMetadata\Middleware\SecFetchSite;

Route::post('user/update-password', function (Request $request){
    //
})->middleware(SecFetchSite::class);

Route::post('user/update-password', function (Request $request){
    //
})->middleware(SecFetchSite::class.':same-origin,cross-site');
```

## Error Handling
When Sec-Fetch value is invalid, throw the `Symfony\Component\HttpKernel\Exception\InvalidMetadataException`

You can change the response in `bootstrap/app.php`.

```php
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\InvalidMetadataException;
 
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (InvalidMetadataException $e, Request $request) {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    });
})
```

## LICENSE

MIT
