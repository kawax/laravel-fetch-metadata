<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Route;
use Revolution\FetchMetadata\Middleware\SecFetchDest;
use Revolution\FetchMetadata\Middleware\SecFetchMode;
use Revolution\FetchMetadata\Middleware\SecFetchSite;
use Revolution\FetchMetadata\Middleware\SecFetchUser;
use Symfony\Component\HttpKernel\Exception\InvalidMetadataException;
use Tests\TestCase;

class SecFetchTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::post('site', fn () => response()->json())->middleware(SecFetchSite::class);

        Route::post('mode', fn () => response()->json())->middleware(SecFetchMode::class);

        Route::post('dest', fn () => response()->json())->middleware(SecFetchDest::class);

        Route::post('user', fn () => response()->json())->middleware(SecFetchUser::class);
    }

    public function test_site_successful()
    {
        $response = $this->withHeader('Sec-Fetch-Site', 'same-origin')
            ->postJson('site');

        $response->assertSuccessful();
    }

    public function test_site_invalid()
    {
        Exceptions::fake();

        $response = $this->withHeader('Sec-Fetch-Site', 'cross-origin')
            ->postJson('site');

        Exceptions::assertReported(fn (InvalidMetadataException $e) => $e->getMessage() === 'Invalid Sec-Fetch-Site.');

        $response->assertStatus(500);
    }

    public function test_site_multiple()
    {
        Exceptions::fake();

        Route::post('site-multi', fn () => response()->json())->middleware([SecFetchSite::class.':same-origin,cross-origin']);

        $response = $this->withHeader('Sec-Fetch-Site', 'cross-origin')
            ->postJson('site-multi');

        Exceptions::assertNothingReported();

        $response->assertSuccessful();
    }

    public function test_mode_successful()
    {
        $response = $this->withHeader('Sec-Fetch-Mode', 'same-origin')
            ->postJson('mode');

        $response->assertSuccessful();
    }

    public function test_mode_invalid()
    {
        Exceptions::fake();

        $response = $this->withHeader('Sec-Fetch-Mode', 'cross-origin')
            ->postJson('mode');

        Exceptions::assertReported(fn (InvalidMetadataException $e) => $e->getMessage() === 'Invalid Sec-Fetch-Mode.');

        $response->assertStatus(500);
    }

    public function test_dest_successful()
    {
        $response = $this->withHeader('Sec-Fetch-Dest', 'document')
            ->postJson('dest');

        $response->assertSuccessful();
    }

    public function test_dest_invalid()
    {
        Exceptions::fake();

        $response = $this->withHeader('Sec-Fetch-Dest', 'empty')
            ->postJson('dest');

        Exceptions::assertReported(fn (InvalidMetadataException $e) => $e->getMessage() === 'Invalid Sec-Fetch-Dest.');

        $response->assertStatus(500);
    }

    public function test_user_successful()
    {
        $response = $this->withHeader('Sec-Fetch-User', '?1')
            ->postJson('user');

        $response->assertSuccessful();
    }

    public function test_user_invalid()
    {
        Exceptions::fake();

        $response = $this->postJson('user');

        Exceptions::assertReported(fn (InvalidMetadataException $e) => $e->getMessage() === 'Invalid Sec-Fetch-User.');

        $response->assertStatus(500);
    }
}
