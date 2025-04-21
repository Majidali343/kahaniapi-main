<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;
use App\Models\Reply;

class ReviewController extends Controller
{
    public function getReviewsByKahani($kahaniId)
    {
        // $reviews = Review::where('kahani_id', $kahaniId)
        // ->where('status','active')->with('user')->get();

        $reviews = Review::with(['user', 'replies.user'])
        ->where('review_status','true')
        ->where('kahani_id',$kahaniId)->get();
        
        if ($reviews->count() > 0) {
            return response()->json($reviews);
        }
    
        return response()->json(['message' => 'No reviews found'], 404);
    }

    public function postReview(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'kahani_id' => 'required|exists:kahanis,kahani_id',
            'comment' => 'required|string',
        ]);

        // Check if the user has already reviewed this kahani
        $existingReview = Review::where('user_id', $user->id)
                                ->where('kahani_id', $validatedData['kahani_id'])
                                ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this kahani.'
            ], 409); // Conflict status code
        }

        $validatedData['user_id'] = $user->id;

        $review = Review::create($validatedData);
        return response()->json($review, 201);
    }

    public function postreply(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'comment_id' => 'required',
            'message' => 'required|string',
        ]);

        $validatedData['user_id'] = $user->id;

        $review = Reply::create($validatedData);
        return response()->json($review, 201);
    }


}
