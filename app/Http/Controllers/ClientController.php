<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Device;
use App\JournalEntry;
use Illuminate\Support\Facades\Auth;
use Session;
use LdapRecord\Models\ActiveDirectory\User as LDAPUser;

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
            $client->last_journal_entry = JournalEntry::where('user_id', $client->id)->get()->last();
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
            'name' => 'required|unique:clients',
            'ad_user' => 'required|unique:clients',
            'email' => 'required|unique:clients',
        ]);
        $client = New Client;
        $client->name = $request->name;
        $client->ad_user = $request->ad_user;
        $client->email = $request->email;
        $client->device_id = $request->device_id;
        $client->department = $request->department;
        $client->role = $request->role;
        $client->comments = $request->comment;
        $client->save();
        $journal_entry = New JournalEntry;
        $journal_entry->user_id = $client->id;
        if (Auth::check()) {
            $journal_entry->admin_id = Auth::id();
        }
        $journal_entry->journal_entry = "Client $request->name created.";
        $journal_entry->save();
        return redirect('/client/index')->with('message', "New client $request->name created.");
    }
    public function view($id){
        $client = Client::where('id', $id)->first();
        $journal_entries = JournalEntry::where('user_id', $id)->get();
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
        $adname = $client->ad_user;
        $ldapclient = LDAPUser::where('sAMAccountName', '=', $client->ad_user)->get();
        if ($ldapclient->count()){
            $client->manager = $ldapclient[0]->manager[0];
            $str_end = strpos($client->manager, ",OU");
            $client->manager = \substr($client->manager, 3, $str_end-3);
            $client->email = $ldapclient[0]->mail[0];
            $client->mobile = $ldapclient[0]->mobile[0];
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
            'name' => 'required',
            'email' => 'required',
        ]);
        $client = Client::where('id', $id)->first();
        $client->name = $request->name;
        $client->ad_user = $request->ad_user;
        $client->email = $request->email;
        $client->device_id = $request->device_id;
        $client->department = $request->department;
        $client->role = $request->role;
        $client->comments = $request->comment;
        $client->save();
        return redirect("/client/view/$id")->with('message', "Client updated.");
    }
    public function lookup($id){
        $client = Client::where('id', $id)->firstorfail();
        $ldapclient = LDAPUser::where('sAMAccountName', '=', $client->ad_user)->get();

        // $details = (object)[];
        // $details->company = $ldapclient[0]->company[0];
        // $details->manager = $ldapclient[0]->manager[0];
        // $details->mobile = $ldapclient[0]->mobile[0];

        if (!$ldapclient->count()){
            return redirect("/client/view/$id")->with('message', "Unable to read details.");
        }

        $updated_fields = 0;
        if ($ldapclient[0]->cn){
            $client->name = $ldapclient[0]->cn[0];
            $updated_fields++;
        }
        if ($ldapclient[0]->mail){
            $client->email = $ldapclient[0]->mail[0];
            $updated_fields++;
        }
        if ($ldapclient[0]->department){
            $client->department = $ldapclient[0]->department[0];
            $updated_fields++;
        }
        if ($ldapclient[0]->title){
            $client->role = $ldapclient[0]->title[0];
            $updated_fields++;
        }
        $client->save();
        return redirect("/client/view/$id")->with('message', "$updated_fields fields updated.");
    }
    public function delete($id){
        $user = Client::where('id', $id)->firstorfail()->delete();
        Session::flash('message', 'Client deleted!');
        return redirect('/client/index/');
    }
}
