<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Device;
use App\JournalEntry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Session;
use LdapRecord\Models\ActiveDirectory\User as LDAPUser;
use Config;

class ClientController extends SearchController
{
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
            $sortby = 'name';
        }
        if (! isset($sortorder)){
            $sortorder = 'asc';
        }
        $clients = Client::orderBy($sortby, $sortorder)
        ->select('clients.*', 'devices.computername')
        ->leftJoin('devices', 'clients.device_id','=','devices.id')
        ->paginate(10);

        foreach ($clients as $client){
            $last_journal_entry = JournalEntry::where('user_id', $client->id)->get()->last();
            if ($last_journal_entry){
                $client->journal_entry_preview = $last_journal_entry["journal_entry"];
                if (strlen($last_journal_entry["journal_entry"]) > 90){
                    $client->journal_entry_preview = substr($last_journal_entry['journal_entry'], 0, 80) . "...";
                }
            }
        }
        $clientcount = Client::all()->count();
        return view('client_index')->with('clientlist', $clients)->with('clientcount', $clientcount);
    }
    public function create()
    {
        $devices = [];
        foreach (Client::all() as $device){
            \array_push($devices, $device);
        }
        return view('client_create')->with('devices', $devices);
    }
    public function add_client(Request $request){
        $ldap_details = $this->get_user_details_from_ldap($request->ad_user);
        $client = New Client;
        $client->name = $ldap_details->name;
        $client->ad_user = $request->ad_user;
        $client->email = $ldap_details->email;
        $client->device_id = 0;
        $client->department = $ldap_details->department;
        $client->role = $ldap_details->title;
        $client->comments = "";
        $client->save();
        $journal_entry = New JournalEntry;
        $journal_entry->user_id = $client->id;
        if (Auth::check()) {
            $journal_entry->admin_id = Auth::id();
        }
        $journal_entry->journal_entry = "Client $request->ad_user created.";
        $journal_entry->save();
        return redirect('/client/index')->with('message', "New client $request->ad_user created.");
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ad_user' => 'required|unique:clients'
        ]);
        $client = New Client;
        $client->name = $request->name;
        $client->ad_user = $request->ad_user;
        $client->email = $request->email;
        $client->device_id = $request->device_id;
        $client->department = $request->department;
        $client->role = $request->role;
        $client->comments = $request->comment;
        $client->ww_user = $request->ww_user;
        $client->save();
        $journal_entry = New JournalEntry;
        $journal_entry->user_id = $client->id;
        if (Auth::check()) {
            $journal_entry->admin_id = Auth::id();
        }
        if ($client->device_id){
            $device = Device::where('id', $client->device_id)->first();
            $device->assigned_user_id = $client->id;
            $device->save();
        }
        $updated_fields = $this->update_client_details_from_ldap_by_id($client->id);
        $journal_entry->journal_entry = "Client $request->ad_user created.";
        $journal_entry->save();
        return redirect('/client/index')->with('message', "New client $request->ad_user created.");
    }
    public function view($username){
        $client = Client::where('ad_user', $username)->first();
        if ($client){
            $details = $this->get_user_details_from_ldap($username);
            foreach (get_object_vars($details) as $key => $value) {
                $client->$key = $value;
            }
            $journal_entries = JournalEntry::where('user_id', $client->id)->orderBy('updated_at', 'desc')->get();
            $assigned_device = Device::where('id', $client->device_id)->first();
            if ($assigned_device){
                $device = $assigned_device;
            } else {
                $device = new \stdClass();
                $device->id = 0;
                $device->computername = "None";
                $device->serial_no = "None";
                $device->device_model = "None";
                $device->operating_system = "Unknown";
            };
        } else {
            $client = $this->get_user_details_from_ldap($username);
            if ($client){
                $client->ad_user = $username;
                $journal_entries = [];
                $device = new \stdClass();
                $device->id = 0;
                $client->manager_id = 0;
                $device->computername = "None";
                $client->ww_user = 0;
                $client->comments = "";
            } else {
                Session::flash('message', 'No such user in LDAP!');
                return redirect('/client/view/' . $username);
            }
        }
        return view('client_viewer')->with('client', $client)->with('journal_entries', $journal_entries)->with('device', $device);
    }
    public function edit($id){
        $client = Client::where('id', $id)->first();
        $devices = Device::all();
        $assigned_device = Device::where('id', $client->device_id)->first();
        if ($assigned_device){
            $device_name = $assigned_device->computername;
        } else {
            $device_name = "None";
        };
        return view('client_edit')->with('client', $client)->with('devices', $devices)->with('device_name', $device_name);
    }
    public function update($id, Request $request){
        $validated = $request->validate([
            'name' => 'required'
        ]);
        $client = Client::where('id', $id)->first();
        $client->name = $request->name;
        $client->ad_user = $request->ad_user;
        $client->email = $request->email;
        $client->device_id = $request->device_id;
        $client->department = $request->department;
        $client->role = $request->role;
        $client->comments = $request->comment;
        $client->ww_user = $request->ww_user;
        $client->save();

        $device = Device::where('id', $client->device_id)->first();
        $device->assigned_user_id = $client->id;
        $device->save();
        
        return redirect("/client/view/$client->ad_user")->with('message', "Client updated.");
    }
    public function update_client_details_from_ldap_by_id($id){
        $client = Client::where('id', $id)->firstorfail();
        $ldapclient = LDAPUser::where('sAMAccountName', '=', $client->ad_user)->get();
        $updated_fields = 0;
        if ($ldapclient[0]->cn && $client->name !== $ldapclient[0]->cn[0]){
            $client->name = $ldapclient[0]->cn[0];
            $updated_fields++;
        }
        if ($ldapclient[0]->mail && $client->email !== $ldapclient[0]->mail){
            $client->email = $ldapclient[0]->mail[0];
            $updated_fields++;
        }
        if ($ldapclient[0]->department && $client->department !== $ldapclient[0]->department){
            $client->department = $ldapclient[0]->department[0];
            $updated_fields++;
        }
        if ($ldapclient[0]->title && $client->role !== $ldapclient[0]->title){
            $client->role = $ldapclient[0]->title[0];
            $updated_fields++;
        }
        $client->save();
        return $updated_fields;
    }
    public function lookup($id){
        $client = Client::where('id', $id)->firstorfail();
        $ldapclient = LDAPUser::where('sAMAccountName', '=', $client->ad_user)->get();
        $updated_fields = $this->update_client_details_from_ldap_by_id($id);
        if (!$updated_fields){
            return redirect("/client/view/$id")->with('message', "Unable to read details.");
        }
        if ($updated_fields > 0){
            return redirect("/client/view/$id")->with('message', "$updated_fields fields updated.");
        }else{
            return redirect("/client/view/$id")->with('message', "Details already up to date.");
        }
    }
    public function export_csv(){
        $filename = "Clients.csv";
        $clients = Client::all();
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => 0
        );
        $columns = array("Name", "Department", "Role", "Woodwing User", "Computer");
        $callback = function() use($clients, $columns){
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($clients as $client) {
                $row['Name']  = $client->name;
                $row['Department']    = $client->department;
                $row['Role']    = $client->role;
                $ww_user = $client->ww_user ? "Yes" : "No";
                $row['Woodwing User']  = $ww_user;
                $assigned_device = Device::where('id', $client->device_id)->first();
                if ($assigned_device){
                    $device_name = $assigned_device->computername;
                } else {
                    $device_name = "None";
                };
                $row['Computer']  = $device_name;

                fputcsv($file, array($row['Name'], $row['Department'], $row['Role'], $row['Woodwing User'], $row['Computer']));
            }

            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
    public function delete($id){
        $user = Client::where('id', $id)->firstorfail()->delete() ;
        Session::flash('message', 'Client deleted!');
        return redirect('/client/index/');
    }
}
