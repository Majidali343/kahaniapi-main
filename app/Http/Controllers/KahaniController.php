<?php

namespace App\Http\Controllers;

use App\Models\Kahani;
use App\Models\Favourite;
use App\Models\Viewcheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class KahaniController extends Controller
{


    public function getkahanis(Request $request)
    {


        $kahanis = Kahani::select(
            'kahanis.kahani_id',
            'kahanis.title',
            'kahanis.duration',
            'kahanis.free',
            'kahanis.views',
            'kahanis.description',
            'kahanis.pg',
            'kahanis.audio',
            'kahanis.thumbnail',
            'kahanis.video',
            'kahanis.image',
            'kahanis.created_at',
            DB::raw('COUNT(ratings.id) as number_of_ratings'),
            DB::raw('AVG(ratings.rating) as average_rating')
        )
            ->leftJoin('ratings', 'kahanis.kahani_id', '=', 'ratings.kahani_id')
            ->groupBy(
                'kahanis.kahani_id',
                'kahanis.title',
                'kahanis.duration',
                'kahanis.free',
                'kahanis.pg',
                'kahanis.views',
                'kahanis.description',
                'kahanis.audio',
                'kahanis.video',
                'kahanis.image',
                'kahanis.thumbnail',
                'kahanis.created_at',
            )
            ->get();

        if ($kahanis) {
            return  response()->json(['data' => $kahanis, 'message' => "kahanis get sucessfully", 'status' => true]);
        }
        return  response()->json(['message' => "kahanis not found", 'status' => false]);
    }

    public function getFavouriteKahanis(Request $request)
    {
        $user = Auth::user();

        $kahanis = Kahani::select(
            'kahanis.kahani_id',
            'kahanis.title',
            'kahanis.duration',
            'kahanis.free',
            'kahanis.views',
            'kahanis.description',
            'kahanis.pg',
            'kahanis.audio',
            'kahanis.video',
            'kahanis.image',
            'kahanis.created_at',
            'kahanis.thumbnail',
            DB::raw('COUNT(ratings.id) as number_of_ratings'),
            DB::raw('AVG(ratings.rating) as average_rating')
        )
            ->leftJoin('ratings', 'kahanis.kahani_id', '=', 'ratings.kahani_id')
            ->join('favourites', function ($join) use ($user) {
                $join->on('kahanis.kahani_id', '=', 'favourites.kahani_id')
                    ->where('favourites.user_id', '=', $user->id);
            })
            ->groupBy(
                'kahanis.kahani_id',
                'kahanis.title',
                'kahanis.duration',
                'kahanis.free',
                'kahanis.thumbnail',
                'kahanis.views',
                'kahanis.description',
                'kahanis.pg',
                'kahanis.audio',
                'kahanis.video',
                'kahanis.image',
                'kahanis.created_at'
            )
            ->get();

        if ($kahanis->isNotEmpty()) {
            return response()->json([
                'data' => $kahanis,
                'message' => "Favourite kahanis retrieved successfully",
                'status' => true
            ]);
        }

        return response()->json([
            'message' => "No favourite kahanis found",
            'status' => false
        ]);
    }


    public function getfamouskahanis(Request $request)
    {
        // Fetch 12 Kahanis with the highest views
        $kahanis =  Kahani::select(
            'kahanis.kahani_id',
            'kahanis.title',
            'kahanis.duration',
            'kahanis.free',
            'kahanis.views',
            'kahanis.description',
            'kahanis.pg',
            'kahanis.audio',
            'kahanis.video',
            'kahanis.image',
            'kahanis.created_at',
            'kahanis.thumbnail',
            DB::raw('COUNT(ratings.id) as number_of_ratings'),
            DB::raw('AVG(ratings.rating) as average_rating')
        )
            ->leftJoin('ratings', 'kahanis.kahani_id', '=', 'ratings.kahani_id')
            ->groupBy(
                'kahanis.kahani_id',
                'kahanis.title',
                'kahanis.duration',
                'kahanis.free',
                'kahanis.thumbnail',
                'kahanis.views',
                'kahanis.description',
                'kahanis.pg',
                'kahanis.audio',
                'kahanis.video',
                'kahanis.image',
                'kahanis.created_at',
            )
            ->orderBy('kahanis.views', 'desc')
            ->get();

        if ($kahanis->isNotEmpty()) {
            return response()->json(['data' => $kahanis, 'message' => "Kahanis retrieved successfully", 'status' => true]);
        }
        return response()->json(['message' => "Kahanis not found", 'status' => false]);
    }

    public function freekahani(Request $request)
    {

        $kahani = Kahani::where('free', 1)->first();

        $kahani->views = $kahani->views + 1;

        $kahani->save();

        if ($kahani) {
            return  response()->json(['data' => $kahani, 'message' => "free kahani get sucessfully", 'status' => true]);
        }
        return  response()->json(['message' => "free kahani not found", 'status' => false]);
    }

    public function getsinglekahani($id)
    {

        $singlekahani =  Kahani::select(
            'kahanis.kahani_id',
            'kahanis.title',
            'kahanis.duration',
            'kahanis.description',
            'kahanis.free',
            'kahanis.views',
            'kahanis.pg',
            'kahanis.audio',
            'kahanis.image',
            'kahanis.video',
            'kahanis.created_at',
            'kahanis.thumbnail',
            DB::raw('COUNT(ratings.id) as number_of_ratings'),
            DB::raw('AVG(ratings.rating) as average_rating')
        )
            ->leftJoin('ratings', 'kahanis.kahani_id', '=', 'ratings.kahani_id')
            ->groupBy(
                'kahanis.kahani_id',
                'kahanis.title',
                'kahanis.duration',
                'kahanis.description',
                'kahanis.free',
                'kahanis.views',
                'kahanis.pg',
                'kahanis.audio',
                'kahanis.image',
                'kahanis.video',
                'kahanis.created_at',
                'kahanis.thumbnail',
            )
            ->where('kahanis.kahani_id', $id)->first();


        if ($singlekahani) {
            return  response()->json(['data' => $singlekahani, 'message' => "kahani get sucessfully", 'status' => true]);
        }

        return  response()->json(['message' => "kahani not found", 'status' => false]);
    }

    public function addviewkahani(Request $request, $id)
    {

        $user = Auth::user();
        if (!$id) {
            return  response()->json(['message' => "kahani id missing", 'status' => false]);
        }

            Viewcheck::create([
                'kahani_id' => $id,
                'user_id' => $user->id,
            ]);

            $kahani = Kahani::where('kahani_id', $id)->first();


            if (!$kahani) {
                return  response()->json(['message' => "kahani not found", 'status' => false]);
            }


            $kahani->views = $kahani->views + 1;


            $kahani->save();

            return response()->json(['message' => "Kahani views updated successfully", 'status' => true]);
        
    }

    public function addFavourite(Request $request)
    {
        $user = Auth::user();

        // Validate incoming request
        $request->validate([
            'kahani_id' => 'required|integer',
        ]);

        // Check if the favourite already exists
        $existingFavourite = Favourite::where('user_id', $user->id)
            ->where('kahani_id', $request->input('kahani_id'))
            ->first();

        if ($existingFavourite) {

            $existingFavourite->delete();

            return response()->json([
                'message' => 'Kahani has been removed from your favourites'
            ], 409); // 409 Conflict status code
        }

        // Create a new favourite entry
        $favourite = new Favourite();
        $favourite->user_id =  $user->id;
        $favourite->kahani_id = $request->input('kahani_id');
        $favourite->save();

        return response()->json([
            'message' => 'Favourite added successfully',
            'data' => $favourite
        ], 201);
    }
    public function isfavourite(Request $request, $id)
    {
        $user = Auth::user();

        // if ($id) {
        //     return response()->json([
        //         'message' => 'please prvoide the kahani Id'
        //     ], 409); // 409 Conflict status code
        // }

        // Check if the favourite already exists
        $existingFavourite = Favourite::where('user_id', $user->id)
            ->where('kahani_id', $id)
            ->first();

        if (!$existingFavourite) {
            return response()->json([
                'message' => 'you did not liked this kahani.',
                'liked' => false

            ], 401); // 409 Conflict status code
        }

        return response()->json([
            'message' => 'you liked this kahani.',
            'liked' => true

        ], 200);
    }
}
