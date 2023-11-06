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
            $clients = Client::where('name', 'like', '%'.$searched_text.'%')->paginate(15);
            $clientcount = count($clients);
            if (count($clients) == 0){
                $client = $this->get_user_details_from_ldap($searched_text);
                if ($client){
                    return redirect("/client/view/" . $client->ad_name);
                } else {
                    Session::flash('message', 'No results found!');
                    return redirect("/client/index/");
                }
            }
            if (count($clients) == 1){
                foreach($clients as $foundclient){
                    return redirect("/client/view/$foundclient->ad_user");
                }
            } else {
                return view('client_index')->with('clientlist', $clients)->with('clientcount', $clientcount);
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
            ->paginate(15);
            $device_count = count($founddevices);
            if (count($founddevices) == 0){
                if ($this->get_manage_engine_resource_id($request, $searched_text)){
                    return redirect("/device/view/" . $searched_text);
                } else {
                    Session::flash('message', 'No such device!');
                    return redirect("/device/index/");
                }
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
                return false;
            }
            $computer_details = $data->message_response->scancomputers[0];
            return $computer_details->resource_id;
        } else {
            return False;
        }
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
    public function get_me_device_by_computername(Request $request, $computer_name){
        $token = $this->authenticate_to_me($request);
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => $token
        ];
        $guzzle_client = new \GuzzleHttp\Client(['verify' => false]);
        $url = env('ME_SERVER_URL') . ":" . env('ME_SERVER_PORT') .  "/api/1.4/inventory/scancomputers?searchtype=resource_name&searchcolumn=resource_name&searchvalue=" . $computer_name;
        $response = $guzzle_client->get($url, [
            'headers' => $headers,
        ]);
        $response_data = $response->getBody();
        $data = json_decode($response_data);
        if ($data->status == "success"){
            return $data->message_response;
        } else {
            return FALSE;
        }
    }
    public function get_me_devices_by_username(Request $request, $ad_user){
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
        if ($data->status == "success"){
            $scan_computers = $data->message_response->scancomputers;
            return $scan_computers;
        } else {
            return FALSE;
        }
    }
    public function find_device_by_user(Request $request, $username){
        $scan_computers = $this->get_me_devices_by_username($request, $username);
        $devices = [];
        foreach ($computers as $computer){
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
    public function find_device_in_me_by_username(Request $request, $ad_user){
        $client = Client::where('ad_user', $ad_user)->firstorfail();
        $me_computers = $this->get_me_devices_by_username($request, $ad_user);
        if (sizeof($me_computers) !== 0){
            // Select the first computer in the array if there are many
            $me_computer = $me_computers[0];
        } else {
            return redirect("/client/view/$client->id")->with('message', "Unable to find device in Manage Engine!");
        }
        if (isset($client->device_id)){
            // Client already has an assigned device
            $device = Device::where('id', $client->device_id)->first();
            if ( ! isset($device)){
                $device = New Device;
            }
            $me_device_details = $this->get_device_details_from_manage_engine($request, $me_computer->resource_id);
            $device->device_manufacturer = $me_device_details->computer_hardware_summary->device_manufacturer;
            $device->device_model = $me_device_details->computer_hardware_summary->device_model;
            $device->device_type = $me_device_details->computer_hardware_summary->device_type;
            $device->serial_no = $me_device_details->computer_hardware_summary->serial_number;
            $device->ram = $me_device_details->computer_hardware_summary->memory;
            $device->disk_total_size = $me_device_details->computer_disk_summary->total_size;
            $device->disk_percent_free = round($me_device_details->computer_disk_summary->percent_free, 2);
            $device->operating_system = $me_device_details->computer_os_summary->os_name;
            $device->os_version = $me_device_details->computer_os_summary->os_version;
            $device->assigned_user_id = $client->id;
            $device->computername = $me_device_details->computer_summary->computer_name;
            $device->username = $client->name;
            $device->reportjson = "";
            $device->save();
            $client->device_id = $device->id;
            $client->save();
            $journal_entry = New JournalEntry;
            $journal_entry->journal_entry = "Allocated new PC: $device->computername";
            $journal_entry->user_id = $client->id;
            $journal_entry->admin_id = Auth::id();
            $journal_entry->save();
            return redirect("/client/view/$client->ad_user")->with('message', "Device details updated.");
        } else {
            // Creating a new device
            $device = New Device;
            $device->assigned_user_id = $client->id;
            $device->computername = $computer_details->resource_name;
            $device->username = $client->name;
            $device->device_type = "Default";
            $device->device_model = "Default";
            $device->me_res_id = $computer_details->resource_id;
            $device->serial_no = "xxx";
            $device->operating_system = $computer_details->software_name;
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
            return redirect("/client/view/$ad_user")->with('message', "New device created.");
        }
    }
    public function get_user_details_from_ldap($ad_name){
        $client = new \stdClass();
        $ldapclient = LDAPUser::where('sAMAccountName', '=', $ad_name)->get();
        if ($ldapclient->count()){
            $client->ad_name = $ldapclient[0]->sAMAccountName[0];
            $client->name = $ldapclient[0]->cn[0];
            $client->title = $ldapclient[0]->title[0];
            $client->description = $ldapclient[0]->description[0];
            $client->location = isset($ldapclient[0]->l[0]) ?  $ldapclient[0]->l[0] : Null;
            $client->city = isset($ldapclient[0]->st[0]) ? $ldapclient[0]->st[0] : Null;
            $client->country = isset($ldapclient[0]->co[0]) ? $ldapclient[0]->co[0] : Null;
            $client->email = isset($ldapclient[0]->mail[0]) ? $ldapclient[0]->mail[0] : Null;
            $client->department = isset($ldapclient[0]->department[0]) ? $ldapclient[0]->department[0] : "";
            $client->physicaladdress = isset($ldapclient[0]->physicaldeliveryofficename[0]) ? $ldapclient[0]->physicaldeliveryofficename[0] : "";
            $client->streetaddress = isset($ldapclient[0]->streetaddress[0]) ? $ldapclient[0]->streetaddress[0] : "";
            $client->mobile = isset($ldapclient[0]->mobile[0]) ? $ldapclient[0]->mobile[0] : "";
            $client->company = isset($ldapclient[0]->company[0]) ? $ldapclient[0]->company[0] : "";
            $client->manager = isset($ldapclient[0]->manager[0]) ? $ldapclient[0]->manager[0] : "";
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
            $client->employeetype = isset($ldapclient[0]->employeetype[0]) ? $ldapclient[0]->employeetype[0] : "";
            if (isset ($ldapclient[0]->badpwcount[0])){
                $client->badpwcount = $ldapclient[0]->badpwcount[0];
            }
            $client->lockouttime = $ldapclient[0]->lockouttime;
            return $client;
        } else {
            return False;
        }
    }
    public function make_manage_engine_request(Request $request, $endpoint, $items_per_page = 30 ){
        $token = $this->authenticate_to_me($request);
        $headers = [
            "Content-Type" => "application/json",
            "Authorization" => $token
        ];
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $server = env('ME_SERVER_URL');
        $port = env('ME_SERVER_PORT');
        $url = $server . ":" . $port . $endpoint . "&pagelimit=" . $items_per_page;
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
    public function get_softwarelist_from_manage_engine(Request $request, $computername, $items_per_page = 80){
        $starting_page = 1;
        $res_id = $this->get_manage_engine_resource_id($request, $computername);
        if ($res_id == 0){
            return "Unable to retrieve software list.";
        };
        $apiuri = "/api/1.4/inventory/installedsoftware?resid=" . $res_id . "&page=" . $starting_page . "&orderby=desc";
        $response = $this->make_manage_engine_request($request, $apiuri, $items_per_page);
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
            $new_entry->software_id = $entry->software_id;
            $installed_software[] = $new_entry;
        }
        $software_name = array_column($installed_software, 'software_name');
        array_multisort($software_name, SORT_ASC, $installed_software);
        return $installed_software;
    }
    public function get_device_details_from_manage_engine(Request $request, $res_id){
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
            return $data->message_response->compdetailssummary;
        } else {
            return FALSE;
        }
    }
    public function get_device_details_from_manage_engine_by_device_id(Request $request, $id){
        $device = Device::where('id', $id)->firstorfail();
        if (!isset($device->me_res_id)){
            $res_id = $this->get_manage_engine_resource_id($request, $device->computername);
        } else {
            $res_id = $device->me_res_id;
        }
        if ($res_id == 0){
            return redirect("/device/view/$id")->with('message', "Unable to find device in Manage Engine!");
        }
        $device_details = $this->get_device_details_from_manage_engine($res_id);
        return $device_details;
    }
}
