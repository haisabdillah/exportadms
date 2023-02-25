<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use PDF;
use Illuminate\Support\Facades\Validator;

class ExportController extends Controller
{
    public function view(){
        return view('pages.export');
    }
    public function export(){
        $validator = Validator::make(request()->all(), [
            'employee' => 'nullable|array',
            'department_id' => 'required|exists:departments,DeptID',
            'start' => 'required|date_format:Y-m-d',
            'end' => 'required|date_format:Y-m-d',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => 'Bad Request'], 400);
        }
        $employee = request('employee');
        $deptId = request('department_id');
        $start= request('start');
        $end = request('end');
        try {
            $dateRange = \Carbon\CarbonPeriod::create($start,$end);
            $count = 0;
            $subCount = 0;
            $tempDateRange=[];
            foreach ($dateRange as $key => $value) {
                $tempDateRange[$subCount][] = $value;
                if ($count == 12) {
                    $count = 0;
                    $subCount++;
                }
                else {
                    $count++;
                }
            }
            $dateRange = $tempDateRange;

            $department = DB::table('departments')->where('DeptID',$deptId)->first()->DeptName ?? null;
            $presence = DB::table('checkinout')->join('userinfo','userinfo.userid','=','checkinout.userid')
            ->whereBetween('checkinout.checktime',[$start.' 00:00:00',$end.' 23:00:00'])
            ->when($employee,function($q) use ($employee) {
                $q->whereIn('userinfo.badgenumber',$employee);
            })
            ->orderBy('userinfo.badgenumber','asc')
            ->where('userinfo.defaultdeptid',$deptId)
            ->orderBy('checkinout.checktime','asc')
            ->select('checkinout.id','userinfo.name','userinfo.badgenumber','checkinout.SN',DB::raw('DATE_FORMAT(checkinout.checktime,"%Y-%m-%d") as date'),DB::raw('DATE_FORMAT(checkinout.checktime,"%H:%i") as time'))
            ->get()->groupBy('badgenumber');
            if ($presence->isEmpty()) {
                return response()->json(['errors'=>'Data Not Found'], 404);
            }
            $count = $presence->count() ;
            $data = [
                'department' => $department,
                'date_from' => $start,
                'date_until' => $end,
                'date_range' => $dateRange,
                'count' => $count,
                'data' => $presence
            ];
            $pdf = PDF::loadview('pdf.export-absent',$data)->setPaper('a4', 'landscape');
            return $pdf->stream('asdasd.pdf',['attachment; filename="my_filename.txt"']);
        } catch (\Throwable $th) {
           return response()->json(['error' => $th->getMessages()], 500);
        }

    }

    public function getDepartment(){
        $id = request('q');
        $data = DB::table('departments')
                ->when($id,function($q) use ($id){
                    $q->where('departments.DeptName','like','%'.$id.'%');
                })
                ->select('DeptID as id','DeptName as text')->get();
        return response()->json($data, 200);
    }

    public function getEmployee(){
        $deptId = request('department_id');
        $search = request('search');
        $data = DB::table('userinfo')
                ->join('departments','departments.DeptID','userinfo.defaultdeptid')
                ->where('departments.DeptID',$deptId)
                ->when($search,function($q) use ($search){
                    $q->where('userinfo.badgenumber','like','%'.$search.'%');
                    $q->orWhere('userinfo.name','like','%'.$search.'%');
                })
               ->select('badgenumber as id',DB::raw("CONCAT(userinfo.badgenumber,' - ',userinfo.name) AS text"))->get();
        return response()->json($data, 200);
    }

    public function getData(){
        $data = DB::table('checkinout')
                ->where('checktime','>=','2023-02-19 09:00:00')
                ->get();
        // foreach ($data as $key => $value) {
        //          DB::table('checkinout')
        //             ->where('id',$value->id)
        //             ->update(['checktime' => \Carbon\Carbon::parse($value->checktime)->subHours('15')->format('Y-m-d H:i:s')]);
        //  }
        //return 'oke';
        return response()->json($data, 200);
    }
}
