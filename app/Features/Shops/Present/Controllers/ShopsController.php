<?php

namespace App\Features\Shops\Present\Controllers;
use App\Features\Users\Data\Models\User;
use App\Features\Shops\Present\Resources\ShopResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopsController
{
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return failureRes(null, 401, 'غير مصرح بالدخول');
        }
        $query = $request->input('query');
        $shops = User::whereIn('user_type', ['farmer', 'shop'])->with([
            'receivedRatings' => function ($q) use ($user) {
                $q->where('evaluator_id', $user->id);
            }
        ]);
        if ($query) {
            $shops->where('name', 'like', "%{$query}%");
        }
        return paginateRes($shops->paginate(3), ShopResource::class);
    }
    public function show(User $user)
    {
        $authEvaluator = Auth::guard('sanctum')->user();
        if ($authEvaluator) {
            $user->load([
                'receivedRatings' => function ($q) use ($authEvaluator) {
                    $q->where('evaluator_id', $authEvaluator->id);
                }
            ]);
        }
        return new ShopResource($user);
    }
}
