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

class ClientController extends Controller
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
        $clientlist = [];
        foreach (Client::orderBy($sortby, $sortorder)->get() as $client) {
            $last_journal_entry = JournalEntry::where('user_id', $client->id)->get()->last();
            if (strlen($last_journal_entry['journal_entry']) > 100){
                $client->journal_entry_preview = substr($last_journal_entry['journal_entry'], 0, 95) . "...";
            } else {
                $client->journal_entry_preview = $last_journal_entry['journal_entry'];
            }
            $assigned_device = Device::where('id', $client->device_id)->first();
            if (isset($assigned_device->computername)){
                $client->assigned_device_name = $assigned_device->computername;
            } else {
                $client->assigned_device_name = "None";
            }
            \array_push($clientlist, $client);
        }
        $clientcount = Client::all()->count();
        return view('client_index')->with('clientlist', $clientlist)->with('clientcount', $clientcount);
    }
    public function create()
    {
        $devices = [];
        foreach (Client::all() as $device){
            \array_push($devices, $device);
        }
        return view('client_create')->with('devices', $devices);
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
            $client = new \stdClass();
            $client->ad_user = $username;
            $journal_entries = [];
            $device = new \stdClass();
            $device->id = 0;
            $client->manager_id = 0;
            $device->computername = "None";
            $client->ww_user = 0;
            $client->comments = "";
        }
        $ldapclient = LDAPUser::where('sAMAccountName', '=', $username)->get();
        if ($ldapclient->count()){
            $client->name = $ldapclient[0]->cn[0];
            $client->title = $ldapclient[0]->title[0];
            $client->description = $ldapclient[0]->description[0];
            $client->location = $ldapclient[0]->l[0];
            $client->city = $ldapclient[0]->st[0];
            $client->country = $ldapclient[0]->co[0];
            $client->email = $ldapclient[0]->mail[0];
            $client->department = $ldapclient[0]->department[0];
            $client->physicaladdress = $ldapclient[0]->physicaldeliveryofficename[0];
            $client->streetaddress = $ldapclient[0]->streetaddress[0];
            $client->mobile = $ldapclient[0]->mobile[0];
            $client->company = $ldapclient[0]->company[0];
            $client->manager = $ldapclient[0]->manager[0];
            $str_end = strpos($client->manager, ",OU");
            $client->manager = \substr($client->manager, 3, $str_end-3);
            $manager = Client::where('name', $client->manager)->first();
            if ($manager){
                $client->manager_id = $manager->id;
                $client->manager_ad_username = $manager->ad_user;
            }
            $client->memberof = $ldapclient[0]->memberof;
            $aliases = [];
            foreach ($ldapclient[0]->proxyaddresses as $proxyaddress){
                if (! strpos($proxyaddress, "@")){
                    continue;
                }
                // if (! strpos($proxyaddress, "SMTP:")){
                //     continue;
                // }
                if (strpos($proxyaddress, "onmicrosoft.com")){
                    continue;
                }
                if (strpos($proxyaddress, "m24.media24.com")){
                    continue;
                }
                if (strpos($proxyaddress, "IP:")){
                    continue;
                }
                if (strpos($proxyaddress, "ip:")){
                    continue;
                }
                $proxyaddress = str_ireplace("SMTP:", "", $proxyaddress);
                $aliases[] = $proxyaddress;
            }                
            $client->aliases = $aliases;
            $directreports = $ldapclient[0]->directreports;
            $reports = [];
            if (isset($directreports)){
                foreach ($directreports as $directreport){
                    $entries = explode(",", $directreport);
                    $report = $entries[0];
                    $report = str_ireplace("CN=", "", $report);
                    $reports[] = $report;
                }
            }
            $client->directreports = $reports;
            $client->employeetype = $ldapclient[0]->employeetype[0];
            $client->badpwcount = $ldapclient[0]->badpwcount[0];
            $client->lockouttime = $ldapclient[0]->lockouttime;
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
