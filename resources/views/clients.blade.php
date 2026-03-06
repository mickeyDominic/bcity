<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        

        <title>{{ config('app.name', 'Laravel') }} | Clients</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        
        <!-- Bootstrap Css -->
        <link href="{{ URL::asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        
        <!-- DataTables -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.7/datatables.min.css" rel="stylesheet" integrity="sha384-TaPZIHS40hwajVHHLxaVNexgxbNrJTIRl7BUl34LA1SFjtWIrsAJkw5e73Pb2wQ+" crossorigin="anonymous">

        <!-- ParsleyJS -->
        <link href="{{ URL::asset('/css/parsley.css') }}" rel="stylesheet" type="text/css" />

        <!-- Custom css -->
        <link href="{{ URL::asset('/css/custom.css') }}" rel="stylesheet" type="text/css" />

    </head>
    <body class="container py-3">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">BCITY</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/clients">Clients</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/contacts">Contacts</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="row mt-5">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            Clients
                            <a id="create_ticket" class="btn btn-primary float-end"
                               data-bs-toggle="modal"
                               data-bs-target="#reusableModalLG"
                               data-bs-label="Add Client"
                               data-bs-app="add_client"
                            >
                                Add Client
                            </a>
                        </h4>
                        <p class="card-title-desc">View Clients here.</p>
                        

                        <div id="clients_found_div" class="table-responsive">
                            <table id="clients_table" class="table table-bordered dt-responsive stripe">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Client Code</th>
                                        <th>No. of linked contacts</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div id="clients_not_found_div" class="text-center text-danger">
                            <h4>No client(s) found</h4>
                        </div>
                        <!-- Modal -->
                        @include('modal_content.modalLG')
                    </div>
                </div>
            </div>
        </div>

        <!-- JAVASCRIPT -->
        <script src="{{ URL::asset('libs/jquery/jquery.min.js')}}"></script>
        <script src="{{ URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

        <!-- DataTables js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.3.7/datatables.min.js" integrity="sha384-vt7Se62YlbFnFq+6GomzrzmNsmoyYSLuZlXUULIBkHoL6at7yZfsATRmsQ3MtuJP" crossorigin="anonymous"></script>

        <!-- ParsleyJS -->
        <script src="{{ URL::asset('js/parsley.min.js') }}"></script>

        <!-- Custom js -->
        <script src="{{ URL::asset('js/custom.js') }}"></script>

        <script>
            function getClientData() {
                $('#clients_not_found_div').hide();
                $('#clients_found_div').hide();

                const table = $('#clients_table').DataTable({
                    searching: true,
                    paging:"simple_numbers",
                    lengthMenu: [[5, 10, 20, 50],[5,10,20,50]],
                    serverSide:true,
                    responsive: true,
                    processing:true,
                    order: [[0, 'asc']],
                    ajax:{
                        url: window.location.origin + '/clients/get-data',
                        type: 'POST',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        data: {}
                    },
                    columns: [
                        {"data":"name"},
                        {"data":"code"},
                        {"data":"count"},
                        {
                            "data":"buttons" ?? null,
                            "mRender": function(data, type, row){
                                return '<a data-bs-toggle="modal" data-bs-target="#reusableModalLG"'+
                                            'data-bs-label="View Client"'+
                                            'data-bs-client="'+row.client_id+'"'+
                                            'data-bs-app="add_client" class="btn btn-warning btn-sm me-1">'+
                                        'View'+
                                    '</a>' + 
                                    '<a data-bs-toggle="modal" data-bs-target="#reusableModalLG"'+
                                            'data-bs-label="Link To Contacts"'+
                                            'data-bs-client="'+row.client_id+'"'+
                                            'data-bs-app="link_contact" class="btn btn-success btn-sm ms-1">'+
                                        'Link To Contacts'+
                                    '</a>';;
                            }
                        }
                    ],
                    columnDefs:[
                        {
                            "orderable": false,
                            "targets": [-1, -2]
                        },

                        
                        { responsivePriority: 1, targets: 0 },
                        { responsivePriority: 2, targets: 1 },
                        { responsivePriority: 3, targets: 2 }
                    ],
                });


                //Listen for no records
                table.on('xhr', function() {
                    const json = table.ajax.json();
                    if (json.data.length === 0) {
                        $('#clients_found_div').hide();
                        $('#clients_not_found_div').show();
                    } else {
                        $('#clients_found_div').show();
                        $('#clients_not_found_div').hide();
                    }
                });
            }
            
            $(document).ready(function () {
                DataTable.defaults.column.orderSequence = ['asc', 'desc'];
                getClientData();
            });
        </script>
    </body>
</html>
