<?php

namespace Modules\City\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\City\DataTables\CityDataTable;
use Modules\City\DataTables\AreaDataTable;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;

class CityController extends Controller
{
   // Display form to create a new city
    public function index()
    {
        return view('city::create');
    }

    // Store a new city
    public function create(Request $request)
    {
        $request->validate([
            'city_name' => 'required|string|max:255',
            'short_code' => ['required', 'size:3', 'regex:/^[A-Z]{3}$/'],
            // 'pincode' => ['required', 'digits:6', 'regex:/^[1-9][0-9]{5}$/'],
            'status' => 'required|boolean',
        ]);

        // Check if the city already exists
        $existingCity = City::where('city_name', $request->city_name)->first();
    
        if ($existingCity) {
            // Return error message if the city already exists
            return redirect()->back()->withErrors(['city_name' => 'This city already exists.']);
        }
        
        $existingCitycode = City::where('short_code', $request->short_code)->first();
    
        if ($existingCitycode) {
            // Return error message if the city already exists
            return redirect()->back()->withErrors(['short_code' => 'This city short code already exists.']);
        }
    
        // If the city doesn't exist, create a new one
        City::create($request->only('city_name', 'short_code','status'));
    
        return redirect()->route('admin.Green-Drive-Ev.City.list')->with('success', 'City created successfully.');
    }

    // List all cities
    public function list(CityDataTable $dataTable)
    {
        return $dataTable->render('city::list');
    }
    

    // Delete a specific city
    public function delete_city($id) // don't delete the city because this one based on work recruiters and etc.. added by Gowtham.s
    {
        $city = City::findOrFail($id);
        // $city->delete();

        return redirect()->route('admin.Green-Drive-Ev.City.list')->with('success', 'City deleted successfully.');
    }

    // Show form to edit a specific city
    public function edit_city($id)
    {
        $city = City::findOrFail($id);
        return view('city::edit', compact('city'));
    }

    // Update a specific city
    public function update(Request $request, $id)
    {
        $request->validate([
            'city_name' => 'required|string|max:255',
            'short_code' => ['required', 'size:3', 'regex:/^[A-Z]{3}$/'],
            // 'pincode' => ['required', 'digits:6', 'regex:/^[1-9][0-9]{5}$/'],
            'status' => 'required|boolean',
        ]);

        $city = City::findOrFail($id);
        
        $existingCitycode = City::where('short_code', $request->short_code)->where('id','!=',$city->id)->first();
    
        if ($existingCitycode) {
            // Return error message if the city already exists
            return redirect()->back()->withErrors(['short_code' => 'This city short code already exists.']);
        }
        
        $city->update($request->only('city_name', 'short_code','status'));

        return redirect()->route('admin.Green-Drive-Ev.City.list')->with('success', 'City updated successfully.');
    }

    // Change status of a city
    public function change_status($id, $status)
    {
        $city = City::findOrFail($id);
        $city->status = $status;
        $city->save();

        return redirect()->route('admin.Green-Drive-Ev.City.list')->with('success', 'City status updated successfully.');
    }
    
    public function area_index()
    {
        $City = City::where('status',1)->get();
        return view('city::area.create',compact('City'));
    }
    
    public function area_list(AreaDataTable $dataTable)
    {
        return $dataTable->render('city::area.list');
    }
    
    public function area_change_status($id, $status)
    {
        $city = Area::findOrFail($id);
        $city->status = $status;
        $city->save();

        return redirect()->route('admin.Green-Drive-Ev.Area.list')->with('success', 'Area status updated successfully.');
    }
    
    // Delete a specific city
    public function area_delete($id)
    {
        $city = Area::findOrFail($id);
        $city->delete();

        return redirect()->route('admin.Green-Drive-Ev.Area.list')->with('success', 'Area deleted successfully.');
    }
    
    // Show form to edit a specific city
    public function area_edit($id)
    {
        $area = Area::findOrFail($id);
         $City = City::where('status',1)->get();
        return view('city::area.edit', compact('area','City'));
    }
    
    public function area_create(Request $request)
    {
        $request->validate([
            'city_id' => 'required',  // Ensure the city exists
            'status' => 'required',  // Ensure status is boolean
            'area_name' => 'required|string|max:155' . $request->city_id  // Check uniqueness per city
        ]);
    
        // If validation passes, create the area
        Area::create([
            'Area_name' => $request->area_name,
            'city_id' => $request->city_id,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.Green-Drive-Ev.Area.list')->with('success', 'Area created successfully.');
    }

    public function area_update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'city_id' => 'required',  // Ensure the city exists
            'status' => 'required',  // Ensure status is boolean
            'area_name' => 'required|string|max:155'  // Check uniqueness per city, excluding the current area
        ]);
    
        // Find the area by its ID
        $area = Area::findOrFail($id);
    
        // Update the area
        $area->update([
            'Area_name' => $request->area_name,
            'city_id' => $request->city_id,
            'status' => $request->status,
        ]);
        
        // Redirect with success message
        return redirect()->route('admin.Green-Drive-Ev.Area.list')->with('success', 'Area updated successfully.');
    }

}
