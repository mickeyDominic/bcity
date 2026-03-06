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
            <button class="nav-link" id="contacts-tab" data-bs-toggle="tab" 
                    data-bs-target="#contacts" type="button" role="tab" 
                    aria-controls="contacts" aria-selected="false"
            >
                Contacts
            </button>
        </li>
    </ul>
    <hr>

    <div class="tab-content" id="modalTabContent">
        <div class="row">
            <span id="server_message" class="text-center"></span>
        </div>
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <form id="add_client_form" class="p-3">
                <div class="mb-2">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Name" 
                        value="{{$client->name ?? ''}}" {{ !empty($client) ? 'readonly' : '' }} required
                    >
                </div>
                <div class="mb-3">
                    <label for="code">Code</label>
                    <input type="text" id="code" class="form-control" readonly value="{{$client->code ?? ''}}">
                </div>
                
                @if(empty($client))
                    <div class="mb-2 float-end">
                        <button type="submit" class="btn btn-success">Add Client</button>
                    </div>
                @endif
        </form>
        </div>
        <div class="tab-pane fade" id="contacts" role="tabpanel" aria-labelledby="contacts-tab">
            @if(!empty($linkedContacts))
                <div class="row text-center">
                    <div class="col-5">
                        Full Name
                    </div>
                    <div class="col-5">
                        Email
                    </div>
                    <div class="col-2">
                        
                    </div>
                    <hr>
                </div>

                @foreach($linkedContacts as $contact)
                    <form id="unlink_client_from_contact_{{$contact->contact_id}}" class="unlink_client_from_contact mb-2">
                        <input type="hidden" name="client_id" value="{{$contact->client_id}}">
                        <input type="hidden" name="contact_id" value="{{$contact->contact_id}}">
                        <div class="row text-start mb-3">
                            <div class="col-5">
                                {{$contact->surname}} {{$contact->name}}
                            </div>
                            <div class="col-5">
                                {{$contact->email}}
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn btn-success btn-sm float-start">Unlink</button>
                            </div>
                        </div>
                    </form>
                @endforeach
            @else
                <h4 class="text-center text-danger">No contacts found.</h4>
            @endif
        </div>
    </div>

</div>
<script>
    $(document).ready(function () {
        $('#add_client_form').parsley();
        $('.unlink_client_from_contact').on('submit', function (event) {
            let formId = this.id;
            event.preventDefault();
            $('#reusableModalLG #content').hide();
            $('#reusableModalLG #loading').show();
            $.ajax({
                type: "POST",
                url: window.location.origin + '/clients/unlink-from-contact',
                data: JSON.stringify(Object.fromEntries(new FormData(document.getElementById(this.id)))),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response) {
                    $('#'+formId).remove();
                    $('#server_message').html('<div class="alert alert-'+response.alert+'">'+response.message+'</div>');
                    $('#clients_table').DataTable().draw();
                    if($('.unlink_client_from_contact').length === 0) {
                        $('#contacts').html('<h4 class="text-center text-danger">No contacts found.</h4>');
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
        
        $('#add_client_form').on('submit', function (event) {
            event.preventDefault();
            $('#reusableModalLG #content').hide();
            $('#reusableModalLG #loading').show();
            $.ajax({
                type: "POST",
                url: window.location.origin + '/clients',
                data: JSON.stringify(Object.fromEntries(new FormData(document.getElementById('add_client_form')))),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                contentType: "application/json; charset=utf-8",
                dataType: "json",
                success: function(response) {
                    $('#server_message').html('<div class="alert alert-'+response.alert+'">'+response.message+'</div>');
                    $('#clients_table').DataTable().row.add(response.rowData).draw();
                    $('#add_client_form #name').attr('readonly', true);
                    $('.error-message').remove();
                    $('#reusableModalLG #content').show();
                    $('#reusableModalLG #loading').hide();
                    $('#code').val(response.client_code);
                    $('#reusableModalLG .modal-body button[type="submit"]').prop('disabled', true);
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
                    } else {
                        $('#server_message').html('<div class="alert alert-'+response.alert+'">'+response.message+'</div>');
                    }
                }
            });
        });
    });
</script>