<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Rules\Password;
use App\Models\BusinessSetting;

class TicketModuleController extends Controller
{
    public function login(Request $request){
        return view('auth::ticket-portal-login');
    }
}