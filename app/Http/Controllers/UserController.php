<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Device;
use App\JournalEntry;
use Session;

class UserController extends Controller
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
        $userlist = [];
        foreach (User::orderBy($sortby, $sortorder)->get() as $user) {
            \array_push($userlist, $user);
        }
        $usercount = User::all()->count();
        return view('user_index')->with('userlist', $userlist)->with('usercount', $usercount);
    }
    public function create()
    {
        $devices = [];
        foreach (Device::all() as $device){
            \array_push($devices, $device);
        }
        return view('user_create')->with('devices', $devices);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:users',
            'ad_user' => 'required|unique:users',
            'email' => 'required|unique:users',
            'role' => 'required',
        ]);
        $user = New User;
        $user->name = $request->name;
        $user->ad_user = $request->ad_user;
        $user->email = $request->email;
        $user->password = "password";
        $user->device_id = $request->device_id;
        $user->department = $request->department;
        $user->role = $request->role;
        $user->comments = $request->comment;
        $user->save();
        $journal_entry = New JournalEntry;
        $journal_entry->user_id = $user->id;
        $journal_entry->journal_entry = "User $request->name created.";
        $journal_entry->save();
        return redirect('/user/index')->with('message', "New user $request->name created.");
    }
    public function view($id){
        $user = User::where('id', $id)->first();
        $journal_entries = JournalEntry::where('user_id', $id)->get();
        $assigned_device = Device::where('id', $user->device_id)->first();
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
        return view('user_viewer')->with('user', $user)->with('journal_entries', $journal_entries)->with('device', $device);
    }
    public function edit($id){
        $user = User::where('id', $id)->first();
        $devices = Device::all();
        $assigned_device = Device::where('id', $user->device_id)->first();
        if ($assigned_device){
            $device_name = $assigned_device->computername;
        } else {
            $device_name = "None";
        };
        return view('user_edit')->with('user', $user)->with('devices', $devices)->with('device_name', $device_name);
    }
    public function update($id, Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required',
        ]);
        $user = User::where('id', $id)->first();
        $user->name = $request->name;
        $user->ad_user = $request->ad_user;
        $user->email = $request->email;
        $user->password = "password";
        $user->device_id = $request->device_id;
        $user->department = $request->department;
        $user->role = $request->role;
        $user->comments = $request->comment;
        $user->save();
        return redirect("/user/view/$id")->with('message', "User updated.");
    }
    public function delete($id){
        $user = User::where('id', $id)->firstorfail()->delete();
        Session::flash('message', 'User deleted!');
        return redirect('/user/index/');
    }
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
