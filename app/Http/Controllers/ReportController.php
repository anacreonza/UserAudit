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
        $report->report_type = $request->report_type;
        $report->software_id = $request->software_id;
        $report->software_name = $request->software_name;
        $report->items_per_page = $request->items_per_page;
        $report->software_manufacturer = $request->software_manufacturer;
        $report->endpoint = $this->generate_query_url($report);
        $report->save();
        Session::flash('message', "Report Created");
        return redirect('/report/index');
    }
    public function index(Request $request){
        if ($request->query('reports_sortby')){
            if ($request->query('reports_sortby') != $request->session()->get('reports_sortby')){
                $request->session()->forget('reports_sortorder');
            }
            $sortby = $request->query('reports_sortby');
            $request->session()->put('reports_sortby', $sortby);
            if ($request->session()->get('reports_sortorder')){
                if ($request->session()->get('reports_sortorder') == 'asc'){
                    $sortorder = 'desc';
                } elseif ($request->session()->get('reports_sortorder') == 'desc'){
                    $sortorder = 'asc';
                }
                $request->session()->put('reports_sortorder', $sortorder);
            } else {
                $request->session()->put('reports_sortorder', 'asc');
            }
        }
        if ($request->session()->get('reports_sortby')){
            $sortby = $request->session()->get('reports_sortby');
        };
        if (! isset($sortby)){
            $sortby = 'report_name';
        }
        if (! isset($sortorder)){
            $sortorder = 'asc';
        }
        $reports = Report::orderBy($sortby, $sortorder)->paginate(10);
        // $reports = [];
        // foreach (Report::all() as $report) {
        //     \array_push($reports, $report);
        // }
        return view('report_index')->with('reports', $reports);
    }
    public function edit($id){
        $report = Report::findOrFail($id);
        return view('report_edit')->with('report', $report);
    }
    private function get_computers_by_software_ids(Request $request, $report){
        if (\gettype($report->software_ids) == "array"){
            foreach ($ids as $id){
                $result = $this->make_manage_engine_request($request, $report->endpoint, $report->items_per_page);
            }
        }

        $result = $this->make_manage_engine_request($request, $report->endpoint, $report->items_per_page);
        if ($result->status == "success"){
            $response = $result->message_response;
            // dd($response);
            foreach ($response->computers as $computer){
                $computer->relative_last_scan_date = $this->generate_relative_date($computer->last_successful_scan);
            }
        }
        return $response;
    }
    public function run_computers_by_software_id_report(Request $request){
        $report = new Report;
        $report->created_by = Auth::id();
        $report->report_type = "me_devices_by_software_id";
        $report->software_id = $request->input('software_id');
        $report->software_name = $request->input('software_name');
        $report->software_version = $request->input('software_version');
        $report->report_name = "Computers with " . $request->input('software_name') . ", Version " . $request->input('software_version');
        $report->items_per_page = "500";
        $report->software_manufacturer = "";
        $report->endpoint = $this->generate_query_url($report);
        $result = $this->get_computers_by_software_ids($request, $report);
        $result->report = $report;
        return view('report_result_view')->with('result', $result);
    }
    private function generate_relative_date($input_date){
        $epoch = substr($input_date, 0, -3);
        $dt = new \DateTime("@$epoch");
        $date = $dt->format('Y-m-d H:i:s');
        $cdate = Carbon::createFromDate($date);
        $relative_date = $cdate->diffForHumans();
        return $relative_date;
    }
    public function run_report(Request $request, $id){
        $report = Report::where('id', $id)->first();
        $response = $this->make_manage_engine_request($request, $report->endpoint, $report->items_per_page);
        if ($response->status == "success"){
            $result = $response->message_response;
            $packages = $result->software;
            $software_name = $report->software_name;
            $manufacterer_name = $report->manufacturer_name;
            $filtered_software = [];
            $installs_total = 0;
            foreach($packages as $package){
                $manufacturer_found = stripos($package->manufacturer_name, $report->software_manufacturer);
                $software_found = stripos($package->display_name, $software_name);
                if ($report->software_manufacturer == "all"){
                    $manufacturer_found = true;
                }
                if (
                    $software_found !== false &&
                    $manufacturer_found !== false 
                ){
                    $filtered_software[] = $package;
                    $installs_total += $package->network_installations;
                }
            }
            $result->filtered_software = $filtered_software;
            $result->installs_total = $installs_total;
            $result->total = count($filtered_software);
            if ($report->report_type == "me_software_by_software_name"){
                // dd($response);
                foreach ($result->software as $package){
                    $package->human_readable_detected_time = $this->generate_relative_date($package->detected_time);
                };
                $report->count = $result->total;
                $report->save();
            } else {
                foreach ($result->computers as $computer){
                    $computer->relative_last_scan_date = $this->generate_relative_date($computer->last_successful_scan);
                }
                $chosen_sort_criterion = $request->input('sortby');
                if ($chosen_sort_criterion){
                    $resource_name = array_column($result->computers, $chosen_sort_criterion);
                    array_multisort($resource_name, SORT_ASC, $result->computers);
                } else {
                    // Default sorting order - by most recent scan
                    $last_successful_scan_column = array_column($result->computers, "report_name");
                    array_multisort($last_successful_scan_column, SORT_DESC, $result->computers);
                }
                $report->count = $response->total;
                $report->save();
            }
            $result->report = $report;
            return $result;
        } else {
            Session::flash('message', 'Unable to generate report');
            return redirect("/reports/index");
        }
    }
    public function run_single_report(Request $request, $id){
        $result = $this->run_report($request, $id);
        return view('report_result_view')->with('result', $result);
    }
    public function run_multi_report(){

    }
    private function generate_query_url($report){
        switch ($report->report_type) {
            case 'me_devices_by_software_id':
                $url = "/api/1.4/inventory/computers?swid=" . $report->software_id;
                break;
            
            case 'me_software_by_software_name':
                $url = "/api/1.4/inventory/software?searchtype=software_name&searchcolumn=software_name&searchvalue=" . htmlentities($report->software_name);
                break;
            
            default:
                $url = "/api/1.4/inventory/computers?swid=" . $report->software_id;
                break;
        }
        return $url;
    }
    public function update(Request $request, $id)
    {
        $report = Report::where('id', $id)->first();
        $report->report_name = $request->report_name;
        $report->report_type = $request->report_type;
        $report->software_id = $request->software_id;
        $report->software_name = $request->software_name;
        $report->software_manufacturer = $request->software_manufacturer;
        $report->endpoint = $this->generate_query_url($report);
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
