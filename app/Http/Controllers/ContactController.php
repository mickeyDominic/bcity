<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ClientContact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('contacts');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|min:3|max:50',
            'surname' => 'required|string|min:3|max:50',
            'email' => 'required|email|unique:contact,email'
        ];
        $validator = Validator::make($request->json()->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        } else {
            $contact = Contact::create([
                'name' => $request->post("name"), 
                'surname' => $request->post("surname"), 
                'email' => $request->post("email")
            ]);

            $buttons = '<a data-bs-toggle="modal" data-bs-target="#reusableModalLG"'.
                            'data-bs-label="View Contact"'.
                            'data-bs-contact="' . $contact->id .'"'.
                            'data-bs-app="add_client" class="btn btn-warning btn-sm me-1">'.
                        'View'.
                    '</a>' .
                    '<a data-bs-toggle="modal" data-bs-target="#reusableModalLG"'.
                            'data-bs-label="Link To Clients"'.
                            'data-bs-contact="' .$contact->id . '"'.
                            'data-bs-app="link_contact" class="btn btn-success btn-sm ms-1">'.
                        'Link To Clients'.
                    '</a>';

            return response()->json([
                'status' => 'success', 
                'rowData' => [
                    'surname' => $contact->surname,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'count' => 0,
                    'buttons' => $buttons
                ],
                'alert' => 'success',
                'message' => 'Contact added successfully'
            ], 201);
        }
    }

    /**
     * Retrieve records for DataTables
     */
    public function getRecords(Request $request)
    {
        //allow searching by name and code
        $search = $request->post('search')['value'] ? "contact.name like '%".$request->post('search')['value']."%' or contact.surname like '%".$request->post('search')['value']."%' or contact.email like '%".$request->post('search')['value']."%'" : ' 1 > 0 ';
        
        //columns for ordering
        $columns = ['contact.surname', 'contact.name', 'contact.email'];
        
        //check if ordering by surname, if so, order by surname then name to avoid same surname disorder
        if ($request->post('order')[0]['column'] == 0) {
            $orderByRawString = $columns[0]  . ' ' . $request->post('order')[0]['dir'] . ' , ' . $columns[1] . ' ' . $request->post('order')[0]['dir'];
        } elseif ($request->post('order')[0]['column'] == 1) {
            $orderByRawString = $columns[1]  . ' ' . $request->post('order')[0]['dir'] . ' , ' . $columns[0] . ' ' . $request->post('order')[0]['dir'];
        } else {
            $orderByRawString = $columns[$request->post('order')[0]['column']] . ' ' . $request->post('order')[0]['dir'];
        }
        
        $data = DB::table('contact')
            ->leftJoin('client_contact', 'contact.id', '=', 'client_contact.contact_id')
            ->select('contact.id as contact_id', 'contact.name', 'contact.surname', 'contact.email', DB::raw('count(client_contact.id) as count'))
            ->whereRaw($search)
            ->groupBy('contact.id')
            ->orderByRaw($orderByRawString)
            ->skip($request->post('start'))
            ->take($request->post('length'))
            ->get();

        $records = Contact::whereRaw($search)->get(['name', 'email']);
        
        $allRecords = Contact::all();

        return ['data' => $data, "recordsFiltered" => count($records), "recordsTotal" => count($allRecords)];
    }

    /**
     * Create or update linked contacts for a client.
     */
    public function storeContactClients(Request $request)
    {
        try {
            ClientContact::where('contact_id', $request->post('contact_id'))
                ->delete();
            foreach ($request->post('clients') as $key =>$value) {
                $data[] = [
                    'contact_id' => $request->post('contact_id'),
                    'client_id' => $value
                ]; 
            }
            ClientContact::insert($data);

            $status = 'success';
            $alert = 'success';
            $mesage = 'Clients linked successfully';
            $httpCode = 201;
        } catch (Exception $e) {
            $status = 'error';
            $alert = 'danger';
            $mesage = 'An error occurred while linking clients';
            $httpCode = 500;
        }
        
        return response()->json(['status' => $status, 'alert' => $alert, 'message' => $mesage], $httpCode);
    }

    /**
     * Unlinked contact from client.
     */
    public function unlinkFromClient(Request $request)
    {
        try {
            ClientContact::where('client_id', $request->post('client_id'))
                ->where('contact_id', $request->post('contact_id'))
                ->delete();

            $status = 'success';
            $alert = 'success';
            $mesage = 'Client unlinked successfully';
            $httpCode = 204;
        } catch (Exception $e) {
            $status = 'error';
            $alert = 'danger';
            $mesage = 'An error occurred while unlinking client';
            $httpCode = 500;
        }
        
        return response()->json(['status' => $status, 'alert' => $alert, 'message' => $mesage], $httpCode);
    }












    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
