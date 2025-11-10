<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class B2BReportController extends Controller
{
    public function list()
    {
        
        return view('b2badmin::report.list');
    }
    
    
    public function view()
    {
       
        return view('b2badmin::report.list');
    }


}
