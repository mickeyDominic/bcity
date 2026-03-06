<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientContactCotroller extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function storeClientContacts(Request $request)
    {
        Client::update(
            ['linked_contacts' => json_encode($request->post('linked_contacts'))]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeContactClients(Request $request)
    {
        Contact::update(
            ['linked_contacts' => json_encode($request->post('linked_clients'))]
        );
    }
}
