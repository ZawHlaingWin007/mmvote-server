<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{

    protected  $candidaterepo;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {        
        if(Auth::user()->type=='admin' || Auth::user()->type=='superadmin'){
            return redirect()->route('admin.election.index');
        }elseif(Auth::user()->type=='generator'){
            return redirect()->route('generator.index');
        }
    }
}
