# Fetch metadata middleware for Laravel

https://developer.mozilla.org/en-US/docs/Glossary/Fetch_metadata_request_header

## Overview

Laravel Fetch Metadata is a security-focused middleware package that validates Sec-Fetch-* HTTP headers to protect your Laravel applications from CSRF attacks and unwanted cross-site requests. The package provides four specialized middleware classes that examine browser-generated fetch metadata headers, allowing you to control which types of requests are permitted based on their origin, mode, destination, and user interaction status.

By leveraging the browser's built-in security features, this package helps prevent malicious requests from unauthorized origins while maintaining a seamless experience for legitimate users.

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

## Usage Examples

This section demonstrates common use cases for the `Sec-Fetch-Site` and `Sec-Fetch-Mode` middleware with practical examples.

### Sec-Fetch-Site Examples

The `Sec-Fetch-Site` header indicates the relationship between the request initiator's origin and the target's origin. By default, this middleware allows `same-origin` and `none` (user-initiated requests).

**Basic protection for sensitive operations:**
```php
// Only allow requests from the same origin or direct user navigation
Route::post('user/delete-account', function (Request $request) {
    // Handle account deletion
})->middleware('sec-fetch-site');
```

**Allow cross-site requests for public APIs:**
```php
// Allow requests from any origin for public API endpoints
Route::get('api/public/data', function (Request $request) {
    return response()->json(['data' => 'public']);
})->middleware('sec-fetch-site:same-origin,cross-site,same-site');
```

**Restrict to same-origin only:**
```php
// Only allow requests from the exact same origin
Route::post('admin/settings', function (Request $request) {
    // Handle admin settings
})->middleware('sec-fetch-site:same-origin');
```

**Allow same-site requests (subdomains):**
```php
// Allow requests from subdomains of the same site
Route::post('api/internal', function (Request $request) {
    // Handle internal API calls
})->middleware('sec-fetch-site:same-origin,same-site');
```

For more information about `Sec-Fetch-Site` values, see the [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Sec-Fetch-Site).

### Sec-Fetch-Mode Examples

The `Sec-Fetch-Mode` header indicates the mode of the request. By default, this middleware allows `navigate` and `cors` requests.

**Protect forms from programmatic requests:**
```php
// Only allow navigation requests (user clicking links/submitting forms)
Route::post('contact/submit', function (Request $request) {
    // Handle contact form submission
})->middleware('sec-fetch-mode:navigate');
```

**Allow CORS requests for API endpoints:**
```php
// Allow both navigation and CORS requests for API endpoints
Route::post('api/data', function (Request $request) {
    return response()->json(['status' => 'success']);
})->middleware('sec-fetch-mode'); // Uses default: navigate,cors
```

**Restrict to navigation only:**
```php
// Only allow user-initiated navigation (clicking links, form submissions)
Route::post('user/login', function (Request $request) {
    // Handle user login
})->middleware('sec-fetch-mode:navigate');
```

**Allow all request modes:**
```php
// Allow navigation, CORS, no-cors, same-origin, and websocket requests
Route::post('api/webhook', function (Request $request) {
    // Handle webhook data
})->middleware('sec-fetch-mode:navigate,cors,no-cors,same-origin,websocket');
```

**Combining multiple middleware:**
```php
// Use both Sec-Fetch-Site and Sec-Fetch-Mode for enhanced security
Route::post('user/update-profile', function (Request $request) {
    // Handle profile updates
})->middleware(['sec-fetch-site:same-origin', 'sec-fetch-mode:navigate']);
```

For more information about `Sec-Fetch-Mode` values, see the [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Sec-Fetch-Mode).

### Sec-Fetch-User Examples

The `Sec-Fetch-User` header indicates whether the request was initiated by user interaction. This middleware can be used to reject requests that are not initiated by user interaction, thus preventing automatic scraping and bot requests.

**⚠️ Warning:** Using `Sec-Fetch-User` will also block search engine crawlers, so caution is advised when implementing it on public pages that need to be indexed.

**Protect sensitive operations from automated requests:**
```php
// Only allow requests initiated by user interaction
Route::post('user/transfer-funds', function (Request $request) {
    // Handle fund transfers
})->middleware('sec-fetch-user');
```

**Prevent automated form submissions:**
```php
// Block automated bot submissions on contact forms
Route::post('contact/submit', function (Request $request) {
    // Handle contact form submission
})->middleware('sec-fetch-user');
```

**Protect API endpoints from scraping:**
```php
// Prevent automated data harvesting
Route::get('api/user/profile', function (Request $request) {
    return response()->json(['profile' => 'data']);
})->middleware('sec-fetch-user');
```

**Combining with other middleware for enhanced security:**
```php
// Use multiple fetch metadata headers for maximum protection
Route::post('admin/critical-action', function (Request $request) {
    // Handle critical admin actions
})->middleware(['sec-fetch-site:same-origin', 'sec-fetch-mode:navigate', 'sec-fetch-user']);
```

For more information about `Sec-Fetch-User` values, see the [MDN documentation](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Sec-Fetch-User).

## Error Handling
When Sec-Fetch value is invalid, throw the `Symfony\Component\HttpKernel\Exception\BadRequestHttpException`

You can change the response in `bootstrap/app.php`.

```php
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
 
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->render(function (BadRequestHttpException $e, Request $request) {
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
