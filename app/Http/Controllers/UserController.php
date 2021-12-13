<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Device;
use Session;

class UserController extends Controller
{
    public function index()
    {
        $userlist = [];
        foreach (User::all() as $user) {
            \array_push($userlist, $user);
        }
        return view('user_index')->with('userlist', $userlist);
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
        $user = New User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = "password";
        $user->device_id = $request->device_id;
        $user->department = $request->department;
        $user->role = $request->role;
        $user->save();
        return redirect('/user/index')->with('message', "New user created.");
    }
    public function view($id){
        $user = User::where('id', $id)->first();
        return view('user_viewer')->with('user', $user);
    }
    public function delete($id){
        $user = User::where('id', $id)->firstorfail()->delete();
        Session::flash('message', 'User deleted!');
        return redirect('/user/index/');
    }
}
