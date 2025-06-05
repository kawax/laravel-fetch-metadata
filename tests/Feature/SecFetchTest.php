<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Revolution\FetchMetadata\Middleware\SecFetchDest;
use Revolution\FetchMetadata\Middleware\SecFetchMode;
use Revolution\FetchMetadata\Middleware\SecFetchSite;
use Revolution\FetchMetadata\Middleware\SecFetchUser;
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
        $response = $this->withHeader('Sec-Fetch-Site', 'cross-site')
            ->postJson('site');

        $response->assertStatus(400);
    }

    public function test_site_multiple()
    {
        Route::post('site-multi', fn () => response()->json())->middleware([SecFetchSite::class.':same-origin,cross-site']);

        $response = $this->withHeader('Sec-Fetch-Site', 'cross-site')
            ->postJson('site-multi');

        $response->assertSuccessful();
    }

    public function test_mode_successful()
    {
        $response = $this->withHeader('Sec-Fetch-Mode', 'navigate')
            ->postJson('mode');

        $response->assertSuccessful();
    }

    public function test_mode_invalid()
    {
        $response = $this->withHeader('Sec-Fetch-Mode', 'no-cors')
            ->postJson('mode');

        $response->assertStatus(400);
    }

    public function test_dest_successful()
    {
        $response = $this->withHeader('Sec-Fetch-Dest', 'document')
            ->postJson('dest');

        $response->assertSuccessful();
    }

    public function test_dest_invalid()
    {
        $response = $this->withHeader('Sec-Fetch-Dest', 'empty')
            ->postJson('dest');

        $response->assertStatus(400);
    }

    public function test_user_successful()
    {
        $response = $this->withHeader('Sec-Fetch-User', '?1')
            ->postJson('user');

        $response->assertSuccessful();
    }

    public function test_user_invalid()
    {
        $response = $this->postJson('user');

        $response->assertStatus(400);
    }
}
