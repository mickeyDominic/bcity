<div class="row">
    @if(!empty($client))
        <div class="row">
            <p><strong>Client Name:</strong> {{$client->name}}</p>
            <span id="server_message"></span>
        </div>
        <div class="row">
            <form id="link_to_contacts" class="px-3">
                <label for="contacts" class="mb-2">Select Contacts To Link:</label>
                <div class="row text-center mt-2 text-strong">
                    <div class="col-2">
                        Select
                    </div>
                    <div class="col-5">
                        Full Name
                    </div>
                    <div class="col-5">
                        Email
                    </div>
                </div>
                <div class="row mb-2">
                    <input type="hidden" name="client_id" value="{{$client->id}}">
                    @foreach($contacts as $contact)
                        <div class="col-2 text-center">
                            <input class="form-check-input" type="checkbox" value="{{$contact->id}}" 
                                    id="contact_{{$contact->id}}" name="contacts[]"
                                    {{ in_array($contact->id, array_column($linkedContacts, "contact_id")) ? 'checked' : '' }}

                            >
                        </div>
                        <div class="col-5">
                            {{$contact->surname}} {{$contact->name}}
                        </div>
                        <div class="col-5">
                            {{$contact->email}}
                        </div>
                    @endforeach
                </div>
                <div class="mb-2">
                    <button type="submit" class="btn btn-success btn-sm float-end">Save</button>
                </div>
            </form>
        </div>
    @elseif(!empty($contact))
        <div class="row">
            <p><strong>Contact Name:</strong> {{$contact->surname}} {{$contact->name}}</p>
            <span id="server_message"></span>
        </div>
        <div class="row">
            <form id="link_to_clients" class="px-3">
                <label for="contacts" class="mb-2">Select Clients To Link:</label>
                <div class="row text-center mt-2 text-strong">
                    <div class="col-2">
                        Select
                    </div>
                    <div class="col-5">
                        Name
                    </div>
                    <div class="col-5">
                        Code
                    </div>
                </div>
                <div class="row mb-2">
                    <input type="hidden" name="contact_id" value="{{$contact->id}}">
                    @foreach($clients as $client)
                        <div class="col-2 text-center">
                            <input class="form-check-input" type="checkbox" value="{{$client->id}}" 
                                    id="contact_{{$client->id}}" name="clients[]"
                                    {{ in_array($client->id, array_column($linkedClients, "client_id")) ? 'checked' : '' }}
                            >
                        </div>
                        <div class="col-5">
                            {{$client->name}}
                        </div>
                        <div class="col-5">
                            {{$client->code}}
                        </div>
                    @endforeach
                </div>
                <div class="mb-2">
                    <button type="submit" class="btn btn-success btn-sm float-end">Save</button>
                </div>
            </form>
        </div>
    @endif
<script>
    $(document).ready(function () {
        $('#link_to_contacts').on('submit', function (event) {
            event.preventDefault();
            $('#reusableModalLG #loading').show();
            $('#reusableModalLG #content').hide();
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: window.location.origin + '/clients/link-contacts',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                dataType: "json",
                success: function(response) {
                    $('#clients_table').DataTable().draw();
                    $('#reusableModalLG #content').show();
                    $('#reusableModalLG #loading').hide();
                    $('#server_message').html('<div class="alert alert-'+response.alert+'">'+response.message+'</div>');
                },
                error: function(xhr) {
                    // If validation fails (422), access errors here
                    if (xhr.status === 422) {
                        $('#reusableModalLG #content').show();
                        $('#reusableModalLG #loading').hide();
                        const errors = xhr.responseJSON.errors;
                        $('.error-message').remove(); 
                        // $.each(errors, function(field, messages) {
                        //     const input = $(`[name="${field}"]`);
                        //     input.after(`<span class="error-message" style="color:red;">${messages[0]}</span>`);
                        // });
                    }
                }
            });
        });
        
        $('#link_to_clients').on('submit', function (event) {
            event.preventDefault();
            $('#reusableModalLG #loading').show();
            $('#reusableModalLG #content').hide();
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: window.location.origin + '/contacts/link-clients',
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                dataType: "json",
                success: function(response) {
                    $('#contacts_table').DataTable().draw();
                    $('#reusableModalLG #content').show();
                    $('#reusableModalLG #loading').hide();
                    $('#server_message').html('<div class="alert alert-'+response.alert+'">'+response.message+'</div>');
                },
                error: function(xhr) {
                    // If validation fails (422), access errors here
                    if (xhr.status === 422) {
                        $('#reusableModalLG #content').show();
                        $('#reusableModalLG #loading').hide();
                        const errors = xhr.responseJSON.errors;
                        $('.error-message').remove(); 
                        // $.each(errors, function(field, messages) {
                        //     const input = $(`[name="${field}"]`);
                        //     input.after(`<span class="error-message" style="color:red;">${messages[0]}</span>`);
                        // });
                    }
                }
            });
        });
    });
</script>