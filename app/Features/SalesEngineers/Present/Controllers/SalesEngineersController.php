<?php

namespace App\Features\SalesEngineers\Present\Controllers;

use App\Features\SalesEngineers\Present\Resources\SalesEngineerResource;
use App\Features\Users\Data\Models\User;
use App\Features\Users\Domain\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesEngineersController
{
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (! $user) {
            return failureRes('غير مصرح بالدخول', 401);
        }
        $query = $request->input('query');
        $salesEngineers = User::where(
            'user_type',
            UserType::SalesEngineer->value,
        )->with([
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
