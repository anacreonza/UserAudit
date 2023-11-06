<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Report;
use App\User;
use App\Device;
use Illuminate\Support\Facades\Auth;
use Session;
use Carbon\Carbon;

class ReportController extends SearchController
{
    public function __construct() {
        $this->middleware('auth');
    }
    public function create(){
        return view('report_create');
    }
    public function read($id){
        $report = Report::where('id', $id)->first();
        $report_data = \json_decode($report->report_data);
        $report->html_report = $this->printTree($report_data);
        return view('report_viewer')->with('report', $report);
    }
    public function store(Request $request){
        $report = new Report;
        $report->created_by = Auth::id();
        $report->report_name = $request->report_name;
        $report->endpoint = $request->endpoint;
        $report->system = $request->system;
        $report->software_id = $request->software_id;
        $report->items_per_page = $request->items_per_page;
        $report->save();
        Session::flash('message', "Report Created");
        return redirect('/report/index');
    }
    public function index(){
        $reports = [];
        foreach (Report::all() as $report) {
            \array_push($reports, $report);
        }
        return view('report_index')->with('reports', $reports);
    }
    public function edit($id){
        $report = Report::findOrFail($id);
        return view('report_edit')->with('report', $report);
    }
    public function run_report(Request $request, $id){
        $report = Report::where('id', $id)->first();
        $url = $report->endpoint . "?swid=" . $report->software_id;
        $result = $this->make_manage_engine_request($request, $url, $report->items_per_page);
        if ($result->status == "success"){
            $response = $result->message_response;
            foreach ($response->computers as $computer){
                $epoch = substr($computer->last_successful_scan, 0, -3);
                $dt = new \DateTime("@$epoch");
                $date = $dt->format('Y-m-d H:i:s');
                $cdate = Carbon::createFromDate($date);
                $relative_date = $cdate->diffForHumans();
                $computer->human_readable_last_scan_date = $date;
                $computer->relative_last_scan_date = $relative_date;
            }
            return view('report_result_view')->with('response', $response)->with('report', $report);
        } else {
            Session::flash('message', 'Unable to generate report');
            return redirect("/reports/index");
        }
    }
    public function update(Request $request, $id)
    {
        $report = Report::where('id', $id)->first();
        $report->report_name = $request->report_name;
        $report->endpoint = $request->endpoint;
        $report->software_id = $request->software_id;
        $report->items_per_page = $request->items_per_page;
        $report->save();
        return redirect("/report/index")->with('message', "Report updated.");
    }
    public function delete($id)
    {
        $report = Report::where('id', $id)->firstorfail()->delete();
        Session::flash('message', 'Report Deleted!');
        return redirect('/report/index');
    }
    public function cs_users(){
        
    }
}
