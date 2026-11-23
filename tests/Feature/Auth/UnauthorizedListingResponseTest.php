<?php

namespace Tests\Feature\Auth;

use App\Features\SalesEngineers\Present\Controllers\SalesEngineersController;
use App\Features\Shops\Present\Controllers\ShopsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class UnauthorizedListingResponseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $guard = Mockery::mock();
        $guard->shouldReceive('user')->andReturnNull();
        Auth::shouldReceive('guard')->with('sanctum')->andReturn($guard);
    }

    public function test_shops_listing_exposes_unauthorized_reason(): void
    {
        $response = (new ShopsController)->index(Request::create('/api/user/shops'));

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('غير مصرح بالدخول', $response->getData(true)['message']);
        $this->assertNull($response->getData(true)['errors']);
    }

    public function test_sales_engineers_listing_exposes_unauthorized_reason(): void
    {
        $response = (new SalesEngineersController)->index(Request::create('/api/user/sales-engineers'));

        $this->assertSame(401, $response->getStatusCode());
        $this->assertSame('غير مصرح بالدخول', $response->getData(true)['message']);
        $this->assertNull($response->getData(true)['errors']);
    }
}
