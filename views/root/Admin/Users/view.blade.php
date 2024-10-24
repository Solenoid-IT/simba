@php



use \App\Stores\Localizer as LocalizerStore;



// (Initializing the store)
LocalizerStore::read();



@endphp



@extends('layouts/base.blade.php')



@section('view.head')
    <title>
        Users
    </title>
@endsection



@section('view.body')

    <h1 class="mt-4">Users</h1>

    <div class="row">
        <div class="col d-flex justify-content-end">
            <button class="btn btn-primary" id="register_resource_button" title="Add">
                <i class="fa-solid fa-plus"></i>
                <span class="btn-label ms-1">
                    Add
                </span>
            </button>
        </div>
    </div>

    <div class="card mt-3 mb-4">
        <div class="card-body">
            <table id="resource_table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Username</th>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Surname</th>
                        <th>Email</th>
                        <th>Insert DateTime</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $entry)
                        <tr>
                            <td>
                                {{ $entry['id'] }}
                            </td>
                            <td>
                                {{ $entry['hierarchy']['type'] }}
                            </td>
                            <td>
                                {{ $entry['username'] }}
                            </td>
                            <td>
                                @if ( $entry['profile']['photo'] )
                                    <img class="profile-image me-3" src="data:{{ $entry['profile']['photo']['type'] }};base64,{{ $entry['profile']['photo']['content'] }}" alt="">
                                @endif
                            </td>
                            <td>
                                {{ $entry['profile']['name'] }}
                            </td>
                            <td>
                                {{ $entry['profile']['surname'] }}
                            </td>
                            <td>
                                {{ $entry['email'] }}
                            </td>
                            <td>
                                <span class="local-datetime" title="{{ $entry['datetime']['insert'] }} UTC">
                                    {{ LocalizerStore::fetch()->localize_datetime( $entry['datetime']['insert'] ) }}
                                </span>
                            </td>
                            <td>
                                <div class="table-row-controls" data-id="{{ $entry['id'] }}">
                                    <button class="btn btn-warning ms-2" value="edit" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                        <span class="btn-label ms-1">
                                            Edit
                                        </span>
                                    </button>
                                    <button class="btn btn-danger ms-2" value="delete" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                        <span class="btn-label ms-1">
                                            Delete
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>



    <div class="modal fade" id="register_resource_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        User
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="swg swg-form" id="register_resource_form">
                        <input type="hidden" class="input" name="id" value="">

                        <div class="p-2">
                            <div class="row mt-2">
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control input" name="username" placeholder="username" data-required>
                                        <label>Username *</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control input" name="email" placeholder="email" data-required>
                                        <label>Email *</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control input" name="name" placeholder="name">
                                        <label>Name</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control input" name="surname" placeholder="surname">
                                        <label>Surname</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col">
                                    <label class="d-block">
                                        Type *
                                        <select class="form-control input" name="hierarchy" data-required>
                                            <option value=""></option>
                                            <option disabled></option>

                                            @foreach ( $hierarchy_list as $entry )
                                                <option value="{{ $entry['id'] }}">
                                                    {{ $entry['type'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col">
                                    <span class="polymorph-block" data-id="register" hidden>
                                        <button type="submit" class="btn btn-primary m-auto d-table">
                                            Add
                                        </button>
                                    </span>
                                    <span class="polymorph-block" data-id="change" hidden>
                                        <button type="submit" class="btn btn-primary m-auto d-table">
                                            Save
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('view.script')

    <script name="button">
    
        // (Click-Event on the element)
        $('#register_resource_button').on('click', function () {
            // (Resetting the form)
            $('#register_resource_form')[0].swg.reset();

            // (Setting the property)
            $('#register_resource_form .input[name="email"]').prop('disabled', false);



            // (Setting the attributes)
            $('#register_resource_modal .polymorph-block').attr('hidden', true);
            $('#register_resource_modal .polymorph-block[data-id="register"]').attr('hidden', false);



            // (Showing the modal)
            $('#register_resource_modal').modal('show');
        });

    </script>

    <script name="table">

        // (Creating a DataTable)
        new simpleDatatables.DataTable( $('#resource_table')[0] );

        // (Click-Event on the element)
        $('#resource_table .table-row-controls .btn').on('click', async function () {
            // (Getting the values)
            const id     = $(this).closest('.table-row-controls').attr('data-id');
            const action = $(this).val();



            // (Setting the value)
            let response = null;

            switch ( action )
            {
                case 'edit':
                    // (Sending an http request)
                    response = await Solenoid.HTTP.sendRequest
                    (
                        '',
                        'RPC',
                        {
                            'Action': `${ resourceType }::find`
                        },
                        JSON.stringify
                        (
                            {
                                'key':   'id',
                                'value': id
                            }
                        ),
                        'json'
                    )
                    ;

                    if ( response.status.code !== 200 )
                    {// (Request failed)
                        // (Alerting the value)
                        alert( response.body['error']['message'] );

                        // Returning the value
                        return;
                    }



                    // (Resetting the form)
                    $('#register_resource_form')[0].swg.reset();

                    // (Setting the input)
                    $('#register_resource_form')[0].swg.setInput
                    (
                        {
                            'id':        response.body[ resourceType ]['id'],

                            'username':  response.body[ resourceType ]['username'],
                            'email':     response.body[ resourceType ]['email'],

                            'name':      response.body[ resourceType ]['profile']['name'],
                            'surname':   response.body[ resourceType ]['profile']['surname'],

                            'hierarchy': response.body[ resourceType ]['hierarchy']['id']
                        }
                    )
                    ;

                    // (Setting the property)
                    $('#register_resource_form .input[name="email"]').prop('disabled', true);



                    // (Setting the attributes)
                    $('#register_resource_modal .polymorph-block').attr('hidden', true);
                    $('#register_resource_modal .polymorph-block[data-id="change"]').attr('hidden', false);



                    // (Showing the modal)
                    $('#register_resource_modal').modal('show');
                break;

                case 'delete':
                    if ( !confirm( 'Are you sure to remove this entry ?' ) ) return;



                    // (Sending an http request)
                    response = await Solenoid.HTTP.sendRequest
                    (
                        '',
                        'RPC',
                        {
                            'Action': `${ resourceType }::unregister`
                        },
                        JSON.stringify
                        (
                            {
                                'id': id
                            }
                        ),
                        'json'
                    )
                    ;

                    if ( response.status.code !== 200 )
                    {// (Request failed)
                        // (Alerting the value)
                        alert( response.body['error']['message'] );

                        // Returning the value
                        return;
                    }



                    // (Setting the location)
                    window.location.href = '';
                break;
            }
        });

    </script>

    <script name="form">

        // (Setting the value)
        const resourceType = 'user';
    


        // (Creating a Form)
        const form = new Solenoid.SWG.Form( $('#register_resource_form')[0] );

        // (Listening for the events)
        form.addEventListener('submit', async function (event) {
            if ( Object.keys( form.validate( true ) ).length > 0 ) return;



            // (Getting the value)
            const input = await form.getInput();



            // (Sending an http request)
            const response = await Solenoid.HTTP.sendRequest
            (
                '',
                'RPC',
                {
                    'Action': `${ resourceType }::${ input['id'] ? 'change' : 'register' }`
                },
                JSON.stringify( input ),
                'json'
            )
            ;

            if ( response.status.code !== 200 )
            {// (Request failed)
                // (Alerting the value)
                alert( response.body['error']['message'] );

                // Returning the value
                return;
            }



            if ( input['id'] )
            {// Value found
                // (Setting the location)
                window.location.href = '';
            }
            else
            {// Value not found
                // (Creating a Connection)
                const connection = new Solenoid.SSE.Connection( '/authorization' );

                // (Listening for the event)
                connection.addEventListener('close', function (event) {
                    // (Closing the connection)
                    connection.close();



                    // (Stopping the spinner)
                    spinner.stop();



                    // (Setting the location)
                    window.location.href = '';
                });



                // (Opening the connection)
                connection.open();



                // (Starting the spinner)
                spinner.start( `Confirm Authorization via <b>${ response.body['receiver'] }</b>`, response.body['exp_time'] - Math.floor( new Date().valueOf() / 1000 ) );
            }
        });
    
    </script>

@endsection