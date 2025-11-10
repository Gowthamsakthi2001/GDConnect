<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Leads\Entities\leads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\LeadSource\Entities\LeadSource;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Exports\TelecallerExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LeadImport;
use Illuminate\Support\Str;


class LeadsController extends Controller
{
    /**
     * Display a listing of the leads.
     */
    public function index()
    {
        $list = leads::orderBy('id','desc')->get();
        // $telecaller = DB::table('users')->get()->keyBy('id')->toArray();
        $telecaller = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id') 
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->where('roles.name', 'Telecaller')
        ->select('users.*') 
        ->get();
        $leadsource = LeadSource::where('status', 1)->get();
        $City = City::where('status', 1)->get();
        
        
       $approve_users = \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->join('users', 'model_has_roles.model_id', '=', 'users.id')
        ->select('users.id as user_id', 'users.name as user_name')
        ->whereIn('model_has_roles.role_id', ['1', '4'])
        ->where('users.status', 'Active')
        ->get();
        $login_user_id = auth()->id();
        $get_approve_ids = $approve_users->pluck('user_id')->toArray(); 
        return view('leads::index', compact('list', 'telecaller', 'leadsource', 'City','get_approve_ids','login_user_id'));
    }
        public function lead_dev_index()
    {
        $list = leads::orderBy('id','desc')->get();
        // $telecaller = DB::table('users')->get()->keyBy('id')->toArray();
        $telecaller = DB::table('users')
        ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id') 
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->where('roles.name', 'Telecaller')
        ->select('users.*') 
        ->get();
        $leadsource = LeadSource::where('status', 1)->get();
        $City = City::where('status', 1)->get();
        
        
       $approve_users = \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->join('users', 'model_has_roles.model_id', '=', 'users.id')
        ->select('users.id as user_id', 'users.name as user_name')
        ->whereIn('model_has_roles.role_id', ['1', '4'])
        ->where('users.status', 'Active')
        ->get();
        $login_user_id = auth()->id();
        $get_approve_ids = $approve_users->pluck('user_id')->toArray(); 
        return view('leads::lead_dev', compact('list', 'telecaller', 'leadsource', 'City','get_approve_ids','login_user_id'));
    }
    
    public function fetchLeads(Request $request)
    {
        $status = $request->input('status');
        $offset = $request->input('offset');
        $limit = $request->input('limit');
    
        $leads = DB::table('ev_tbl_leads')
            ->where('telecaller_status', $status)
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->map(function($lead) {
                $image = DB::table('users')->where('id', $lead->assigned)->first();
                $image_data_uri = $image && $image->profile_photo_path ? asset('uploads/users/'.$image->profile_photo_path) : null;
                $caller_name = $image && $image->name ? $image->name : null;
    
                $source = DB::table('ev_tbl_lead_source')->where('id', $lead->source)->first();
                $source_name = $source ? $source->source_name : 'Twitter';
    
                $telecaller_comments = DB::table('telecaller_comments')->where('task_id', $lead->id)->count();
    
                return [
                    'id' => $lead->id,
                    'f_name' => ucfirst($lead->f_name),
                    'l_name' => ucfirst($lead->l_name),
                    'phone_number' => $lead->phone_number,
                    'last_update' => \Carbon\Carbon::parse($lead->created_at)->format('d M, Y H:i'),
                    'image_data_uri' => $image_data_uri,
                    'caller_name' => $caller_name,
                    'source_name' => $source_name,
                    'comments_count' => $telecaller_comments,
                    'color' => '#03a9f4'  // Update with dynamic color if needed
                ];
            });
    dd($leads);
        return response()->json($leads);
    }




    /**
     * Store a newly created lead in storage.
     */
    public function store(Request $request)
    {
      
     $validator = Validator::make($request->all(), [
        'tele_status' => 'required',
        'Source' => 'required',
        // 'Assigned' => 'required',
        'fname' => 'required|string|max:255',
        'lname' => 'required|string|max:255',
        'mobile' => 'required',
        'current_city' => 'required',
        'Interested_city' => 'required',
    ], [
        'tele_status.required' => 'Telecaller Status field is required.',
        'Source.required' => 'Source field is required.',
        // 'Assigned.required' => 'Assigned field is required.',
        'fname.required' => 'First name field is required.',
        'fname.string' => 'First name must be a string.',
        'fname.max' => 'First name may not be greater than 255 characters.',
        'lname.required' => 'Last name field is required.',
        'lname.string' => 'Last name must be a string.',
        'lname.max' => 'Last name may not be greater than 255 characters.',
        'mobile.required' => 'Mobile field is required.',
        'current_city.required' => 'Current city field is required.',
        'Interested_city.required' => 'Interested city field is required.',
    ]);
    
    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }


