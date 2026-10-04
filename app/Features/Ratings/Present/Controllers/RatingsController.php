<?php

namespace App\Features\Ratings\Present\Controllers;

use App\Features\Ratings\Data\Models\Rating;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingsController
{
    public function index(Request $request) {}

    public function show($id, Request $request)
    {
        return Rating::where(
            'rateable_type',
            Relation::getMorphedModel(
                $request->input(
                    'rateable_type',
                ),
            ),
        )->where('rateable_id', $id)->get();
    }

    public function store(Request $request)
    {
        return Rating::updateOrCreate(
            [
                'evaluator_id' => Auth::id(),
                'rateable_id' => $request->rateable_id,
                'rateable_type' => $request->input(
                    'rateable_type',
                ),
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );
    }

    public function update($id, Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $rateableType = $request->input('rateable_type', 'user');

        return Rating::updateOrCreate(
            [
                'evaluator_id' => $user->id,
                'rateable_type' => Relation::getMorphedModel($rateableType) ?? $rateableType,
                'rateable_id' => $id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );
    }
}
