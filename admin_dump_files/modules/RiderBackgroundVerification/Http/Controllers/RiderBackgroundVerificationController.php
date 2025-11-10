<?php

namespace Modules\RiderBackgroundVerification\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\RiderBackgroundVerification\DataTables\RecruiterDataTable;
use Modules\Deliveryman\Entities\Deliveryman;

class RiderBackgroundVerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function recruiter_list(Request $request)
    // {
    //     $roll_type = $request->roll_type ?? '';
    
    //     $query = Deliveryman::where('delete_status', 0);
    
    //     if ($roll_type != "") {
    //         $query->where('work_type', $roll_type);
    //     }
    
    //     $lists = $query->orderBy('id','desc')->get();
    
    //     return view('riderbackgroundverification::recruiter_list', compact('lists','roll_type'));
    // }
    
    // public function recruiter_preview(Request $request,$id)
    // {

    //     $dm = Deliveryman::where('delete_status', 0)->where('id',$id)->first();
    //      if (!$dm) {
    //         return back()->with('error', 'Rider Not found');
    //     }

    //     return view('riderbackgroundverification::recruiter_preview', compact('dm'));
    // }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('riderbackgroundverification::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('riderbackgroundverification::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('riderbackgroundverification::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
