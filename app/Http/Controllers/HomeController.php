<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function view(){
        return view('pages.home');
    }
    public function devices(){
        $data = \DB::table('iclock')->select('SN','LastActivity','DeviceName','Alias')->get();
        return response()->json(['data'=> $data], 200);
    }
}
