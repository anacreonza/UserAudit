<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Device;
use App\User;
use App\Client;
use App\JournalEntry;
use App\Report;
use Session;
use LdapRecord\Models\ActiveDirectory\User as LDAPUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use guzzlehttp\guzzle;
use Config;
use Carbon\Carbon;

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
                    return redirect("/client/view/$foundclient->ad_user");
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
                    return redirect("/device/view/$founddevice->computername");
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
    public function lookup(Request $request){
        return view('lookup');
    }
    public function authenticate_to_me(Request $request){
        $me_token = $request->session()->get('me_token');
        if (!$me_token){
            $me_token = env("ME_TOKEN");
            $request->session()->put('me_token', $me_token);
        }
        return $me_token;
    }
    public function get_manage_engine_resource_id(Request $request, $computer_name){
        $token = $this->authenticate_to_me($request);
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => $token
        ];
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $server = env('ME_SERVER_URL');
        $port = env('ME_SERVER_PORT');
        $url = $server . ":" . $port . "/api/1.4/inventory/scancomputers?searchtype=resource_name&searchcolumn=resource_name&searchvalue=" . $computer_name;
        $response = $client->get($url, [
            'headers' => $headers,
        ]);
        $response_data = $response->getBody();
        $data = json_decode($response_data);
        if ($data->status == "success"){
            if (sizeof($data->message_response->scancomputers) === 0){
                return 0;
            }
            $computer_details = $data->message_response->scancomputers[0];
            $details = [];
            $details["device_name"] = $computer_details->{'managedcomputer.friendly_name'};
            $details["res_id"] = $computer_details->resource_id;
            $device->me_res_id = $details["res_id"];
            $device->save();
            return $details["res_id"];
        }
        return 0;
    }
    public function lookup_item(Request $request){
        switch ($request->search_type) {
            case 'user':
                return redirect("/client/view/$request->item");
                break;
            
            case 'computer_by_user':
                return redirect("/device/find_by_user/$request->item");
                break;
            
            case 'computer_by_computername':
                return redirect("/device/view/$request->item");
                break;
            
            default:
                # code...
                break;
        }
        return view("lookup")->with('result', $result)->with('item', $request->item);
    }
    public function get_device_in_me_by_username(Request $request, $ad_user){
        $token = $this->authenticate_to_me($request);
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => $token
        ];
        $guzzle_client = new \GuzzleHttp\Client(['verify' => false]);
        $url = env('ME_SERVER_URL') . ":" . env('ME_SERVER_PORT') .  "/api/1.4/inventory/scancomputers?searchtype=agent_logged_on_users&searchcolumn=agent_logged_on_users&searchvalue=" . $ad_user;
        $response = $guzzle_client->get($url, [
            'headers' => $headers,
        ]);
        $response_data = $response->getBody();
        $data = json_decode($response_data);
        return $data;
    }
    public function find_device_by_user(Request $request, $username){
        $data = $this->get_device_in_me_by_username($request, $username);
        if ($data->status == "success"){
            $computers = $data->message_response->scancomputers;
            $devices = [];
            foreach ($computers as $computer){
                // dd($computer);
                $device = new \stdClass();
                $device->computername = $computer->resource_name;
                $device->name = $computer->agent_logged_on_users;
                $device->ad_user = $computer->agent_logged_on_users;
                $device->device_model = "unknown";
                $device->machine_manifest = "none";
                $device->updated_at = "none";
                $device->operating_system = $computer->software_name;
                $device->me_res_id = $computer->resource_id;
                array_push($devices, $device);
            }
            if (count($devices) == 0){
                return redirect("/lookup")->with("message", "Unable to locate PC.");
            } else {
                if (count($devices) == 1){
                    $computername = $devices[0]->computername;
                    return redirect("/device/view/$computername");
                } else {
                    return view('device_index')->with('devices', $devices)->with('device_count', count($devices));
                }
            }
        }
        // $data = $this->get_device_in_me_by_username($request, $username);
    }
    public function lookup_device_in_me_by_username(Request $request, $ad_user){
        $client = Client::where('ad_user', $ad_user)->firstorfail();
        $data = $this->get_device_in_me_by_username($request, $ad_user);
        if ($data->status == "success" && sizeof($data->message_response->scancomputers) !== 0){
            $computer_details = $data->message_response->scancomputers[0];
            $device = New Device;
            $device->assigned_user_id = $client->id;
            $device->computername = $computer_details->resource_name;
            $device->username = $client->name;
            $device->reportjson = "none";
            $device->save();
            $assigned_user = Client::where('id', $device->assigned_user_id)->first();
            $assigned_user->device_id = $device->id;
            $assigned_user->save();
            $journal_entry = New JournalEntry;
            $journal_entry->journal_entry = "Allocated new PC: $device->computername";
            $journal_entry->user_id = $client->id;
            $journal_entry->admin_id = Auth::id();
            $journal_entry->save();
            return redirect("/device/view/$device->id")->with('message', "New device created.");
        }
        return redirect("/client/view/$client->id")->with('message', "Unable to find device in Manage Engine!");
    }
    public function make_manage_engine_request(Request $request, $apiuri){
        $token = $this->authenticate_to_me($request);
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => $token
        ];
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $server = env('ME_SERVER_URL');
        $port = env('ME_SERVER_PORT');
        $url = $server . ":" . $port . $apiuri;
        $response = $client->get($url, [
            'headers' => $headers,
        ]);
        $response_data = $response->getBody();
        $data = json_decode($response_data);
        if ($data->status == "success"){
            return $data;
        } else {
            return redirect("/device/view/$id")->with('message', "Unable to retrieve device details from Manage Engine!");
        }
    }
    public function get_softwarelist_from_manage_engine(Request $request, $computername){
        $page_limit = 0;
        $starting_page = 1;
        $device = Device::where('computername', $computername)->firstorfail();
        if (!isset($device->me_res_id)){
            $res_id = $this->get_manage_engine_resource_id($request, $computername);
        } else {
            $res_id = $device->me_res_id;
        }
        if ($res_id == 0){
            return redirect("/device/view/$id")->with('message', "Unable to find device in Manage Engine!");
        }
        $apiuri = "/api/1.4/inventory/installedsoftware?resid=" . $res_id . "&page=" . $starting_page . "&pagelimit=" . $page_limit . "&orderby=desc";
        $response = $this->make_manage_engine_request($request, $apiuri);
        $installed_software_response = $response->message_response->installedsoftware;
        $installed_software = [];
        foreach ($installed_software_response as $entry){
            if ($entry->installed_date == 0 && $entry->manufacturer_name == "Microsoft Corporation"){
                continue;
            }
            $new_entry = New \stdClass();
            $new_entry->software_name = $entry->software_name;
            $new_entry->software_version = $entry->software_version;
            $new_entry->epoch = $entry->installed_date;
            $date = Carbon::createFromTimeStamp($entry->installed_date/1000);
            $new_entry->installed_date = $date->format('Y-m-d');
            $new_entry->architecture = $entry->architecture;
            $new_entry->manufacturer_name = $entry->manufacturer_name;
            $installed_software[] = $new_entry;
        }
        $software_name = array_column($installed_software, 'software_name');
        array_multisort($software_name, SORT_ASC, $installed_software);
        return $installed_software;
    }
    public function get_device_details_from_manage_engine(Request $request, $id){
        $device = Device::where('id', $id)->firstorfail();
        if (!isset($device->me_res_id)){
            $res_id = $this->get_manage_engine_resource_id($request, $device->computername);
        } else {
            $res_id = $device->me_res_id;
        }
        if ($res_id == 0){
            return redirect("/device/view/$id")->with('message', "Unable to find device in Manage Engine!");
        }
        $token = $this->authenticate_to_me($request);
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => $token
        ];
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $server = env('ME_SERVER_URL');
        $port = env('ME_SERVER_PORT');
        $url = $server . ":" . $port . "/api/1.4/inventory/compdetailssummary?resid=" . $res_id;
        $response = $client->get($url, [
            'headers' => $headers,
        ]);
        $response_data = $response->getBody();
        $data = json_decode($response_data);
        if ($data->status == "success"){
            $computer_details = $data->message_response->compdetailssummary;
            // $device-> = $computer_details->computer_summary->computer_name;
            $device->device_manufacturer = $computer_details->computer_hardware_summary->device_manufacturer;
            $device->device_model = $computer_details->computer_hardware_summary->device_model;
            $device->device_type = $computer_details->computer_hardware_summary->device_type;
            $device->serial_no = $computer_details->computer_hardware_summary->serial_number;
            $device->ram = $computer_details->computer_hardware_summary->memory;
            $device->disk_total_size = $computer_details->computer_disk_summary->total_size;
            $device->disk_percent_free = round($computer_details->computer_disk_summary->percent_free, 2);
            $device->operating_system = $computer_details->computer_os_summary->os_name;
            $device->os_version = $computer_details->computer_os_summary->os_version;
            $device->save();
            return redirect("/device/view/$id")->with('message', "Device Details updated.");
        }
        return redirect("/device/view/$id")->with('message', "Device details update failed.");
        // ;
    }
}
