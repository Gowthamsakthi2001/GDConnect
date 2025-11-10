<?php

namespace Modules\HRStatus\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Deliveryman\Entities\Deliveryman;
use Modules\City\Entities\City;
use Modules\City\Entities\Area;
use App\Models\BgvComment;
use App\Models\BgvDocument;
use Modules\RiderType\Entities\RiderType;
use App\Models\HrQuery;
use App\Models\CandidateKycUpdate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeCategoryController extends Controller
{
    public function employee_list(Request $request)
    {

        $employees = Deliveryman::with('current_city')
               ->where('work_type', 'in-house')
               ->where('register_date_time', '!=', '')
               ->where('delete_status', 0)
               ->get();
               
    
  
         return view('hrstatus::employee.employee_list' , compact('employees'));
    }
    
        public function employee_view(Request $request ,$id)
    {
        $employee = Deliveryman::with([
                       'current_city',
                       'interest_city',
                       'RiderType'
                  ])->findOrFail($id);
          $cities = City::all();
          $riderTypes = RiderType::all();
  
         return view('hrstatus::employee.employee_preview' , compact('employee' ,'cities' ,'riderTypes'));
    }
    
    

    public function rider_list(Request $request)
    {
        $riders = Deliveryman::with('current_city')
               ->where('work_type', 'deliveryman')
               ->where('register_date_time', '!=', '')
               ->where('delete_status', 0)
               ->get();

        
               
               
         return view('hrstatus::rider.rider_list' , compact('riders'));
    }
    
        public function adhoc_list(Request $request)
    {
        $adhocs = Deliveryman::with('current_city')
               ->where('work_type', 'adhoc')
                ->where('register_date_time', '!=', '')
               ->where('delete_status', 0)
               ->get();
               
               
         return view('hrstatus::adhoc.adhoc_list' ,compact('adhocs'));
    }
    
    
    
    
    public function helper_list(Request $request)
    {
                $helpers = Deliveryman::with('current_city')
               ->where('work_type', 'helper')
                ->where('register_date_time', '!=', '')
               ->where('delete_status', 0)
               ->get();
               
               
         return view('hrstatus::helper.helper_list' , compact('helpers'));
    }
    
    
            public function rider_view(Request $request , $id)
    {
  
          $rider = Deliveryman::with([
                       'current_city',
                       'interest_city',
                       'RiderType'
                  ])->findOrFail($id);
          $cities = City::all();
          $riderTypes = RiderType::all();
          
         return view('hrstatus::rider.rider_preview' , compact('rider' , 'cities' ,'riderTypes'));
    }
    
    
    
            public function adhoc_view(Request $request , $id)
    {
        $adhoc = Deliveryman::with([
                       'current_city',
                       'interest_city',
                       'RiderType'
                  ])->findOrFail($id);
          $cities = City::all();
          $riderTypes = RiderType::all();
  
         return view('hrstatus::adhoc.adhoc_preview' , compact('adhoc' , 'cities' ,'riderTypes'));
    }
    
    
    
            public function helper_view(Request $request ,$id)
    {
  
          $helper = Deliveryman::with([
                       'current_city',
                       'interest_city',
                       'RiderType'
                  ])->findOrFail($id);
          $cities = City::all();
          $riderTypes = RiderType::all();
          
         return view('hrstatus::helper.helper_preview' , compact('helper' , 'cities' ,'riderTypes'));
    }
    
    public function employee_update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:15',
            'current_city_id' => 'required|exists:ev_tbl_city,id',
            'interested_city_id' => 'nullable|exists:ev_tbl_city,id',
            'gender' => 'nullable|string',
            'house_no' => 'nullable|string',
            'street_name' => 'nullable|string',
            'pincode' => 'nullable|string',
            'alternative_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date_format:d-m-Y',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'father_name' => 'nullable|string',
            'father_mobile_number' => 'nullable|string',
            'referal_person_name' => 'nullable|string',
            'referal_person_mobile' => 'nullable|string',
            'referal_person_relationship' => 'nullable|string',
            'spouse_name' => 'nullable|string',
            'spouse_mobile_number' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'social_links' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
        ]);
    
        // Convert date to Y-m-d if provided
        if (!empty($validatedData['date_of_birth'])) {
            $validatedData['date_of_birth'] = \Carbon\Carbon::createFromFormat('d-m-Y', $validatedData['date_of_birth'])->format('Y-m-d');
        }
    
        try {
            $employee = EvDeliveryMan::findOrFail($id);
    
            $employee->first_name = $validatedData['first_name'];
            $employee->last_name = $validatedData['last_name'];
            $employee->email = $validatedData['email'];
            $employee->mobile_number = $validatedData['mobile_number'];
            $employee->current_city_id = $validatedData['current_city_id'];
            $employee->interested_city_id = $validatedData['interested_city_id'] ?? null;
            $employee->gender = $validatedData['gender'] ?? null;
            $employee->house_no = $validatedData['house_no'] ?? null;
            $employee->street_name = $validatedData['street_name'] ?? null;
            $employee->pincode = $validatedData['pincode'] ?? null;
            $employee->alternative_number = $validatedData['alternative_number'] ?? null;
            $employee->date_of_birth = $validatedData['date_of_birth'] ?? null;
            $employee->present_address = $validatedData['present_address'] ?? null;
            $employee->permanent_address = $validatedData['permanent_address'] ?? null;
            $employee->father_name = $validatedData['father_name'] ?? null;
            $employee->father_mobile_number = $validatedData['father_mobile_number'] ?? null;
            $employee->referal_person_name = $validatedData['referal_person_name'] ?? null;
            $employee->referal_person_number = $validatedData['referal_person_mobile'] ?? null;
            $employee->referal_person_relationship = $validatedData['referal_person_relationship'] ?? null;
            $employee->spouse_name = $validatedData['spouse_name'] ?? null;
            $employee->spouse_mobile_number = $validatedData['spouse_mobile_number'] ?? null;
            $employee->blood_group = $validatedData['blood_group'] ?? null;
            $employee->social_links = $validatedData['social_links'] ?? null;
            $employee->bank_name = $validatedData['bank_name'] ?? null;
            $employee->account_holder_name = $validatedData['account_holder_name'] ?? null;
            $employee->account_number = $validatedData['account_number'] ?? null;
            $employee->ifsc_code = $validatedData['ifsc_code'] ?? null;
    
            $employee->save();
            $employee->refresh();
    
            $type = 'employee';
    
            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' updated successfully',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update employee: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
        public function rider_update(Request $request, $id)
    {
      
        
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:15',
            'current_city_id' => 'required|exists:ev_tbl_city,id',
            'interested_city_id' => 'nullable|exists:ev_tbl_city,id',
            'gender' => 'nullable|string',
            'house_no' => 'nullable|string',
            'street_name' => 'nullable|string',
            'pincode' => 'nullable|string',
            'alternative_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date_format:d-m-Y',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'father_name' => 'nullable|string',
            'father_mobile_number' => 'nullable|string',
            'referal_person_name' => 'nullable|string',
            'referal_person_mobile' => 'nullable|string',
            'referal_person_relationship' => 'nullable|string',
            'spouse_name' => 'nullable|string',
            'spouse_mobile_number' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'social_links' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
        ]);
    
        // Convert date to Y-m-d if provided
        if (!empty($validatedData['date_of_birth'])) {
            $validatedData['date_of_birth'] = \Carbon\Carbon::createFromFormat('d-m-Y', $validatedData['date_of_birth'])->format('Y-m-d');
        }
    
        try {
            $employee = EvDeliveryMan::findOrFail($id);
    
            $employee->first_name = $validatedData['first_name'];
            $employee->last_name = $validatedData['last_name'];
            $employee->email = $validatedData['email'];
            $employee->mobile_number = $validatedData['mobile_number'];
            $employee->current_city_id = $validatedData['current_city_id'];
            $employee->interested_city_id = $validatedData['interested_city_id'] ?? null;
            $employee->gender = $validatedData['gender'] ?? null;
            $employee->house_no = $validatedData['house_no'] ?? null;
            $employee->street_name = $validatedData['street_name'] ?? null;
            $employee->pincode = $validatedData['pincode'] ?? null;
            $employee->alternative_number = $validatedData['alternative_number'] ?? null;
            $employee->date_of_birth = $validatedData['date_of_birth'] ?? null;
            $employee->present_address = $validatedData['present_address'] ?? null;
            $employee->permanent_address = $validatedData['permanent_address'] ?? null;
            $employee->father_name = $validatedData['father_name'] ?? null;
            $employee->father_mobile_number = $validatedData['father_mobile_number'] ?? null;
            $employee->referal_person_name = $validatedData['referal_person_name'] ?? null;
            $employee->referal_person_number = $validatedData['referal_person_mobile'] ?? null;
            $employee->referal_person_relationship = $validatedData['referal_person_relationship'] ?? null;
            $employee->spouse_name = $validatedData['spouse_name'] ?? null;
            $employee->spouse_mobile_number = $validatedData['spouse_mobile_number'] ?? null;
            $employee->blood_group = $validatedData['blood_group'] ?? null;
            $employee->social_links = $validatedData['social_links'] ?? null;
            $employee->bank_name = $validatedData['bank_name'] ?? null;
            $employee->account_holder_name = $validatedData['account_holder_name'] ?? null;
            $employee->account_number = $validatedData['account_number'] ?? null;
            $employee->ifsc_code = $validatedData['ifsc_code'] ?? null;
    
            $employee->save();
            $employee->refresh();
    
            $type = 'rider';
    
            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' updated successfully',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update employee: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
            public function adhoc_update(Request $request, $id)
    {
      
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:15',
            'current_city_id' => 'required|exists:ev_tbl_city,id',
            'interested_city_id' => 'nullable|exists:ev_tbl_city,id',
            'gender' => 'nullable|string',
            'house_no' => 'nullable|string',
            'street_name' => 'nullable|string',
            'pincode' => 'nullable|string',
            'alternative_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date_format:d-m-Y',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'father_name' => 'nullable|string',
            'father_mobile_number' => 'nullable|string',
            'referal_person_name' => 'nullable|string',
            'referal_person_mobile' => 'nullable|string',
            'referal_person_relationship' => 'nullable|string',
            'spouse_name' => 'nullable|string',
            'spouse_mobile_number' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'social_links' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
        ]);
    
        // Convert date to Y-m-d if provided
        if (!empty($validatedData['date_of_birth'])) {
            $validatedData['date_of_birth'] = \Carbon\Carbon::createFromFormat('d-m-Y', $validatedData['date_of_birth'])->format('Y-m-d');
        }
    
        try {
            $employee = EvDeliveryMan::findOrFail($id);
    
            $employee->first_name = $validatedData['first_name'];
            $employee->last_name = $validatedData['last_name'];
            $employee->email = $validatedData['email'];
            $employee->mobile_number = $validatedData['mobile_number'];
            $employee->current_city_id = $validatedData['current_city_id'];
            $employee->interested_city_id = $validatedData['interested_city_id'] ?? null;
            $employee->gender = $validatedData['gender'] ?? null;
            $employee->house_no = $validatedData['house_no'] ?? null;
            $employee->street_name = $validatedData['street_name'] ?? null;
            $employee->pincode = $validatedData['pincode'] ?? null;
            $employee->alternative_number = $validatedData['alternative_number'] ?? null;
            $employee->date_of_birth = $validatedData['date_of_birth'] ?? null;
            $employee->present_address = $validatedData['present_address'] ?? null;
            $employee->permanent_address = $validatedData['permanent_address'] ?? null;
            $employee->father_name = $validatedData['father_name'] ?? null;
            $employee->father_mobile_number = $validatedData['father_mobile_number'] ?? null;
            $employee->referal_person_name = $validatedData['referal_person_name'] ?? null;
            $employee->referal_person_number = $validatedData['referal_person_mobile'] ?? null;
            $employee->referal_person_relationship = $validatedData['referal_person_relationship'] ?? null;
            $employee->spouse_name = $validatedData['spouse_name'] ?? null;
            $employee->spouse_mobile_number = $validatedData['spouse_mobile_number'] ?? null;
            $employee->blood_group = $validatedData['blood_group'] ?? null;
            $employee->social_links = $validatedData['social_links'] ?? null;
            $employee->bank_name = $validatedData['bank_name'] ?? null;
            $employee->account_holder_name = $validatedData['account_holder_name'] ?? null;
            $employee->account_number = $validatedData['account_number'] ?? null;
            $employee->ifsc_code = $validatedData['ifsc_code'] ?? null;
    
            $employee->save();
            $employee->refresh();
    
            $type = 'adhoc';
    
            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' updated successfully',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update employee: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }


    public function helper_update(Request $request, $id)
    {
        
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_number' => 'required|string|max:15',
            'current_city_id' => 'required|exists:ev_tbl_city,id',
            'interested_city_id' => 'nullable|exists:ev_tbl_city,id',
            'gender' => 'nullable|string',
            'house_no' => 'nullable|string',
            'street_name' => 'nullable|string',
            'pincode' => 'nullable|string',
            'alternative_number' => 'nullable|string',
            'date_of_birth' => 'nullable|date_format:d-m-Y',
            'present_address' => 'nullable|string',
            'permanent_address' => 'nullable|string',
            'father_name' => 'nullable|string',
            'father_mobile_number' => 'nullable|string',
            'referal_person_name' => 'nullable|string',
            'referal_person_mobile' => 'nullable|string',
            'referal_person_relationship' => 'nullable|string',
            'spouse_name' => 'nullable|string',
            'spouse_mobile_number' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'social_links' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_holder_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'ifsc_code' => 'nullable|string',
        ]);
    
        // Convert date to Y-m-d if provided
        if (!empty($validatedData['date_of_birth'])) {
            $validatedData['date_of_birth'] = \Carbon\Carbon::createFromFormat('d-m-Y', $validatedData['date_of_birth'])->format('Y-m-d');
        }
    
        try {
            $employee = EvDeliveryMan::findOrFail($id);
    
            $employee->first_name = $validatedData['first_name'];
            $employee->last_name = $validatedData['last_name'];
            $employee->email = $validatedData['email'];
            $employee->mobile_number = $validatedData['mobile_number'];
            $employee->current_city_id = $validatedData['current_city_id'];
            $employee->interested_city_id = $validatedData['interested_city_id'] ?? null;
            $employee->gender = $validatedData['gender'] ?? null;
            $employee->house_no = $validatedData['house_no'] ?? null;
            $employee->street_name = $validatedData['street_name'] ?? null;
            $employee->pincode = $validatedData['pincode'] ?? null;
            $employee->alternative_number = $validatedData['alternative_number'] ?? null;
            $employee->date_of_birth = $validatedData['date_of_birth'] ?? null;
            $employee->present_address = $validatedData['present_address'] ?? null;
            $employee->permanent_address = $validatedData['permanent_address'] ?? null;
            $employee->father_name = $validatedData['father_name'] ?? null;
            $employee->father_mobile_number = $validatedData['father_mobile_number'] ?? null;
            $employee->referal_person_name = $validatedData['referal_person_name'] ?? null;
            $employee->referal_person_number = $validatedData['referal_person_mobile'] ?? null;
            $employee->referal_person_relationship = $validatedData['referal_person_relationship'] ?? null;
            $employee->spouse_name = $validatedData['spouse_name'] ?? null;
            $employee->spouse_mobile_number = $validatedData['spouse_mobile_number'] ?? null;
            $employee->blood_group = $validatedData['blood_group'] ?? null;
            $employee->social_links = $validatedData['social_links'] ?? null;
            $employee->bank_name = $validatedData['bank_name'] ?? null;
            $employee->account_holder_name = $validatedData['account_holder_name'] ?? null;
            $employee->account_number = $validatedData['account_number'] ?? null;
            $employee->ifsc_code = $validatedData['ifsc_code'] ?? null;
    
            $employee->save();
            $employee->refresh();
    
            $type = 'helper';
    
            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' updated successfully',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to update employee: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }
    
  
}