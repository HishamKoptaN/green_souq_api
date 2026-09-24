<?php

namespace App\Features\Shops\Present\Controllers;
use App\Features\Users\Data\Models\User;
use App\Features\Shops\Present\Resources\ShopResource;
use Illuminate\Http\Request;

class ShopsController
{
    public function index(Request $request)
    {
        return paginateRes(User::where('user_type', 'shop')->paginate(10), ShopResource::class);
    }
    public function show(User $user)
    {
        return new ShopResource($user);
    }
}
