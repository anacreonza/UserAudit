<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Device;
use App\User;
use App\Client;
use App\JournalEntry;
use Session;
use Illuminate\Support\Facades\Http;

class DeviceController extends Controller
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
        $devices = [];
        foreach (Device::orderBy($sortby, $sortorder)
            ->select('devices.*','clients.name')
            ->join('clients', 'devices.assigned_user_id','=','clients.id')
            ->get() as $device) {
            \array_push($devices, $device);
        }
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
            'serial_no' => 'required|unique:devices',
            'assigned_user_id' => 'required',
        ]);
        $device = New Device;
        $device->computername = $request->computername;
        $device->serial_no = $request->serial_no;
        $device->reportjson = "none";
        $device->assigned_user_id = $request->assigned_user_id;
        $device->device_type = $request->device_type;
        $device->operating_system = $request->operating_system;
        $device->username = "None";
        $device->device_model = $request->device_model;
        $device->machine_manifest = $request->machine_manifest;
        $device->save();
        $user = Client::where('id', $device->assigned_user_id)->first();
        $user->device_id = $device->id;
        $user->save();
        $journal_entry = New JournalEntry;
        $journal_entry->journal_entry = "Allocated new PC: $device->computername";
        $journal_entry->user_id = $request->assigned_user_id;
        $journal_entry->save();
        return redirect('/device/index')->with('message', "New device $device->computername created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function view($id)
    {
        $device = Device::findOrFail($id);
        $client = Client::where('id', $device->assigned_user_id)->first();
        return view('device_view')->with('device', $device)->with('client', $client);
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
        return redirect('/');
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
        $columns = array("Computer Name", "Device Model", "Operating System", "Software Manifest", "Assigned User");
        $callback = function() use($devices, $columns){
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($devices as $device) {
                $row['Computer Name']       = $device->computername;
                $row['Device Model']        = $device->device_model;
                $row['Operating System']    = $device->operating_system;
                $row['Software Manifest']   = $device->machine_manifest;
                $assigned_user = Client::where('id', $device->assigned_user_id)->first();
                if ($assigned_user){
                    $row['Assigned User']  = $assigned_user->name;
                } else {
                    $row['Assigned User'] = "None";
                }

                fputcsv($file, array($row['Computer Name'], $row['Device Model'], $row['Operating System'], $row['Software Manifest'], $row['Assigned User']));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
    public function retrieve_mac_details($serial){
        $columns = [
            "machine.serial_number",
            "machine.hostname",
            "reportdata.timestamp",
            "reportdata.console_user",
            "machine.os_version",
        ];
        
        // Using Curl to get auth info - I cannot get Guzzle to show the CSRF token.
        $url = env('MR_URL')."/index.php?/auth/login";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "login=stuart.kinnear&password=SystemShock2022!");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        $php_session_id = substr($result, strpos($result, "PHPSESSID=")+10, 26);
        $csrf_token = substr($result, strpos($result, "CSRF-TOKEN=")+11, 40);
        // echo("PHP Session ID: $php_session_id" . "</br>");
        // echo("CSRF Token: $csrf_token");

        $headers = [
            "x-csrf-token=$csrf_token",
            "Cookie: $php_session_id"
        ];
        $response = Http::post(env("MR_URL")."/index.php?/datatables/data", [
            'headers' => $headers,
        //     'password' => 'SystemShock2022!'
        ]);
        // foreach ($response->getHeaders() as $name => $values) {
        //     echo $name . ': ' . implode(', ', $values) . "\r\n";
        // }
    }
}
