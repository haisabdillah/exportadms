<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function view(){
        return view('pages.attendance');
    }
    public function export(){
        $employee = request('employee');
        $deptId = request('department_id');
        $start= request('start');
        $end = request('end');
        $presence = DB::table('checkinout')
        ->join('userinfo','userinfo.userid','=','checkinout.userid')
        ->join('departments','departments.DeptID','userinfo.defaultdeptid')
        ->whereBetween('checkinout.checktime',[$start.' 00:00:00',$end.' 23:00:00'])
        ->when($employee,function($q) use ($employee) {
            $q->whereIn('userinfo.badgenumber',$employee);
        })
        ->orderBy('userinfo.badgenumber','asc')
        ->where('userinfo.defaultdeptid',$deptId)
        ->orderBy('checkinout.checktime','asc')
        ->select('checkinout.id','userinfo.name','departments.DeptName as department','userinfo.badgenumber','checkinout.SN',DB::raw('DATE_FORMAT(checkinout.checktime,"%Y-%m-%d") as date'),DB::raw('DATE_FORMAT(checkinout.checktime,"%H:%i") as time'))
        ->get();
        if ($presence->isEmpty()) {
            return response()->json(['errors'=>'Data Not Found'], 404);
        }
        $count = $presence->count() ;
        $data = [
            'data' => $presence
        ];
        return response()->json($data, 200);
    }
}
