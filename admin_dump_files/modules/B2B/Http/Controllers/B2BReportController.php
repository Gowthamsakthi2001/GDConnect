<?php

namespace Modules\B2B\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class B2BReportController extends Controller
{

    public function index()
    {
        return view('b2b::reports.index');
    }
    
    public function vehicle_usage()
    {
        return view('b2b::reports.vehicle_usage');
    }
    
    
    

    
    
    
   
    
}
