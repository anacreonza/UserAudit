<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Device;
use App\JournalEntry;
use App\Report;
use Session;

class SearchController extends Controller
{
    public function filter_clients(Request $request, Client $client){
        if ($request->has('searchterm')){
            $searched_text = $request->searchterm;
            $foundclients = $client->where('name', 'like', '%'.$searched_text.'%')->get();
            $clientcount = count($foundclients);
            if (count($foundclients) == 0){
                Session::flash('message', 'No results found!');
                return redirect("/client/index/");
            }
            if (count($foundclients) == 1){
                foreach($foundclients as $foundclient){
                    return redirect("/client/view/$foundclient->id");
                }
            } else {
                return view('client_index')->with('clientlist', $foundclients)->with('clientcount', $clientcount);
            }
        }
    }
    public function filter_devices(Request $request, Device $device){
        if ($request->has('searchterm')){
            $searched_text = $request->searchterm;
            $founddevices = $device
            ->where('computername', 'like', '%'.$searched_text.'%')
            ->orWhere('device_model', 'like', '%'.$searched_text.'%')
            ->orWhere('device_type', 'like', '%'.$searched_text.'%')
            ->orWhere('serial_no', 'like', '%'.$searched_text.'%')
            ->orWhere('operating_system', 'like', '%'.$searched_text.'%')
            ->get();
            $device_count = count($founddevices);
            if (count($founddevices) == 0){
                Session::flash('message', 'No results found!');
                return redirect("/device/index/");
            }
            if (count($founddevices) == 1){
                foreach($founddevices as $founddevice){
                    return redirect("/device/view/$founddevice->id");
                }
            } else {
                return view('device_index')->with('devices', $founddevices)->with('device_count', $device_count);
            }
        }
    }
    public function filter_journalentries(Request $request, JournalEntry $journalentrydevice){
        if ($request->has('searchterm')){
            $searched_text = $request->searchterm;
            $foundjournalentries = $journalentry
            ->where('journal_entry', 'like', '%'.$searched_text.'%')
            ->get();
            $entry_count = count($foundjournalentries);
            if (count($foundjournalentries) == 0){
                Session::flash('message', 'No results found!');
                return redirect("/journal_entry/index/");
            }
            if (count($foundjournalentries) == 1){
                foreach($foundjournalentries as $foundjournalentry){
                    return redirect("/journal_entry/view/$foundjournalentry->id");
                }
            } else {
                return view('journal_entry/index')->with('journal_entries', $foundjournalentries);
            }
        }
    }
}
