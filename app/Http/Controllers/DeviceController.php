<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Device;
use App\User;
use App\Client;
use App\JournalEntry;
use Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use guzzlehttp\guzzle;
use Config;
use Carbon\Carbon;

class DeviceController extends SearchController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
    }
    public function index(Request $request)
    {
        if ($request->query('sortby')){
            if ($request->query('sortby') != $request->session()->get('sortby')){
                $request->session()->forget('sortorder');
            }
            $sortby = $request->query('sortby');
            $request->session()->put('sortby', $sortby);
            if ($request->session()->get('sortorder')){
                if ($request->session()->get('sortorder') == 'asc'){
                    $sortorder = 'desc';
                } elseif ($request->session()->get('sortorder') == 'desc'){
                    $sortorder = 'asc';
                }
                $request->session()->put('sortorder', $sortorder);
            } else {
                $request->session()->put('sortorder', 'asc');
            }
        }
        if ($request->session()->get('sortby')){
            $sortby = $request->session()->get('sortby');
        };
        if (! isset($sortby)){
            $sortby = 'computername';
        }
        if (! isset($sortorder)){
            $sortorder = 'asc';
        }
        $devices = Device::orderBy($sortby, $sortorder)
            ->select('devices.*','clients.name', 'clients.ad_user')
            ->leftJoin('clients', 'devices.assigned_user_id','=','clients.id')
            ->paginate(10);
        $device_count = Device::all()->count();
        return view('device_index')->with('devices', $devices)->with('device_count', $device_count);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $users = Client::orderBy('name')->get();
        return view('device_create')->with('users', $users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'computername' => 'required|unique:devices',
            'assigned_user_id' => 'required',
        ]);
        $device = New Device;
        $device->computername = $request->computername;
        $device->assigned_user_id = $request->assigned_user_id;
        if (isset($device->serial_no)){
            $device->serial_no = $request->serial_no;
        }
        $device->reportjson = "none";
        $device->username = "None";
        if (isset($device->device_type)){
            $device->device_type = $request->device_type;
        }
        if (isset($device->operating_system)){
            $device->operating_system = $request->operating_system;
        }
        if (isset($device->device_model)){
            $device->device_model = $request->device_model;
        }
        if (isset($device->machine_manifest)){
            $device->machine_manifest = $request->machine_manifest;
        }
        $device->save();
        if ($device->assigned_user_id != 0){
            $user = Client::where('id', $device->assigned_user_id)->first();
            $user->device_id = $device->id;
            $user->save();
            $journal_entry = New JournalEntry;
            $journal_entry->journal_entry = "Allocated new PC: $device->computername";
            $journal_entry->user_id = $request->assigned_user_id;
            $journal_entry->admin_id = Auth::id();
            $journal_entry->save();
        }
        return redirect('/device/index')->with('message', "New device $device->computername created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request, $devicename)
    {
        if ($device = Device::where('computername', $devicename)->first()){
            $client = Client::where('id', $device->assigned_user_id)->first();
            if (! isset($client)){
                $client = new \stdClass();
                $client->ad_user = "None";
                $client->name = "None";
            }
        } else {
            $client = new \stdClass();
            $client->ad_user = "None";
            $client->name = "None";
        }
        $resource_id = $this->get_manage_engine_resource_id($request, $devicename);
        if($resource_id){
            $device_details = $this->get_device_details_from_manage_engine($request, $resource_id);
            $device_details->software = $this->get_softwarelist_from_manage_engine($request, $devicename);
            $device_details->software_count = count($device_details->software);
            $scancomputers = $this->get_me_device_by_computername($request, $devicename);
            $device_details->scancomputer = $scancomputers->scancomputers[0];
            $epoch = substr($device_details->scancomputer->last_successful_scan, 0, -3);
            $dt = new \DateTime("@$epoch");
            $date = $dt->format('Y-m-d H:i:s');
            $cdate = Carbon::createFromDate($date);
            $relative_date = $cdate->diffForHumans();
            $device_details->last_scan_ago = $relative_date;
            if ($client->ad_user == "None"){
                $client->ad_user = $device_details->scancomputer->agent_logged_on_users;
            };
            return view('device_view')->with('device_details', $device_details)->with('client', $client);
        } else {
            return redirect("/device/index")->with("message", "Unable to retrieve device details from Manage Engine");
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $device = Device::findOrFail($id);
        $clients = Client::orderBy('name')->get();
        return view('device_edit')->with('device', $device)->with('clients', $clients)->with('message', "Device $device->computername updated.");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $device = Device::where('id', $id)->first();
        $device->assigned_user_id = $request->assigned_user_id;
        $device->device_model = $request->device_model;
        $device->serial_no = $request->serial_no;
        $device->operating_system = $request->operating_system;
        $device->machine_manifest = $request->device_manifest;
        // $device->comments = $request->comments;
        $device->save();
        return redirect("/device/view/$id")->with('message', "Device updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $device = Device::where('id', $id)->firstorfail()->delete();
        Session::flash('message', 'Device Deleted!');
        return redirect('/device/index');
    }
    public function export_csv(){
        $filename = "Devices.csv";
        $devices = Device::all();
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => 0
        );
        $columns = array("Computer Name", "Device Type", "Device Manufacturer", "Device Model", "Operating System", "Operating System Version", "Memory", "Disk Total Size", "Disk Percent Free", "Software Manifest", "Assigned User");
        $callback = function() use($devices, $columns){
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($devices as $device) {
                $row['Computer Name']               = $device->computername;
                $row['Device Type']                 = $device->device_type;
                $row['Device Manufacturer']         = $device->device_manufacturer;
                $row['Device Model']                = $device->device_model;
                $row['Operating System']            = $device->operating_system;
                $row['Operating System Version']    = $device->os_version;
                $row['RAM']                         = $device->ram;
                $row['Disk Total Size']             = $device->disk_total_size;
                $row['Disk Percent Free']           = $device->disk_percent_free;
                $row['Software Manifest']           = $device->machine_manifest;
                $assigned_user = Client::where('id', $device->assigned_user_id)->first();
                if ($assigned_user){
                    $row['Assigned User']  = $assigned_user->name;
                } else {
                    $row['Assigned User'] = "None";
                }

                fputcsv($file, array($row['Computer Name'], $row['Device Type'], $row['Device Manufacturer'], $row['Device Model'], $row['Operating System'], $row['Operating System Version'], $row['RAM'], $row['Disk Total Size'], $row['Disk Percent Free'],  $row['Software Manifest'], $row['Assigned User']));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
