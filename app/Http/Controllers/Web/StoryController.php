<?php

namespace App\Http\Controllers\Web;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Kahani;
use App\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoryController extends Controller
{

    public function allkahanis(Request $request){
        $Kahanis = Kahani::select(
            'kahanis.kahani_id', 
            'kahanis.title', 
            'kahanis.duration', 
            'kahanis.free', 
            'kahanis.views', 
            'kahanis.pg', 
            'kahanis.video', 
            'kahanis.audio', 
            'kahanis.thumbnail', 
            'kahanis.image', 
            'kahanis.created_at',
            DB::raw('COUNT(DISTINCT ratings.id) as number_of_ratings'), 
            DB::raw('COUNT(DISTINCT reviews.id) as number_of_reviews'), 
            DB::raw('AVG(ratings.rating) as average_rating')
        )
        ->leftJoin('ratings', 'kahanis.kahani_id', '=', 'ratings.kahani_id')
        ->leftJoin('reviews', 'kahanis.kahani_id', '=', 'reviews.kahani_id')
        ->groupBy(
            'kahanis.kahani_id', 
            'kahanis.title', 
            'kahanis.duration', 
            'kahanis.free', 
            'kahanis.views', 
            'kahanis.pg', 
            'kahanis.audio', 
            'kahanis.image', 
            'kahanis.thumbnail', 
            'kahanis.video', 
            'kahanis.created_at'
        )
        ->orderBy('kahanis.created_at', 'ASC')->get();
    

    // Check if the users collection is empty
    if ($Kahanis->isEmpty()) {
        return view('kahanis.index', ['message' => 'No Kahani found']);
    }

    // Pass the users collection to the view
    return view('kahanis.index', ['kahanis' => $Kahanis]);
    }

    public function updateReviewStatus(Request $request, $id)
    {
  
    $request->validate([
        'status' => 'required|string|max:255', // You can adjust the validation rules as needed
    ]);

    // Find the membership by ID and update the status
    $updated = DB::table('reviews')
        ->where('id', $id)
        ->update(['review_status' => $request->input('status')]);
   
        if ($updated) {
            return response()->json(['message' => 'review status updated successfully.']);
        } else {
            return response()->json(['message' => 'Failed to update review status.'], 500);
        }
    }


    public function newkahani(){
        return view('kahanis.createkahani');
    }
    
    public function allreviews(Request $request,$id){

        $reviews = Review::where('kahani_id',$id)->
        join('users','reviews.user_id','=' ,'users.id')->
        select('reviews.*','users.username','users.email')->orderBy('reviews.created_at', 'ASC')->get();

        if (!$reviews) {
            return response()->json(['message' => 'Kahani not found', 'status' => false]);
        }

        return view('kahanis.reviews', ['reviews'=> $reviews]);

    }

    public function findkahani(Request $request,$id){

        $kahani = Kahani::where('kahani_id',$id)->first();

        if (!$kahani) {
            return response()->json(['message' => 'Kahani not found', 'status' => false]);
        }

        return view('kahanis.updatekahani',['kahani'=> $kahani]);

    }
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'Duration' => 'required|string',
            'pg' => 'required|string',
            'audio' => 'required|file|mimes:mp3,wav,mp4',
            'video' => 'file|mimes:mp4,avi,mov,wmv,mkv|max:102400',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
            'thumbnail' => 'file|mimes:jpeg,png,jpg,gif,svg',
        ]);
        $kahani = new Kahani();
        // Handle audio file upload
        $audioPath = $request->file('audio')->store('audio', 'public');
    
        // Handle image file upload
        $imagePath = $request->file('image')->store('images', 'public');
        if($request->file('thumbnail')){

            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $kahani->thumbnail = $thumbnailPath;
        }
        if($request->file('video')){
        $videoPath = $request->file('video')->store('videos', 'public');
        $kahani->video = $videoPath;
        }

        
        $kahani->title = $validatedData['title'];
        $kahani->description = $validatedData['description'];
        $kahani->Duration = $validatedData['Duration'];
        $kahani->views = 0;
        $kahani->pg = $validatedData['pg'];
        $kahani->audio = $audioPath;
        $kahani->image = $imagePath;
       
        
        $kahani->free = 0;
    
        $kahani->save();
    
        return redirect()->route('addnew.story')->with('success', 'Kahani created successfully.');
    }
    
    public function deletekahani(Request $request, $id){
        $kahani = Kahani::where('kahani_id', $id)->first();
    
        // If the kahani is not found, return a response
        if (!$kahani) {
            return response()->json(['message' => 'Kahani not found', 'status' => false]);
        }

        if ($kahani->image) {
            Storage::disk('public')->delete($kahani->image);
        }

        if ($kahani->audio) {
            Storage::disk('public')->delete($kahani->audio);
        }
    
        $kahani->delete();

        return redirect()->route('allstories')->with('success', 'Kahani deleted successfully.');

    }
    public function deletereview(Request $request, $id)
    {
        $review = Review::find($id);
    
        // If the review is not found, return a response
        if (!$review) {
            return redirect()->back()->with('error', 'Review not found.');
        }
    
        $review->delete();
    
        return redirect()->back()->with('success', 'Review deleted successfully.');
    }

    public function updatekahani(Request $request, $id)
    {
        // Find the kahani by kahani_id
        $kahani = Kahani::where('kahani_id', $id)->first();
    
      
        // If the kahani is not found, return a response
        if (!$kahani) {
            return response()->json(['message' => 'Kahani not found', 'status' => false]);
        }
       
        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',  // Use nullable if optional
            'description' => 'nullable|string',    // Use nullable if optional
            'Duration' => 'nullable|string',       // Use nullable if optional
            'pg' => 'nullable|string',       // Use nullable if optional
            'free' => 'nullable|boolean',          // Use nullable if optional
            'audio' => 'nullable|file|mimes:mp3,wav,mp4',  // Use nullable if optional
            'video' => 'nullable|file|mimes:mp4,avi,mov,wmv,mkv|max:102400', // Video is optional, so use nullable
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',  // Use nullable if optional
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',  // Use nullable if optional
        ]);
      
      
        // Handle audio file upload
        if ($request->hasFile('audio')) {
            // Delete the old audio file if it exists
            if ($kahani->audio) {
                Storage::disk('public')->delete($kahani->audio);
            }
            $audioPath = $request->file('audio')->store('audio', 'public');
            $kahani->audio = $audioPath;
            

        }
    
        // Handle image file upload
        if ($request->hasFile('image')) {
            // Delete the old image file if it exists
            if ($kahani->image) {
                Storage::disk('public')->delete($kahani->image);
            }

            $imagePath = $request->file('image')->store('images', 'public');
            $kahani->image = $imagePath;
        }

      
        if ($request->hasFile('thumbnail')) {
           
            // Delete the old image file if it exists
            if ($kahani->thumbnail) {
                Storage::disk('public')->delete($kahani->thumbnail);
            }

            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $kahani->thumbnail = $thumbnailPath;
        }

        if ($request->hasFile('video')) {
            // Delete the old image file if it exists
            if ($kahani->video) {
                Storage::disk('public')->delete($kahani->video);
            }

            $videoPath = $request->file('video')->store('videos', 'public');
            $kahani->video = $videoPath;
        }
    
        // Update the other fields
        if (isset($validatedData['pg'])) {
            $kahani->pg = $validatedData['pg'];
        }
        if (isset($validatedData['title'])) {
            $kahani->title = $validatedData['title'];
        }
        if (isset($validatedData['description'])) {
            $kahani->description = $validatedData['description'];
        }
        if (isset($validatedData['Duration'])) {
            $kahani->Duration = $validatedData['Duration'];
        }
        if (isset($validatedData['free'])) {
            $kahani->free = $validatedData['free'];
        }
        if (isset($validatedData['views'])) {
            $kahani->views = $validatedData['views'];
        }
    
        // Save the updated kahani
        $kahani->save();
    
        // Return a success response
        return redirect()->route('allstories')->with('success', 'Kahani created successfully.');
    }
}