        // Create a new lead
        leads::create([
            'telecaller_status' => $request->tele_status ?? null,
            'source' => $request->Source ?? null,
            'assigned' => $request->Assigned ?? null,
            'f_name' => $request->fname ?? null,
            'l_name' => $request->lname ?? null,
            'phone_number' => $request->mobile ?? null,
            'current_city' => $request->current_city ?? null,
            'intrested_city' => $request->Interested_city ?? null,
            'vehicle_type' => $request->vehicle_type ?? null,
            'lead_sources' => $request->lead_source ?? null,
            'register_date' => \Carbon\Carbon::now(),
            'active_status' => $request->status,
            'task' => $request->task,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Lead created successfully.');
    }
    public function lead_import_verify(Request $request)
    {
        $leadsource = LeadSource::where('status', 1)->get();
        $cities = City::where('status', 1)->get();
        $telecallers = \Illuminate\Support\Facades\DB::table('model_has_roles')
        ->join('users', 'model_has_roles.model_id', '=', 'users.id')
        ->select('users.id as id', 'users.name as telecaller_name')
        ->where('model_has_roles.role_id', 3)
        ->where('users.status', 'Active')
        ->get();
        // ->map(fn($item) => ['telecaller_name' => $item->telecaller_name, 'id' => $item->id])
        // ->toArray();
        
        return view('leads::lead_import_verify',compact('leadsource','cities','telecallers'));
    }

    /**
     * Show the form for editing a lead.
     */
    public function edit($id)
    {
        $lead = leads::findOrFail($id);
        return view('leads::edit', compact('lead'));
    }

    /**
     * Update the specified lead in storage.
     */
  
     public function update(Request $request)
    {
        
        $update = leads::where('id',$request->task_id)->update(['telecaller_status'=> $request->tele_status]);
        
            return response()->json([
                'message' => 'status updated'
            ], 200); 
      
        
    } 
    /**
     * Remove the specified lead from storage.
     */
    public function destroy($id)
    {
        leads::destroy($id);

        return back()->with('success', 'Lead deleted successfully.');
    }
    
    // public function downloadExcel()
    // {
    //     // Define the filename for the exported file
    //     $fileName = 'telecaller_data.csv';

    //     // Prepare the data
    //     $data = leads::get(); // Replace with your table name

    //     // Create a Streamed Response
    //     $response = new StreamedResponse(function () use ($data) {
    //         // Open output stream
    //         $handle = fopen('php://output', 'w');

    //         // Add the header row
    //         fputcsv($handle, [
    //             'telecaller_status', 'source', 'Telecaller_id', 'f_name', 'l_name',
    //             'phone_number', 'current_city', 'intrested_city', 'vehicle_type',
    //             'lead_sources', 'register_date', 'active_status', 'task', 'description'
    //         ]);

    //         // Add the data rows
    //         // foreach ($data as $row) {
    //         //     fputcsv($handle, [
    //         //         $row->telecaller_status, $row->source, $row->assigned, $row->f_name,
    //         //         $row->l_name, $row->phone_number, $row->current_city, $row->intrested_city,
    //         //         $row->vehicle_type, $row->lead_sources, $row->register_date,
    //         //         $row->active_status, $row->task, $row->description
    //         //     ]);
    //         // }

    //         // Close the output stream
    //         fclose($handle);
    //     });

    //     // Set headers for the response
    //     $response->headers->set('Content-Type', 'text/csv');
    //     $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');

    //     return $response;
    // }
    
    // public function downloadExcel()
    // {
    //     // Define the filename for the exported file
    //     $fileName = 'telecaller_data.csv';
    
    //     // Fetch the last record from the database
    //     $record = leads::latest()->first(); // Fetch the last record based on the 'created_at' timestamp
    
    //     // Check if there is data
    //     if (!$record) {
    //         return response()->json(['message' => 'No data available to export'], 404);
    //     }
    
    //     $response = new StreamedResponse(function () use ($record) {
    //         // Open output stream
    //         $handle = fopen('php://output', 'w');
    
    //         // Convert the record to an array and exclude unwanted columns
    //         $recordArray = $record->toArray();
    //         $filteredKeys = array_diff(array_keys($recordArray), ['id', 'created_at', 'updated_at']);
    
    //         // Add the filtered column headers
    //         fputcsv($handle, $filteredKeys);
    
    //         // Add a single filtered data row
    //         $filteredRow = array_intersect_key($recordArray, array_flip($filteredKeys));
    //         fputcsv($handle, array_values($filteredRow));
    
    //         // Close the output stream
    //         fclose($handle);
    //     });
    
    //     // Set headers for the response
    //     $response->headers->set('Content-Type', 'text/csv');
    //     $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    
    //     return $response;
    // }
    
    public function leadsExcel_download(Request $request){
        return Excel::download(new TelecallerExport, 'Bulk_leads_import.xlsx');
    }
    
    public function importExcel(Request $request)
    {
        try {
            $file = $request->file('excel_file');
    
            // Check if the file exists
            if (!$file) {
                return redirect()->back()->with('error', 'No file was uploaded.');
            }
    
            // Validate file type
            if ($file->getClientOriginalExtension() !== 'xlsx') {
                return redirect()->back()->with('error', 'Invalid file format. Please upload an Excel (.xlsx) file.');
            }
    
             $file_name = 'Bulk_leads_import' . now()->timestamp . '.xlsx';
            
            // Validate file name
            if (!preg_match('/^Bulk_leads_import(?:\s\(\d+\))?\.xlsx$/', $file->getClientOriginalName())) {
                return redirect()->back()->with('error', 'This file import does not match. Please use the correct bulk lead import sheet.');
            }
    
            // Import leads
            Excel::import(new LeadImport, $file);
    
            return redirect()->back()->with('success', 'Leads imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    // public function importExcel(Request $request)
    // {
    //   // Validate the uploaded file
    //     $validator = Validator::make($request->all(), [
    //         'excel_file' => 'required|mimes:xlsx,xls',
    //     ]);
    
    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput();
    //     }
    
    //     // Load the file
    //     $file = $request->file('excel_file');
    //     $file_name = $file->getClientOriginalName();
        
    //     if ($file_name !== "Bulk_leads_import.xlsx") {
    //         return redirect()->back()->with('error', 'This file import does not match. Please use the correct bulk lead import sheet.');
    //     }
    
    //     if (!isset($data[0])) {
    //         return redirect()->back()->with('error', 'No data found in the sheet.');
    //     }
    
    //     $rows = $data[0]; // Assuming first sheet is "LeadImportsheet"
    
    //     foreach ($rows as $index => $row) {
    //         if ($index === 0) continue; // Skip header row
    
    //         // Ensure required keys exist
    //         if (!isset($row['Mobile_Number'])) {
    //             return redirect()->back()->with('error', "Missing 'Mobile_Number' column in row $index.");
    //         }
    
    //         $lead = Lead::where('phone_number', $row['Mobile_Number'])->first();
    
    //         $leadData = [
    //             'telecaller_status' => $row['Telecaller_Status'] ?? null,
    //             'source'            => $row['Source_id'] ?? null,
    //             'assigned'          => $row['Telecaller_ID'] ?? null,
    //             'f_name'            => $row['First_Name'] ?? null,
    //             'l_name'            => $row['Last_Name'] ?? null,
    //             'phone_number'      => $row['Mobile_Number'] ?? null,
    //             'current_city'      => $row['Current_city_id'] ?? null,
    //             'intrested_city'    => $row['Interested_Area_id'] ?? null,
    //             'vehicle_type'      => $row['Vehicle_Type_id'] ?? null,
    //             'description'       => $row['Description'] ?? null,
    //         ];
    
    //         if ($lead) {
    //             $lead->update($leadData);
    //         } else {
    //             Lead::create($leadData);
    //         }
    //     }
    
    //     return redirect()->back()->with('success', 'Leads imported successfully!');
    // }
    
  public function addComment(Request $request)
  {
        $validator = Validator::make($request->all(), [
            'comment' => 'required',
            'task_id' => 'required',
            'user_role' => 'required',
            'commenter_id' => 'required',
            'existing_comment_id' => 'nullable|exists:telecaller_comments,id',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors(), 'message' => 'Validation Error!'], 422);
        }
    // dd($request->all());
         if (!empty($request->existing_comment_id)) {
            DB::table('telecaller_comments')
                ->where('id', $request->existing_comment_id)
                ->update([
                    'comment' => $request->comment,
                    'task_id' => $request->task_id,
                    'user_role' => $request->user_role,
                    'commenter_id' => $request->commenter_id,
                    'updated_at' => now(), 
                ]);
        } else {
            DB::table('telecaller_comments')->insert([
                'comment' => $request->comment,
                'task_id' => $request->task_id,
                'user_role' => $request->user_role,
                'commenter_id' => $request->commenter_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

    
        $telecaller_comments = DB::table('telecaller_comments')
            ->where('task_id', $request->task_id)
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();
    
        $comment_html = '';
    
        if ($telecaller_comments->isNotEmpty()) {
            foreach ($telecaller_comments as $comments) {
                $comment_html .= '
                <div class="comment-item mb-3 p-3 rounded shadow-sm bg-white" style="cursor: pointer;" 
                     data-comment-id="' . $comments->id . '" 
                     data-task-id="' . $comments->task_id . '">
        
                    <!-- Avatar, Name, and Date -->
                    <div class="d-flex align-items-center justify-content-between">
                        <!-- Left Side (Avatar and Name) -->
                        <div class="d-flex align-items-center">
                            <div class="v-avatar avatar mr-3">
                                <img src="' . asset('admin-assets/img/comment_icon.png') . '" 
                                     alt="Admin" class="img-fluid rounded-circle" style="width: 40px; height: 40px;">
                            </div>
                            <div>
                                <p class="small-para1 px-2 mb-0">Commented on: ' . \Carbon\Carbon::parse($comments->created_at)->format('d M, Y H:i') . '</p>
                                <strong class="text-primary displayName title px-2">' . $comments->commenter_id . '</strong>
                            </div>
                        </div>
        
                        <!-- Right Side (Edit/Delete Buttons) -->
                        <small class="text-muted displayName caption">
                            <i class="bi bi-pencil-square text-primary fw-bold me-1" style="cursor: pointer;" 
                               onclick="OnEditComment(\'' . $comments->id . '\', \'' . $comments->task_id . '\')"></i>
                            <i class="bi bi-trash text-danger fw-bold" style="cursor: pointer;" 
                               onclick="return OndeleteComment(\'' . $comments->id . '\', \'' . $comments->task_id . '\')"></i>
                        </small>
                    </div>
        
                    <!-- Comment Content Section -->
                    <div class="mt-3">
                        <p>' .$comments->comment . '</p>
                    </div>
                </div>';
            }
        } else {
            $comment_html .= '<p>No comments yet.</p>';
        }
    
       return response()->json([
            'status' => true,
            'message' => !empty($request->existing_comment_id) 
                         ? 'Comment updated successfully!' 
                         : 'Comment added successfully!',
            'comment_html' => $comment_html
        ]);

    }

    
    public function deleteComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:telecaller_comments,id',
            'task_id' => 'required'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Validation Error!');
        }
    
        $delete = DB::table('telecaller_comments')
            ->where('id', $request->id)
            ->where('task_id', $request->task_id)
            ->delete();
    
        if ($delete) {
            return response()->json(['status' => true, 'message' => 'Comment deleted successfully.']);
        } else {
            return response()->json(['status' => false, 'message' => 'Please try again.']);
        }
    }

    public function getComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:telecaller_comments,id',
            'task_id' => 'required'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Validation Error!');
        }
    
        $edit_data = DB::table('telecaller_comments')
            ->where('id', $request->id)
            ->where('task_id', $request->task_id)
            ->first();
    
        if ($edit_data) {
            return response()->json(['status' => true, 'data' => $edit_data]);
        } else {
            return response()->json(['status' => false, 'message' => 'Comment not found.']);
        }
    }


    public function assignTelecaller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:ev_tbl_leads,id',
            'caller_id'=>'required'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Validation Error!');
        }
        
        // dd($request->all());
    
        $update = DB::table('ev_tbl_leads')
            ->where('id', $request->lead_id)->update(['assigned'=>$request->caller_id]);
    
        if ($update) {
            return response()->json(['status' => true, 'message' => 'Telecaller Assigned Successfully!']);
        } else {
            return response()->json(['status' => false, 'message' => 'Lead not found']);
        }
    }
    
