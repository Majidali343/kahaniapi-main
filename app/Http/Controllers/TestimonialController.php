<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Testimonial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestimonialController extends Controller
{
    public function storetestimonial(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'description' => 'required|string',
        ]);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user already has a testimonial
        $testimonial = Testimonial::where('user_id', $user->id)->first();

        if ($testimonial) {
            // Update the existing testimonial
            $testimonial->update($validatedData);
        } else {
            // Add the user_id to the validated data and create a new testimonial
            $validatedData['user_id'] = $user->id;
            $testimonial = Testimonial::create($validatedData);
        }

        // Return a JSON response with the created or updated testimonial
        return response()->json($testimonial, 200);
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate the request to ensure 'status' is provided
        $request->validate([
            'status' => 'required|string|max:255', // You can adjust the validation rules as needed
        ]);

       $testimonial = Testimonial::where('id',$id)->first();

       $testimonial->testimonial_status =$request->status ;

       $testimonial->save();
       
       return response()->json(['message' => 'Status updated successfully']);
    }

    public function gettestimonials(Request $request){

        $testimonials = Testimonial::with('user')
            ->where('testimonial_status','true')
            ->orderBy('updated_at', 'DESC')
            ->get();
              
        if(!$testimonials){
         return response()->json(["error"=>"no testomionials found"], 401);
        }

        return response()->json($testimonials, 200);

      }

      public function deletetestimonial(Request $request, $id){

        $Testimonial = Testimonial::where('id', $id)->first();
        
        // If the kahani is not found, return a response
        if (!$Testimonial) {
            return redirect()->back()->with(['message' => 'testimonial not found', 'status' => false]);
        }
    
        $Testimonial->delete();
  
        return redirect()->route('testimonial.index')->with('success', 'Testimonial deleted successfully.');
  
    }

      public function index(Request $request){     
        
        $testomionials = DB::table("testimonials")
        ->join('users' , 'users.id','=' , 'testimonials.user_id')
        ->select('testimonials.*' ,'users.email','users.username' )->get();

        if(!$testomionials){
         return redirect()->back()->with('message' , 'No Testimonial Found');
        }

        return view('testmonials.index',[ 'testomionials' => $testomionials ]);

      }


}
