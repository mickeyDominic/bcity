<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ClientController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Contact;
use App\Models\ClientContact;

class ModalController extends Controller
{
    /**
     * This method should be provided:
     *       1. $request->post('reusableFile') - the file/form to be rendered in modal
     *       2. $request->post('reusableId')   - a resource $id, method will always need at least this $id to get
     *                                            render data
     *       3. $request->post('reusableApp')  - an identifier for what part of the app needs to use the method,
     *                                           depending on this, the logic executed may vary
     *                                           (different data may need to be sent back)
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function reusableModalContent(Request $request)
    {
        $data = [];
        // add more cases as more parts of the app depend on this method
        switch ($request->post('reusableApp')) {
            case 'add_client':
                if(!empty($request->post('clientId'))) {
                    $client = Client::find($request->post('clientId'));
                    $linkedContacts = DB::table('contact')
                        ->select('client_contact.client_id', 'client_contact.contact_id', 'contact.surname', 'contact.name', 'contact.email')
                        ->join('client_contact', 'contact.id', '=', 'client_contact.contact_id')
                        ->where('client_contact.client_id', $request->post('clientId'))
                        ->get()
                        ->toArray();
                    $data = [
                        'client' => $client,
                        'linkedContacts' => $linkedContacts
                    ];
                }
                $file = 'modal_content.add_client';
                break;
            case 'add_contact':
                if(!empty($request->post('contactId'))) {
                    $contact = Contact::find($request->post('contactId'));
                    $linkedClients = DB::table('client')
                        ->select('client_contact.client_id', 'client_contact.contact_id', 'client.name', 'client.code')
                        ->join('client_contact', 'client.id', '=', 'client_contact.client_id')
                        ->where('client_contact.contact_id', $request->post('contactId'))
                        ->get()
                        ->toArray();
                    $data = [
                        'contact' => $contact,
                        'linkedClients' => $linkedClients
                    ];
                }
                $file = 'modal_content.add_contact';
                break;
            case 'link_contact':
                if(!empty($request->post('clientId'))) {
                    $linkedContacts = ClientContact::where('client_contact.client_id', $request->post('clientId'))
                        ->select('client_contact.contact_id')
                        ->get()
                        ->toArray();
                    $data = [
                        'client' => Client::find($request->post('clientId')),
                        'contacts' => Contact::all(),
                        'linkedContacts' => $linkedContacts
                    ];
                } else if (!empty($request->post('contactId'))) {
                    $linkedClients = ClientContact::where('client_contact.contact_id', $request->post('contactId'))
                        ->select('client_contact.client_id')
                        ->get()
                        ->toArray();
                    $data = [
                        'contact' => Contact::find($request->post('contactId')),
                        'clients' => Client::all(),
                        'linkedClients' => $linkedClients
                    ];
                }
                $file = 'modal_content.link_contact';
                break;
        }

        return view($file, $data);
    }
}
