<div class="row">
    <ul class="nav nav-pills nav-justified mb-3" id="modalTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" 
                    data-bs-target="#general" type="button" role="tab" 
                    aria-controls="general" aria-selected="true"
            >
                General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="clients-tab" data-bs-toggle="tab" 
                    data-bs-target="#clients" type="button" role="tab" 
                    aria-controls="clients" aria-selected="false"
            >
                Clients
            </button>
        </li>
    </ul>

    <div class="tab-content" id="modalTabContent">
        <div class="row">
            <span id="server_message" class="text-center"></span>
        </div>
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <form id="add_contact_form" class="p-3">
                <div class="mb-2">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Name"
                            value="{{$contact->name ?? ''}}" {{ !empty($contact) ? 'readonly' : '' }} required
                    >
                </div>
                <div class="mb-3">
                    <label for="surname">Surname</label>
                    <input type="text" name="surname" id="surname" class="form-control"  placeholder="Surname"
                            value="{{$contact->surname ?? ''}}" {{ !empty($contact) ? 'readonly' : '' }} required
                    >
                </div>
                <div class="mb-3">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control"  placeholder="Email Address"
                            value="{{$contact->email ?? ''}}" {{ !empty($contact) ? 'readonly' : '' }} required
                    >
                </div>
                <div class="mb-2 float-end">
                    <button type="submit" class="btn btn-success">Add Contact</button>
                </div>
            </form>
        </div>
        <div class="tab-pane fade" id="clients" role="tabpanel" aria-labelledby="clients-tab">
            @if(!empty($linkedClients))
                <div class="row text-center">
                    <div class="col-5">
                        Name
                    </div>
                    <div class="col-5">
                        Code
                    </div>
                    <div class="col-2">
                        
                    </div>
                    <hr>
                </div>

                @foreach($linkedClients as $client)
                    <form id="unlink_contact_from_client_{{$client->contact_id}}" class="unlink_contact_from_client mb-2">
                        <input type="hidden" name="contact_id" value="{{$client->contact_id}}">
                        <input type="hidden" name="client_id" value="{{$client->client_id}}">
                        <div class="row text-start mb-3">
                            <div class="col-5">
                                {{$client->name}}
                            </div>
                            <div class="col-5">
                                {{$client->code}}
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-success btn-sm float-start">Unlink</button>
                            </div>
                        </div>
                    </form>
                @endforeach
            @else
                <h4 class="text-center text-danger">No clients found.</h4>
            @endif
        </div>
    </div>

</div>
<script>
    $(document).ready(function () {
        $('#add_contact_form').parsley();

        $('.unlink_contact_from_client').on('submit', function (event) {
            let formId = this.id;
            event.preventDefault();
            $('#reusableModalLG #content').hide();
            $('#reusableModalLG #loading').show();
            $.ajax({
                type: "POST",
                url: window.location.origin + '/contacts/unlink-from-client',
                data: JSON.stringify(Object.fromEntries(new FormData(document.getElementById(this.id)))),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response) {
                    $('#'+formId).remove();
                    $('#contacts_table').DataTable().draw();
                    if($('.unlink_contact_from_client').length === 0) {
                        $('#clients').html('<h4 class="text-center text-danger">No clients found.</h4>');
                    }
                    $('#reusableModalLG #content').show();
                    $('#reusableModalLG #loading').hide();
                },
                error: function(xhr) {
                    // If validation fails
                    if (xhr.status === 422) {
                        $('#reusableModalLG #content').show();
                        $('#reusableModalLG #loading').hide();
                        const errors = xhr.responseJSON.errors;
                        $('.error-message').remove(); 
                        $.each(errors, function(field, messages) {
                            const input = $(`[name="${field}"]`);
                            input.after(`<span class="error-message" style="color:red;">${messages[0]}</span>`);
                        });
                    }
                }
            });
        });

        $('#add_contact_form').on('submit', function (event) {
            event.preventDefault();
            $('#reusableModalLG #content').hide();
            $('#reusableModalLG #loading').show();
            $.ajax({
                type: "POST",
                url: window.location.origin + '/contacts',
                data: JSON.stringify(Object.fromEntries(new FormData(document.getElementById('add_contact_form')))),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response) {
                    $('#server_message').html('<div class="alert alert-'+response.alert+'">'+response.message+'</div>');
                    $('#contacts_table').DataTable().row.add(response.rowData).draw();
                    $('#add_contact_form #name').attr('readonly', true);
                    $('#add_contact_form #surname').attr('readonly', true);
                    $('#add_contact_form #email').attr('readonly', true);
                    $('.error-message').remove();
                    $('#reusableModalLG #content').show();
                    $('#reusableModalLG #loading').hide();
                    $('#reusableModalLG .modal-body button[type="submit"]').prop('disabled', true);
                },
                error: function(xhr) {
                    // If validation fails (422), access errors here
                    if (xhr.status === 422) {
                        $('#reusableModalLG #content').show();
                        $('#reusableModalLG #loading').hide();
                        const errors = xhr.responseJSON.errors;
                        $('.error-message').remove(); 
                        $.each(errors, function(field, messages) {
                            const input = $(`[name="${field}"]`);
                            input.after(`<span class="error-message" style="color:red;">${messages[0]}</span>`);
                        });
                    }
                }
            });
        });
    });
</script>