<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Facades\Storage;
use App\Models\Membership;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function index(Request $request){
        $packages = Package::all();
        if($packages->isEmpty()){
          return view('packages.index',['message'=>'No packages found']);
        }
        
        return view('packages.index',['packages'=> $packages]);
    }

    public function newpackage(Request $request){

      return view('packages.packagecreate');
    }

    public function findpackage(Request $request,$id){

      $package = Package::where('id',$id)->first();

      if (!$package) {
        return redirect()->back()->with('message', 'Package not Found');
      }

      return view('packages.editpackage',['package'=> $package]);

  }

  public function updatePackageStatus(Request $request, $id)
  {
      // Validate the request to ensure 'status' is provided
      $request->validate([
          'status' => 'required|string|max:255', // You can adjust the validation rules as needed
      ]);

      // Find the membership by ID and update the status
      $updated = DB::table('packages')
          ->where('id', $id)
          ->update(['status' => $request->input('status')]);


      if ($updated) {
          return response()->json(['message' => 'package status updated successfully.']);
      } else {
          return response()->json(['message' => 'Failed to update package status.'], 500);
      }
  }

    public function packagestore(Request $request){
   
      
      $request->validate([
        'name' => 'required|string|max:255',
        'Description' => 'required|string',
        'Permissions' => 'required|string',
        'validity' => 'required|string',
        'price' => 'required|numeric|min:0',
        'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Adjust max size as needed
    ]);

      $existingPackage = Package::where('Permissions', $request->Permissions)
      ->where('validity', $request->validity)
      ->first();

     
      if ($existingPackage) {
        return redirect()->route('packages.index')->with('message', 'Package already exists');
      }
      
  
     $imagePath = $request->file('image')->store('packages', 'public');
  

    // Create a new Package instance and save it to the database
    $package = new Package();
    $package->name = $request->input('name');
    $package->description = $request->input('Description');
    $package->permissions = $request->input('Permissions');
    $package->validity = $request->input('validity');
    $package->price = $request->input('price');
    $package->image = $imagePath;
    $package->save();

    // Redirect to a specific route with a success message
    return redirect()->route('packages.index')->with('success', 'Package created successfully.');
     
    }

    public function packageUpdate(Request $request, $id)
  {

    $package = Package::findOrFail($id);

    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'Description' => 'required|string',
        'Permissions' => 'required|string',
        'validity' => 'required|string',
        'price' => 'required|numeric|min:0',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 'nullable' allows the field to be optional
    ]);

    // Check if a package with the same 'Permissions' and 'validity' already exists, excluding the current package
    $existingPackage = Package::where('Permissions', $request->Permissions)
        ->where('validity', $request->validity)
        ->where('id', '!=', $id)
        ->first();

    if ($existingPackage) {
        return redirect()->route('packages.index')->with('message', 'Package with the same permissions and validity already exists');
    }

    // Update the package details
    $package->name = $request->input('name');
    $package->description = $request->input('Description');
    $package->permissions = $request->input('Permissions');
    $package->validity = $request->input('validity');
    $package->price = $request->input('price');

    // Check if a new image file was uploaded
    if ($request->hasFile('image')) {
        // Store the new image and delete the old one
        $imagePath = $request->file('image')->store('packages', 'public');
        
        // Delete the old image from storage if it exists
        if ($package->image) {
            Storage::disk('public')->delete($package->image);
        }

        // Update the package image path
        $package->image = $imagePath;
    }

    // Save the updated package details
    $package->save();

    // Redirect to a specific route with a success message
    return redirect()->route('packages.index')->with('success', 'Package updated successfully.');
  }


    public function deletepackage(Request $request, $id){
      $package = Package::where('id', $id)->first();
  
      $existingMembership = Membership::where('package_id', $id)->count();

      if($existingMembership > 0){
        return redirect()->back()->with('message' , 'Can not delete users are using this package');
      }
      
      // If the kahani is not found, return a response
      if (!$package) {
          return redirect()->back()->with(['message' => 'Package not found', 'status' => false]);
      }

      if ($package->image) {
          Storage::disk('public')->delete($package->image);
      }
  
      $package->delete();

      return redirect()->route('packages.index')->with('success', 'Package deleted successfully.');

  }
    
}
