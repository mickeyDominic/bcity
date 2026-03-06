<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use App\Models\ClientContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clients');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->json()->all(), ['name' => 'required|string|min:3|max:50']);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        } else {
            try {
                $client = new Client();
                $client->name = $request->post("name");
                $explodedName = explode(" ", $request->post("name"));
                if (sizeof($explodedName) > 2) {
                    $code = substr($explodedName[0], 0, 1).substr($explodedName[1], 0, 1).substr($explodedName[2], 0, 1);
                } else {
                    $code = substr($request->post("name"), 0, 3);
                }
                $client->code = strtoupper($code);
                $client->save();
                Client::where('id', $client->id)
                    ->update(['code' => $client->code.str_pad($client->id, 3, "0", STR_PAD_LEFT)]);

                $buttons = '<a data-bs-toggle="modal" data-bs-target="#reusableModalLG"'.
                                'data-bs-label="View Client"'.
                                'data-bs-client="'.$client->id.'"'.
                                'data-bs-app="add_client" class="btn btn-warning btn-sm me-1">'.
                            'View'.
                        '</a>' .
                        '<a data-bs-toggle="modal" data-bs-target="#reusableModalLG"'.
                                'data-bs-label="Link To Contacts"'.
                                'data-bs-client="'.$client->id.'"'.
                                'data-bs-app="link_contact" class="btn btn-success btn-sm ms-1">'.
                            'Link To Contacts'.
                        '</a>';

                return response()->json([
                    'status' => 'success', 
                    'client_code' => $client->code.str_pad($client->id, 3, "0", STR_PAD_LEFT),
                    'rowData' => [
                        'name' => $client->name,
                        'code' => $client->code.str_pad($client->id, 3, "0", STR_PAD_LEFT),
                        'count' => 0,
                        'buttons' => $buttons
                    ],
                    'alert' => 'success',
                    'message' => 'Client added successfully'
                ], 201);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'An error occurred while adding the client',
                    'alert' => 'danger'
                ], 500);
            }
        }
    }

    /**
     * Retrieve records for DataTables
     */
    public function getRecords(Request $request)
    {
        //allow searching by name and code
        $search = $request->post('search')['value'] ? "client.name like '%".$request->post('search')['value']."%' or client.code like '%".$request->post('search')['value']."%'" : ' 1 > 0 ';

        //columns for ordering
        $columns = ['client.name','client.code'];
        
        $data = DB::table('client')
            ->leftJoin('client_contact', 'client.id', '=', 'client_contact.client_id')
            ->select('client.id as client_id', 'client.name', 'client.code', DB::raw('count(client_contact.id) as count'))
            ->whereRaw($search)
            ->groupBy('client.id')
            ->orderBy($columns[$request->post('order')[0]['column']], $request->post('order')[0]['dir'])
            ->skip($request->post('start'))
            ->take($request->post('length'))
            ->get();

        $records = Client::whereRaw($search)->get(['name', 'code']);
        
        $allRecords = Client::all();

        return ['data' => $data, "recordsFiltered" => count($records), "recordsTotal" => count($allRecords)];
    }

    /**
     * Create or update linked contacts for a client.
     */
    public function storeClientContacts(Request $request)
    {
        try {
            ClientContact::where('client_id', $request->post('client_id'))
                ->delete();
            foreach ($request->post('contacts') as $key =>$value) {
                $data[] = [
                    'client_id' => $request->post('client_id'),
                    'contact_id' => $value
                ]; 
            }
            ClientContact::insert($data);

            $status = 'success';
            $alert = 'success';
            $mesage = 'Contacts linked successfully';
            $httpCode = 201;
        } catch (Exception $e) {
            $status = 'error';
            $alert = 'danger';
            $mesage = 'An error occurred while linking contacts';
            $httpCode = 500;
        }
        
        return response()->json(['status' => $status, 'alert' => $alert, 'message' => $mesage], $httpCode);
    }


    /**
     * Unlink contact from client.
     */
    public function unlinkFromContact(Request $request)
    {
        try {
            ClientContact::where('client_id', $request->post('client_id'))
                ->where('contact_id', $request->post('contact_id'))
                ->delete();

            $status = 'success';
            $alert = 'success';
            $mesage = 'Contact unlinked successfully';
            $httpCode = 200;
        } catch (Exception $e) {
            $status = 'error';
            $alert = 'danger';
            $mesage = 'An error occurred while unlinking contact';
            $httpCode = 500;
        }
        
        return response()->json(['status' => $status, 'alert' => $alert, 'message' => $mesage], $httpCode);
    }







    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}
