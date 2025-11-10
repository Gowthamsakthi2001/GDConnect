<?php

namespace Modules\Clients\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Clients\DataTables\ClientDataTable;
use Modules\Clients\DataTables\ClientHubDataTable;
use Modules\Zones\Entities\Zones;
use Modules\Clients\Entities\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Modules\Clients\Entities\ClientHub;
class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $zones = Zones::where('status',1)->get();
        return view('clients::index',compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clients::create');
    }
    
    public function list(ClientDataTable $dataTable)
    {
        return $dataTable->render('clients::list');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // dd($request->all());
        // exit;
         // Get the incoming request data
        $data = $request->all();
    
        // Define validation rules
        $rules = [
            'client_name' => 'required|string|max:255',
            'client_zone' => 'required|integer',
            'client_location' => 'required|string|max:255',
            // 'hub_name' => 'required|string|max:255',
            'client_coordinate'=>''
        ];
    
        // Validate the data
        $validator = Validator::make($data, $rules);
    
        // Check if validation fails
        if ($validator->fails()) {
            // Validation failed, redirect back with errors
            return back()
                ->withErrors($validator)   // Include validation errors
                ->withInput();             // Keep the input values in the form
        }
        // Decode the JSON string to get the coordinates
        $zoneCoordinates = json_decode($request->input('client_coordinate'), true);
    
        if (!is_array($zoneCoordinates) || empty($zoneCoordinates)) {
            return redirect()->back()->with('error', 'Invalid zone coordinates.');
        }
    
        // Ensure the polygon is closed
        if ($zoneCoordinates[0] !== end($zoneCoordinates)) {
            $zoneCoordinates[] = $zoneCoordinates[0];
        }
    
        // Convert coordinates to WKT POLYGON format
        $wkt = 'POLYGON((' . implode(',', array_map(function ($coord) {
            return "{$coord['lng']} {$coord['lat']}";
        }, $zoneCoordinates)) . '))';

        // If validation passes, save the client
        $client = new Client();
        $client->client_name = $data['client_name'];
        $client->client_zone = $data['client_zone'];
        $client->client_location = $data['client_location'];
        $client->hub_name = $data['hub_name'] ?? null;
        $client->client_coordinate = DB::raw("ST_GeomFromText('$wkt')");
        $client->save();
    
        // Redirect back to the previous page with a success message
        return redirect()->route('admin.Green-Drive-Ev.clients.list')
                     ->with('success', 'Area created successfully!');
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('clients::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit_client($id)
    {
        try {
             $zones = Zones::where('status', 1)->get();
            // Find the delivery man by ID
            $Client = Client::find($id);
            
            // Check if the delivery man exists
            if (!$Client) {
                return redirect()->route('admin.Green-Drive-Ev.clients.list')
                                 ->withToastrError('client not found.');
            }
            
             // Fetch the zone details by ID
        $zones1 = DB::table('ev_tbl_clients')
            ->select('id', DB::raw('ST_AsText(client_coordinate) as coordinates'))
            ->where('id', $id)
            ->first();
        // Convert WKT string to array of coordinates
        $formattedCoordinates = [];
        if(isset($zones1->coordinates) && $zones1->coordinates != null){
           
                if ($zones1 && $zones1->coordinates) {
                    // Example of WKT string: "POLYGON((lat1 lng1, lat2 lng2, ...))"
                    $coordinatesString = str_replace(['POLYGON((', '))', 'MULTIPOINT(', ')'], '', $zones1->coordinates);
                    $points = explode(',', $coordinatesString);
            
                    foreach ($points as $point) {
                        $latLng = explode(' ', trim($point));
                        if (count($latLng) === 2) {
                            $formattedCoordinates[] = [
                                "lat" => (float)$latLng[1], // Latitude
                                "lng" => (float)$latLng[0]  // Longitude
                            ];
                        }
                    }
                }
            
                // Convert formatted coordinates to JSON
                $json = json_encode($formattedCoordinates, JSON_PRETTY_PRINT);
        }else{
            $json = json_encode($formattedCoordinates, JSON_PRETTY_PRINT);
        }
        
    
            // Return the view with necessary data
            return view('clients::edit', compact('Client','zones','json'));
        } catch (Exception $e) {
            // Handle the error and show a toastr error message
            return back()->withToastrError('An error occurred while editing the clients: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Find the client by ID
        $client = Client::findOrFail($id);
    
        // Define validation rules
        $request->validate([
            'client_name' => 'required|string|max:255',
            'client_zone' => 'required|integer',
            'client_location' => 'required|string|max:255',
            // 'hub_name' => 'required|string|max:255',
        ]);
        
         // Decode the JSON string to get the coordinates
        $zoneCoordinates = json_decode($request->input('client_coordinate'), true);
    
        if (!is_array($zoneCoordinates) || empty($zoneCoordinates)) {
            return redirect()->back()->with('error', 'Invalid zone coordinates.');
        }
    
        // Ensure the polygon is closed
        if ($zoneCoordinates[0] !== end($zoneCoordinates)) {
            $zoneCoordinates[] = $zoneCoordinates[0];
        }
    
        // Convert coordinates to WKT POLYGON format
        $wkt = 'POLYGON((' . implode(',', array_map(function ($coord) {
            return "{$coord['lng']} {$coord['lat']}";
        }, $zoneCoordinates)) . '))';
    
        // Update the client with the new data
        $client->client_name = $request->client_name;
        $client->client_zone = $request->client_zone;
        $client->client_location = $request->client_location;
        $client->hub_name = $request->hub_name ?? null;
        $client->client_coordinate = DB::raw("ST_GeomFromText('$wkt')");
        $client->save();
    
        // Redirect with a success message
        return redirect()->route('admin.Green-Drive-Ev.clients.list')
                         ->with('success', 'Client updated successfully!');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function delete_client($id)
    {
         try {
            // Find the dm by its ID
           $dm = Client::findOrFail($id);
            // Delete the dm
           $dm->delete();
           
          
            return back()->with('Client removed Successfully');
        } catch (Exception $e) {
             // Handle the error using Toastr
            return back()->with('An error occurred while loading dm: ' . $e->getMessage());
        }
    }
    
     public function hub_create()
    {
        $clients = Client::all();
        return view('clients::hub_create',compact('clients'));
    }
    
     public function hub_store(Request $request)
    {
        $data = $request->all();
        $rules = [
            'client_id' => 'required|exists:ev_tbl_clients,id',
            'hub_name' => 'required|array',       
            'hub_name.*' => 'string|min:1',       
        ];
        $validator = Validator::make($data, $rules);
    
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }
    
        $client_id = $request->client_id;
        $hubs = $request->hub_name;
    
        foreach($hubs as $val) {
            ClientHub::create([
                'client_id' => $client_id,
                'hub_name' => $val
            ]);
        }
    
        return redirect()->route('admin.Green-Drive-Ev.clients.hub.list')
                         ->with('success', 'Client Hubs created successfully!');
    }
    
    public function hub_list(ClientHubDataTable $dataTable)
    {
        $clients = Client::all();
        return $dataTable->render('clients::hub_list',compact('clients'));
    }


    public function hub_change_status($id, $status)
    {
        $city = ClientHub::findOrFail($id);
        $city->status = $status;
        $city->save();

        return redirect()->route('admin.Green-Drive-Ev.clients.hub.list')->with('success', 'Client Hub status updated successfully.');
    }
     public function delete_client_hub($id)
    {
        try {
            $hub = ClientHub::findOrFail($id);
            $hub->delete();
    
            $message = 'Client Hub deleted successfully';
            return response()->json(['success' => true, 'message' => $message],200);
            
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while loading dm: ' . $e->getMessage()]);
        }
    }
    
    public function get_client_hub(Request $request,$id){
        $get_hub = ClientHub::where('id', $id)->first();
        if(!$get_hub){
            return response()->json(['status'=>false,'message'=>'client hub not found'],200);
        }
        return response()->json(['status'=>true,'message'=>'client hub fetched successfully','data'=>$get_hub],200);
    }
    
    public function client_hub_update(Request $request)
    {
        $data = $request->all();
        $rules = [
            'client_id' => 'required|exists:ev_tbl_clients,id',
            'hub_name' => 'required',       
        ];
        $validator = Validator::make($data, $rules);
    
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $id = $request->hub_id;
        $hub = ClientHub::where('id',$id)->first();
        if(!$hub){
             return redirect()->route('admin.Green-Drive-Ev.clients.hub.list')
                  ->with('error', 'client hub not found!');
        }
        $update_data = [
          'client_id'=>$request->client_id,
          'hub_name'=>$request->hub_name
        ];
        $hub->update($update_data);
        return redirect()->route('admin.Green-Drive-Ev.clients.hub.list')
                         ->with('success', 'Client Hub updated successfully!');
    }
}
