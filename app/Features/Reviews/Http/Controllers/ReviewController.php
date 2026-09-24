<?php

namespace App\Features\Reviews\Http\Controllers;

use Illuminate\Http\Request;
use App\Features\Reviews\Models\Review;

class ReviewController
{
    public function store(Request $request)
    {
        $request->validate([
            'reviewee_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);
        Review::updateOrCreate(
            [
                'reviewer_id' => auth()->id(),
                'reviewee_id' => $request->reviewee_id
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        return successRes(['message' => 'تم التقييم بنجاح'], 200);
    }
}
