<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Report;
use App\User;
use App\Device;
use Session;

class ReportController extends Controller
{
    public function read($id){
        $report = Report::where('id', $id)->first();
        $report_data = \json_decode($report->report_data);
        $report->html_report = $this->printTree($report_data);
        return view('report_viewer')->with('report', $report);
    }
    function printTree($array) {
        $output = "\t";
        foreach ($array as $key=>$value) {
            if (\is_string($key)){
                $output .= "<div class='report_grid_row'><div class=\"report_key_item\"><b>".$key.": </b></div>";
            } else {
                $output .= "<div class='report_grid_row'><div class=\"report_key_item\"><b>"."</b></div>";
            }
            if (\is_string($value)){
                $output .= "<div>" . $value . "</div>";
            }
            if (\is_object($value)){
                $output .= "</div><div>" . $this->printTree($value) . "</div><div>";
            }
            if (\is_array($value)){
                $output .= "</div><div>" . $this->printTree($value) . "</div><div>";
            }
            $output .= "</div>\n";
            
        }
        // $output .= "</div>";
        return $output;
    }
    public function store(Request $request){
        $report = new Report;
        $report_data = $request->json()->all();
        $report->report_data = json_encode($report_data);
        $report->user_name = $report_data['UserName'];
        $report->computer_name = $report_data['ComputerName'];
        $report->save();
        $device_exists = Device::where('computer_name', $report->computer_name)->first();
        if (! $device_exists ){
            $device = new Device;
            $device->username = $report->user_name;
            $device->computername = $report->computer_name;
            $device->reportjson = "none";
            $device->save();
        }
        
    }
    public function index(){
        $reports = [];
        foreach (Report::all() as $report) {
            \array_push($reports, $report);
        }
        return view('report_index')->with('reports', $reports);
    }
    public function delete($id)
    {
        $report = Report::where('id', $id)->firstorfail()->delete();
        Session::flash('message', 'Report Deleted!');
        return redirect('/report/index/');
    }
    public function cs_users(){
        
    }
}
