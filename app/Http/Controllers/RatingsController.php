<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rating;

class RatingsController extends Controller
{
    public function getAverageRating($kahaniId)
    {
        $averageRating = Rating::where('kahani_id', $kahaniId)->avg('rating');
        $numRating = Rating::where('kahani_id', $kahaniId)->count('rating');
        return response()->json(['average_rating' => $averageRating,'num_ratings'=> $numRating]);
    }

    public function postRating(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'kahani_id' => 'required|exists:kahanis,kahani_id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $validatedData['user_id'] = $user->id ;

        $rating = Rating::updateOrCreate(
            ['user_id' => $user->id, 'kahani_id' => $request->kahani_id],
            ['rating' => $request->rating]
        );

        return response()->json($rating, 201);
    }
}
