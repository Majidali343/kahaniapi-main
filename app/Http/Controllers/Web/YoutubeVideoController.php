<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\YoutubeVideo;


class YoutubeVideoController extends Controller
{
    public function index()
    {
        $videos = YoutubeVideo::all();
        return view('youtubevideos.index', compact('videos'));
    }

    public function getapi()
    {
        $videos = YoutubeVideo::all();
        if(!$videos){
           return response()->json('No vidoes found', 404);
        }

        return response()->json($videos, 200);
    }

    public function create()
    {
     return view('youtubevideos.createyoutube');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'embed_link' => 'required|string',
        ]);

        YoutubeVideo::create($request->all());

        return redirect()->route('youtubevideo.index')->with('success', 'Video added successfully!');
    }

    
    public function deletetestimonial(Request $request, $id){

        $youtubevideo = YoutubeVideo::where('id', $id)->first();
        
        // If the kahani is not found, return a response
        if (!$youtubevideo) {
            return redirect()->back()->with(['message' => 'youtubevideo not found', 'status' => false]);
        }
    
        $youtubevideo->delete();
  
        return redirect()->route('youtubevideo.index')->with('success', 'youtubevideo deleted successfully.');
  
    }


    public function youtubevideofind(Request $request,$id){

        $video = YoutubeVideo::where('id',$id)->first();
  
        if (!$video) {
          return redirect()->back()->with('message', 'YoutubeVideo not Found');
        }
  
        return view('youtubevideos.edityoutube',['youtubevideo'=> $video]);
  
    }

    public function youtubevideoUpdate(Request $request,$id){
 
        $youtubevideo= YoutubeVideo::find($id);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'embed_link' => 'nullable|string',
        ]);

        $youtubevideo->update($request->all());

        return redirect()->route('youtubevideo.index')->with('success', 'Youtube video Updated successfully!');

    }
}