    public function get_popup_data(Request $request, $id)
    {
        $lead = leads::where('id', $id)->first();
    
        if (!$lead) {
            return response()->json(['status'=>true,'message' => 'Lead not found'], 404);
        }

        $img = User::where('id', $lead->assigned)->first();
    
        $image_data_uri = null;
        $caller_name = null;
        if ($img) {
            $image_data_uri = $img->profile_photo_path ? asset('admin-assets/users/' . $img->profile_photo_path) : null;
            $caller_name = $img->name ?? null;
        }
    
        $city = City::where('id', $lead->current_city)->first();
    
        $area_data = Area::where('status', 1)->where('city_id', $lead->current_city)->get();
    
        $telecaller_comments = DB::table('telecaller_comments')->where('task_id', $id)->orderBy('id', 'desc')->get();
    
        $source = DB::table('ev_tbl_lead_source')->where('id', $lead->source)->first();
        $source_name = $source->id ?? 3;
        
        $area_html = '';
        $area_html .= '<option value="">Select an Area</option>'; // get area
        foreach ($area_data as $area) {
            $selected = ($lead->intrested_city != "" && $area->id == $lead->intrested_city) ? 'selected' : '';
            $area_html .= '<option value="'.$area->id.'" '.$selected.'>'.$area->Area_name.'</option>';
        }

        $telecaller_comments = DB::table('telecaller_comments')
            ->where('task_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        $comment_html = '';
        
        if ($telecaller_comments->isNotEmpty()) {
            foreach ($telecaller_comments as $comments) { //get comments
                $comment_html .= '
                <div class="comment-item mb-3 p-3 rounded shadow-sm bg-white" style="cursor: pointer;" 
                     data-comment-id="' . $comments->id . '" 
                     data-task-id="' . $comments->task_id . '">
        
                    <!-- Avatar, Name, and Date -->
                    <div class="d-flex align-items-center justify-content-between">
                        <!-- Left Side (Avatar and Name) -->
                        <div class="d-flex align-items-center">
                            <div class="v-avatar avatar mr-3">
                                <img src="' . asset('admin-assets/img/comment_icon.png') . '" 
                                     alt="Admin" class="img-fluid rounded-circle" style="width: 40px; height: 40px;">
                            </div>
                            <div>
                                <p class="small-para1 px-2 mb-0">Commented on: ' . \Carbon\Carbon::parse($comments->created_at)->format('d M, Y H:i') . '</p>
                                <strong class="text-primary displayName title px-2">' . $comments->commenter_id . '</strong>
                            </div>
                        </div>
        
                        <!-- Right Side (Edit/Delete Buttons) -->
                        <small class="text-muted displayName caption">
                            <i class="bi bi-pencil-square text-primary fw-bold me-1" style="cursor: pointer;" 
                               onclick="OnEditComment(\'' . $comments->id . '\', \'' . $comments->task_id . '\', \'' . $id . '\')"></i>
                            <i class="bi bi-trash text-danger fw-bold" style="cursor: pointer;" 
                               onclick="return OndeleteComment(\'' . $comments->id . '\', \'' . $comments->task_id . '\')"></i>
                        </small>
                    </div>
        
                    <!-- Comment Content Section -->
                    <div class="mt-3">
                        <p>' .$comments->comment . '</p>
                    </div>
                </div>';
            }
        } else {
            $comment_html .= '<p>No comments yet.</p>';
        }

        $assign_html = empty($lead->assigned) ? 'OnTelecallerAssign('.$id.', this.value)' : '';

        return response()->json([
            'status'=>true,
            'lead_id' => $id,
            'lead' => $lead,
            'caller_name' => $caller_name,
            'profile_image' => $image_data_uri,
            'city' => $city,
            'areas' => $area_data,
            'telecaller_comments' => $telecaller_comments,
            'source_name' => $source_name,
            'area_html'=>$area_html,
            'comments_html'=>$comment_html,
            'assign_html'=>$assign_html
        ]);
    }
    
    
    public function load_more_leaddata(Request $request) {
        $statuses = [
            'New' => 'New Lead',
            'Contacted' => 'Contacting',
            'Call_Back' => 'Call Back Request',
            'Onboarded' => 'Onboarded',
            'DeadLead'  => 'Dead Lead'
        ];
    
        $colors = ['#03a9f4', '#7cb342', '#fb8c00', '#c53da9', '#f32f10'];
        $colorIndex = 0;
        
        $limit = $request->limit;
        $status = $request->status;
        $offset = $request->offset;
        $login_user = DB::table('model_has_roles')
            ->where('model_id', auth()->user()->id)
            ->first();
    
        $roles = DB::table('roles')
            ->where('id', $login_user->role_id)
            ->first();
       
      $statuses1 = ['New', 'Contacted', 'Call_Back', 'Onboarded', 'DeadLead'];

        if (empty($request->tele_assign_id)) { // Page load data
            if ($roles->name != 'Telecaller') {
                $query = Leads::where('telecaller_status', $statuses1[0])
                    ->orderBy('id', 'desc')
                    ->limit($limit);
                
                foreach (array_slice($statuses1, 1) as $status) {
                    $query->unionAll(
                        Leads::where('telecaller_status', $status)
                            ->orderBy('id', 'desc')
                            ->limit($limit)
                    );
                }
            } else { // Telecaller case
                $query = Leads::where('telecaller_status', $statuses1[0])
                    ->where('assigned', auth()->user()->id) 
                    ->orderBy('id', 'desc')
                    ->limit($limit);
                
                foreach (array_slice($statuses1, 1) as $status) {
                    $query->unionAll(
                        Leads::where('telecaller_status', $status)
                            ->where('assigned', auth()->user()->id) 
                            ->orderBy('id', 'desc')
                            ->limit($limit)
                    );
                }
            }
        }
         else {
            
           if(isset($request->tele_assign_id) && $request->tele_assign_id != "not-assigned"){ //filter by telecaller
                $query = Leads::where('telecaller_status', $statuses1[0])
                    ->where('assigned', $request->tele_assign_id)
                    ->orderBy('id', 'desc')
                    ->limit($limit);
            
                foreach (array_slice($statuses1, 1) as $status) { 
                    $query->unionAll(
                        Leads::where('telecaller_status', $status)
                            ->where('assigned', $request->tele_assign_id)
                            ->orderBy('id', 'desc')
                            ->limit($limit)
                    );
                }
           }else{
                $query = Leads::where('telecaller_status', $statuses1[0])
                    ->whereNull('assigned')
                    ->orderBy('id', 'desc')
                    ->limit($limit);
            
                foreach (array_slice($statuses1, 1) as $status) { 
                    $query->unionAll(
                        Leads::where('telecaller_status', $status)
                            ->whereNull('assigned')
                            ->orderBy('id', 'desc')
                            ->limit($limit)
                    );
                }
           }
        }

        $list = $query->get();
        $html_data = '';
    
        foreach ($statuses as $key => $value) {
            $count_query = DB::table('ev_tbl_leads')
                ->where('telecaller_status', $key)
                ->when($roles->name == 'Telecaller', function ($query) {
                    return $query->where('Assigned', auth()->user()->id);
                });
            
            if (isset($request->tele_assign_id)) { 
                if ($request->tele_assign_id == "not-assigned") {
                    $count_query->whereNull('Assigned');
                } elseif ($request->tele_assign_id != "") {
                    $count_query->where('Assigned', $request->tele_assign_id);
                }
            }
            
            $count = $count_query->count();
    
            $html_data .= '<div class="col kanban-column card" id="' . $key . '">
                <p class="card-header" style="background-color: ' . $colors[$colorIndex] . '">' . $value . ' - ' . $count . ' Leads</p>
                <div class="card-body p-0">
                    <div class="kanban-cards kanban-cards-'.$key.'">';
    
            $hasLeads = false;
            foreach ($list as $val) {
                if ($key == $val->telecaller_status) {
                    $hasLeads = true;
                    $img = DB::table('users')->where('id', $val->assigned)->first();
                    $image_data_uri = $img && $img->profile_photo_path ? asset('uploads/users/' . $img->profile_photo_path) : null;
                    $caller_name = $img->name ?? 'Not Assigned';
                    $source = DB::table('ev_tbl_lead_source')->where('id', $val->source)->first();
                    $source_name = $source->source_name ?? 'Twitter';
                    $telecaller_comments_count = DB::table('telecaller_comments')->where('task_id', $val->id)->count();
    
                    $html_data .= '<div class="kanban-items m-1" id="item' . $val->id . '" data-item_id="' . $val->id . '" draggable="true">
                        <div class="card task-card bg-white m-2 task-body" style="border-top: 2px solid ' . $colors[$colorIndex] . '" onclick="openModal(' . $val->id . ',\'current_city\')">
                            <div class="card-body">
                                <p class="mb-0 small-para fw-medium" style="color:' . $colors[$colorIndex] . ';"><span class="lead-heading">Name : </span>' . Str::limit(ucfirst($val->f_name) . ' ' . ucfirst($val->l_name), 22, '...') . '</p>
                                <p class="mb-0 small-para fw-medium phone-number"><span class="lead-heading">Phone :</span> ' . ($val->phone_number ?? '') . '</p>
                                <p class="mb-0 small-para fw-medium"><span class="lead-heading">Source :</span> ' . ucfirst($source_name) . '</p>
                                <p class="mb-0 small-para fw-medium"><span class="lead-heading">Last Updated :</span> ' . \Carbon\Carbon::parse($val->created_at)->format('d M, Y H:i') . '</p>
                            </div>
                            <div class="card-footer px-3 py-2 d-flex justify-content-between">
                                <div>';
            
                    if ($image_data_uri) {
                        $html_data .= '<img src="' . $image_data_uri . '" class="d-inline" alt="Telecaller Image" width="25" height="25" style="border-radius: 50%;">
                        <p class="mb-0 small-para fw-medium d-inline">' . $caller_name . '</p>';
                    } else {
                        $html_data .= '<p class="mb-0 small-para fw-medium d-inline text-danger"><i class="bi bi-people-fill"></i> Not Assigned</p>';
                    }
            
                    $html_data .= '</div>
                                <p class="mb-0 small-para fw-medium"><i class="bi bi-chat-dots fw-bold text-warning"></i> ' . $telecaller_comments_count . '</p>
                            </div>
                        </div>
                    </div>';
                  $last_id = $val->id;
                }
            }
    
            if (!$hasLeads) {
                $html_data .= '<div class="text-center mt-5 card-inside" id="no-lead-' . $key . '">
                    <h4><i class="bi bi-opencollective"></i></h4>
                    <h4>No Leads Found</h4>
                </div>';
            } else { //Lead more button
               $html_data .= '<div class="text-center card-inside" id="lead-more-' . $key . '">
                    <button class="btn btn-primary w-100 lead-more-btn" data-status="' . $key . '" 
                        data-get_last_id="' . $last_id . '" 
                        data-get_tele_assign_id="' . (!empty($request->tele_assign_id) ? $request->tele_assign_id : '') . '">
                        Lead More
                    </button>
                </div>';

            }
            
            $html_data .= '</div>
                </div>
            </div>';
    
            $colorIndex = ($colorIndex + 1) % count($colors);
        }
    
        return response()->json(['html_data' => $html_data]);
    }
    
    public function append_leaddata(Request $request) {
        $status = $request->status;
        $last_id = $request->last_id;
        $limit = 30;
        
        $colors = [
            'New' => '#03a9f4',
            'Contacted' => '#7cb342',
            'Call_Back' => '#fb8c00',
            'Onboarded' => '#c53da9',
            'DeadLead'  => '#f32f10'
        ];
        $border_color = $colors[$status] ?? '#000000';
        
        $login_user = DB::table('model_has_roles')
            ->where('model_id', auth()->user()->id)
            ->first();
    
        $roles = DB::table('roles')
            ->where('id', $login_user->role_id)
            ->first();
        if($roles->name != 'Telecaller'){
           
            $query = Leads::where('telecaller_status', $status)
            ->where('id', '<', $last_id);
            if(!empty($request->tele_id) && $request->tele_id != "not-assigned"){
                $query->where('assigned',$request->tele_id);
            }else if(!empty($request->tele_id) && $request->tele_id == "not-assigned"){
               $query->whereNull('assigned'); 
            }
            $query ->orderBy('id', 'desc') 
                ->limit($limit);
                
        }else if($roles->name == 'Telecaller'){
            $query = Leads::where('telecaller_status', $status)
            ->where('id', '<', $last_id) 
            ->where('assigned',auth()->user()->id)
            ->orderBy('id', 'desc') 
            ->limit($limit);
        }else{
             $query = Leads::where('telecaller_status', $status)
            ->where('id', '<', $last_id) 
            ->orderBy('id', 'desc') 
            ->limit($limit);
        }
        $list = $query->get();
        $html_data = '';
        $hasLeads = false;
        foreach ($list as $val) {
            
                    $hasLeads = true;
                    $img = DB::table('users')->where('id', $val->assigned)->first();
                    $image_data_uri = $img && $img->profile_photo_path ? asset('uploads/users/' . $img->profile_photo_path) : null;
                    $caller_name = $img->name ?? 'Not Assigned';
                    $source = DB::table('ev_tbl_lead_source')->where('id', $val->source)->first();
                    $source_name = $source->source_name ?? 'Twitter';
                    $telecaller_comments_count = DB::table('telecaller_comments')->where('task_id', $val->id)->count();
    
                    $html_data .= '<div class="kanban-items m-1" id="item' . $val->id . '" data-item_id="' . $val->id . '" draggable="true">
                        <div class="card task-card bg-white m-2 task-body" style="border-top: 2px solid ' . $border_color . '" onclick="openModal(' . $val->id . ',\'current_city\')">
                            <div class="card-body">
                                <p class="mb-0 small-para fw-medium" style="color:' . $border_color . ';"><span class="lead-heading">Name : </span>' . Str::limit(ucfirst($val->f_name) . ' ' . ucfirst($val->l_name), 22, '...') . '</p>
                                <p class="mb-0 small-para fw-medium phone-number"><span class="lead-heading">Phone :</span> ' . ($val->phone_number ?? '') . '</p>
                                <p class="mb-0 small-para fw-medium"><span class="lead-heading">Source :</span> ' . ucfirst($source_name) . '</p>
                                <p class="mb-0 small-para fw-medium"><span class="lead-heading">Last Updated :</span> ' . \Carbon\Carbon::parse($val->created_at)->format('d M, Y H:i') . '</p>
                            </div>
                            <div class="card-footer px-3 py-2 d-flex justify-content-between">
                                <div>';
            
                    if ($image_data_uri) {
                        $html_data .= '<img src="' . $image_data_uri . '" class="d-inline" alt="Telecaller Image" width="25" height="25" style="border-radius: 50%;">
                        <p class="mb-0 small-para fw-medium d-inline">' . $caller_name . '</p>';
                    } else {
                        $html_data .= '<p class="mb-0 small-para fw-medium d-inline text-danger"><i class="bi bi-people-fill"></i> Not Assigned</p>';
                    }
            
                    $html_data .= '</div>
                                <p class="mb-0 small-para fw-medium"><i class="bi bi-chat-dots fw-bold text-warning"></i> ' . $telecaller_comments_count . '</p>
                            </div>
                        </div>
                    </div>';
                  $last_id = $val->id;
                
        }
        
         if (!$hasLeads) {
                $html_data .= '<div class="text-center mt-5 card-inside" id="no-lead-' . $status . '">
                    <h4><i class="bi bi-opencollective"></i></h4>
                    <h4>No Leads Found</h4>
                </div>';
            } else {
                $html_data .= '<div class="text-center card-inside" id="lead-more-' . $status . '">
                    <button class="btn btn-primary w-100 lead-more-btn" data-status="' . $status . '" data-get_last_id = "'.$last_id.'" data-get_tele_assign_id="' . (!empty($request->tele_id) ? $request->tele_id : '') . '">Lead More</button>
                </div>';
            }
            
    
        return response()->json(['html_data' => $html_data, 'last_id' => $last_id,'status'=>$status]);
    }
    
    public function search_leaddata(Request $request) {
        $statuses = [
            'New' => 'New Lead',
            'Contacted' => 'Contacting',
            'Call_Back' => 'Call Back Request',
            'Onboarded' => 'Onboarded',
            'DeadLead'  => 'Dead Lead'
        ];
    
        $colors = ['#03a9f4', '#7cb342', '#fb8c00', '#c53da9', '#f32f10'];
        $colorIndex = 0;
        $search_data = $request->search_data;
        $limit = $request->limit;
        $status = $request->status;
        $offset = $request->offset;
        $login_user = DB::table('model_has_roles')
            ->where('model_id', auth()->user()->id)
            ->first();
        $roles = DB::table('roles')
            ->where('id', $login_user->role_id)
            ->first();
       
        $statuses1 = ['New', 'Contacted', 'Call_Back', 'Onboarded', 'DeadLead'];

       if ($roles->name != 'Telecaller') {
            $query = Leads::where('phone_number', 'like', '%' . $search_data . '%') // Contains search
                ->limit($limit);
        } else {
            $query = Leads::where('phone_number', 'like', '%' . $search_data . '%') // Contains search
                ->where('assigned', auth()->user()->id)
                ->limit($limit);
        }
        $list = $query->get();
        $html_data = '';
        foreach ($statuses as $key => $value) {
             $count = $list->filter(function ($lead) use ($key) {
                    return $lead->telecaller_status == $key;
                })->count();
    
            $html_data .= '<div class="col kanban-column card" id="' . $key . '">
                <p class="card-header" style="background-color: ' . $colors[$colorIndex] . '">' . $value . ' - ' . $count . ' Leads</p>
                <div class="card-body p-0">
                    <div class="kanban-cards kanban-cards-'.$key.'">';
    
            $hasLeads = false;
            foreach ($list as $val) {
                if ($key == $val->telecaller_status) {
                    $hasLeads = true;
                    $img = DB::table('users')->where('id', $val->assigned)->first();
                    $image_data_uri = $img && $img->profile_photo_path ? asset('uploads/users/' . $img->profile_photo_path) : null;
                    $caller_name = $img->name ?? 'Not Assigned';
                    $source = DB::table('ev_tbl_lead_source')->where('id', $val->source)->first();
                    $source_name = $source->source_name ?? 'Twitter';
                    $telecaller_comments_count = DB::table('telecaller_comments')->where('task_id', $val->id)->count();
    
                    $html_data .= '<div class="kanban-items m-1" id="item' . $val->id . '" data-item_id="' . $val->id . '" draggable="true">
                        <div class="card task-card bg-white m-2 task-body" style="border-top: 2px solid ' . $colors[$colorIndex] . '" onclick="openModal(' . $val->id . ',\'current_city\')">
                            <div class="card-body">
                                <p class="mb-0 small-para fw-medium" style="color:' . $colors[$colorIndex] . ';"><span class="lead-heading">Name : </span>' . Str::limit(ucfirst($val->f_name) . ' ' . ucfirst($val->l_name), 22, '...') . '</p>
                                <p class="mb-0 small-para fw-medium phone-number"><span class="lead-heading">Phone :</span> ' . ($val->phone_number ?? '') . '</p>
                                <p class="mb-0 small-para fw-medium"><span class="lead-heading">Source :</span> ' . ucfirst($source_name) . '</p>
                                <p class="mb-0 small-para fw-medium"><span class="lead-heading">Last Updated :</span> ' . \Carbon\Carbon::parse($val->created_at)->format('d M, Y H:i') . '</p>
                            </div>
                            <div class="card-footer px-3 py-2 d-flex justify-content-between">
                                <div>';
            
                    if ($image_data_uri) {
                        $html_data .= '<img src="' . $image_data_uri . '" class="d-inline" alt="Telecaller Image" width="25" height="25" style="border-radius: 50%;">
                        <p class="mb-0 small-para fw-medium d-inline">' . $caller_name . '</p>';
                    } else {
                        $html_data .= '<p class="mb-0 small-para fw-medium d-inline text-danger"><i class="bi bi-people-fill"></i> Not Assigned</p>';
                    }
            
                    $html_data .= '</div>
                                <p class="mb-0 small-para fw-medium"><i class="bi bi-chat-dots fw-bold text-warning"></i> ' . $telecaller_comments_count . '</p>
                            </div>
                        </div>
                    </div>';
                  $last_id = $val->id;
                }
            }
    
            if (!$hasLeads) {
                $html_data .= '<div class="text-center mt-5 card-inside" id="no-lead-' . $key . '">
                    <h4><i class="bi bi-opencollective"></i></h4>
                    <h4>No Leads Found</h4>
                </div>';
            } else { //Lead more button
            //   $html_data .= '<div class="text-center card-inside" id="lead-more-' . $key . '">
            //         <button class="btn btn-primary w-100 lead-more-btn" data-status="' . $key . '" 
            //             data-get_last_id="' . $last_id . '" 
            //             data-get_tele_assign_id="' . (!empty($request->tele_assign_id) ? $request->tele_assign_id : '') . '">
            //             Lead More
            //         </button>
            //     </div>';

            }
            
            $html_data .= '</div>
                </div>
            </div>';
    
            $colorIndex = ($colorIndex + 1) % count($colors);
        }
    
        return response()->json(['html_data' => $html_data]);
    }

    



    

}
