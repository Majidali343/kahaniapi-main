<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Partner;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::all();
        return view('partner.index', compact('partners'));
    }

    public function getapi()
    {
        $partners = Partner::all();
        if(!$partners){
           return response()->json('No partners found', 404);
        }

        return response()->json($partners, 200);
    }

    public function create()
    {
     return view('partner.createpartner');
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'logo' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $logoPath = $request->file('logo')->store('logos', 'public');

        $partner = new Partner();
        $partner->title = $validatedData['title'];
        $partner->logo =  $logoPath ;

        $partner->save();

        return redirect()->route('partner.index')->with('success', 'partner added successfully!');
    }

    
    public function deletetestimonial(Request $request, $id){

        $youtubevideo = Partner::where('id', $id)->first();
        
        // If the kahani is not found, return a response
        if (!$youtubevideo) {
            return redirect()->back()->with(['message' => 'Partner not found', 'status' => false]);
        }
    
        $youtubevideo->delete();
  
        return redirect()->route('partner.index')->with('success', 'Partner deleted successfully.');
  
    }


    public function youtubevideofind(Request $request,$id){

        $video = Partner::where('id',$id)->first();
  
        if (!$video) {
          return redirect()->back()->with('message', 'YoutubeVideo not Found');
        }
  
        return view('partner.editpartner',['youtubevideo'=> $video]);
  
    }

    public function youtubevideoUpdate(Request $request,$id){
 
        $youtubevideo= Partner::find($id);

        $validatedData = $request->validate([
            'title' => 'nullable|string|max:255',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if (isset($validatedData['title'])) {
            $youtubevideo->title = $validatedData['title'];
        }
        if ($request->hasFile('logo')) {
            // Delete the old image file if it exists
            if ($youtubevideo->logo) {
                Storage::disk('public')->delete($youtubevideo->logo);
            }

            $imagePath = $request->file('logo')->store('logos','public');
            $youtubevideo->logo = $imagePath;
        }

        $youtubevideo->save();

        return redirect()->route('partner.index')->with('success', 'Partner Updated successfully!');

    }
}
