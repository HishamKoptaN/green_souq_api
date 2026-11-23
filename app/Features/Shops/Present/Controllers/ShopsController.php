<?php

namespace App\Features\Shops\Present\Controllers;

use App\Features\Shops\Present\Resources\ShopResource;
use App\Features\Users\Data\Models\User;
use App\Features\Users\Domain\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopsController
{
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (! $user) {
            return failureRes(null, 401, 'غير مصرح بالدخول');
        }
        $query = $request->input('query');
        $shops = User::whereIn(
            'user_type',
            [UserType::Farmer->value, UserType::Shop->value],
        )->with([
            'receivedRatings' => function ($q) use ($user) {
                $q->where('evaluator_id', $user->id);
            },
        ]);
        if ($query) {
            $shops->where('name', 'like', "%{$query}%");
        }

        return paginateRes($shops->paginate(5), ShopResource::class);
    }

    public function show($id)
    {
        $user = User::with([
            'receivedRatings' => function ($q) use ($id) {
                $q->where('evaluator_id', $id);
            },
        ])->find($id);

        return successRes(
            new ShopResource($user),
        );
    }
}
