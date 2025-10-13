<?php

namespace Modules\B2BAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class B2BTicketController extends Controller
{
    public function list()
    {
        
        return view('b2badmin::ticket.list');
    }
    
    
    public function ticket_view()
    {
       
        return view('b2badmin::ticket.view');
    }
    
    public function update_ticket_status($id)
    {
       
        // return view('b2badmin::ticket.view');
    }

}
