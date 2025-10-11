<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class B2BDashboardTicketController extends Controller
{
    
     public function list(Request $request)
    {
    
        return view('b2badmin::dashboard_ticket.list');
    }

}
