<?php

namespace App\Http\Controllers;

use App\JournalEntry;
use Illuminate\Http\Request;
use Session;
use App\Client;
use App\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class JournalEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct() {
        $this->middleware('auth');
    }
     public function index()
    {
        $journal_entries = [];
        foreach (JournalEntry::orderBy('journal_entries.updated_at', 'desc')
            ->select('journal_entries.*', 'clients.name as name', 'users.name as adminName', 'clients.ad_user as ad_user')
            ->leftJoin('clients', 'journal_entries.user_id','=','clients.id')
            ->leftJoin('users', 'journal_entries.admin_id', '=', 'users.id')
            ->get() as $entry)
            \array_push($journal_entries, $entry);
        return view('journal_entry_index')->with('journal_entries', $journal_entries);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $user_id)
    {
        $clients = Client::orderBy("name")->get();
        return view('journal_entry_create')->with('user_id', $user_id)->with('clients', $clients);
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
            'journal_entry' => 'required',
        ]);
        $journal_entry = New JournalEntry;
        $journal_entry->journal_entry = $request->journal_entry;
        $journal_entry->user_id = $request->id;
        $journal_entry->admin_id = Auth::id();
        if ($request->file('attachment')){
            $attachment_file = $request->file('attachment')->store('attachment');
            $original_file = $request->file('attachment');
            $original_file_name = $original_file->getClientOriginalName();
            $file = New File;
            $file->user_id = $request->id;
            $file->friendly_file_name = $original_file_name;
            $file->journal_entry_id = 0;
            $file->uploaded_by_admin_id = Auth::id();
            $file->name = $attachment_file;
            $file->save();
            $journal_entry->attachment = $file->id;
        }
        $journal_entry->save();
        if ($request->file('attachment')){
            $file->journal_entry_id = $journal_entry->id;
            $file->save();
        }


        // touch the client object so we know something happened
        $client = Client::where('id', $request->id)->first();
        $client->touch();

        // Flash a message
        Session::flash('message', 'Journal Entry created!');
        return redirect("/client/view/$client->ad_user");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\JournalEntry  $journalEntry
     * @return \Illuminate\Http\Response
     */
    public function show(JournalEntry $journalEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\JournalEntry  $journalEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(JournalEntry $journalEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JournalEntry  $journalEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JournalEntry $journalEntry)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\JournalEntry  $journalEntry
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id){
        $journal_entry = JournalEntry::where('id', $id)->firstorfail();
        $attachment_file_id = $journal_entry->attachment;
        $attachment_file_entry = File::where('id', $attachment_file_id)->first();
        if ($attachment_file_entry){
            Storage::delete($attachment_file_entry->name);
            $attachment_file_entry->delete();
        }
        $client = Client::where('id', $journal_entry->user_id)->first();
        $journal_entry->delete();
        Session::flash('message', 'Journal Entry deleted!');
        return redirect("/client/view/$client->ad_user");
    }
}
