<?php

namespace App\Features\SalesEngineers\Present\Controllers;

use App\Features\SalesEngineers\Present\Resources\SalesEngineerResource;
use App\Features\Users\Data\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesEngineersController
{
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (! $user) {
            return failureRes(null, 401, 'غير مصرح بالدخول');
        }
        $query = $request->input('query');
        $salesEngineers = User::where('user_type', 'salesEngineer')->with([
            'receivedRatings' => function ($q) use ($user) {
                $q->where('evaluator_id', $user->id);
            },
        ]);
        if ($query) {
            $salesEngineers->where('name', 'like', "%{$query}%");
        }

        return paginateRes($salesEngineers->paginate(3), SalesEngineerResource::class);
    }

    public function show(User $user)
    {
        $authEvaluator = Auth::guard('sanctum')->user();
        if ($authEvaluator) {
            $user->load([
                'receivedRatings' => function ($q) use ($authEvaluator) {
                    $q->where('evaluator_id', $authEvaluator->id);
                },
            ]);
        }

        return new SalesEngineerResource($user);
    }
}
