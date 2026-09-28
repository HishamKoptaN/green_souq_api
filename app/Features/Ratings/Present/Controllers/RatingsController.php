<?php

namespace App\Features\Ratings\Present\Controllers;
use App\Features\Users\Data\Models\User;
use App\Features\Ratings\Data\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class RatingsController
{
    public function index(Request $request)
    {
    }
    public function show($id)
    {
        return Rating::where('evaluated_id', $id)->get();
    }
    public function update($id, Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        Rating::updateOrCreate(
            [
                'evaluator_id' => $user->id,
                'evaluated_id' => $id,
            ],
            [
                'rating' => $request->rating,
                'comm2ent' => $request->comment,
            ]
        );
    }
}
