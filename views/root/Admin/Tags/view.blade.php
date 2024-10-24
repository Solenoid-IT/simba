@php



use \Solenoid\Color\HSL;

use \App\Stores\Localizer as LocalizerStore;



// (Initializing the store)
LocalizerStore::read();



@endphp



@extends('layouts/base.blade.php')



@section('view.head')
    <title>
        Tags
    </title>
@endsection



@section('view.body')

    <h1 class="mt-4">Tags</h1>

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
                        <th>Value</th>
                        <th>Color</th>
                        <th>Insert DateTime</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $tag)
                        <tr>
                            <td>
                                {{ $tag['id'] }}
                            </td>
                            <td>
                                {{ $tag['value'] }}
                            </td>
                            <td>
                                <div class="color-sample" style="background-color: {{ $tag['color'] }};"></div>
                            </td>
                            <td>
                                <span class="local-datetime" title="{{ $tag['datetime']['insert'] }} UTC">
                                    {{ LocalizerStore::fetch()->localize_datetime( $tag['datetime']['insert'] ) }}
                                </span>
                            </td>
                            <td>
                                <div class="table-row-controls" data-id="{{ $tag['id'] }}">
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
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Tag
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="swg swg-form" id="register_resource_form">
                        <input type="hidden" class="input" name="id" value="">

                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control input" name="value" placeholder="value" data-required>
                                    <label>Value</label>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-floating mb-3">
                                    <input type="color" class="form-control input" name="color" placeholder="color" value="#bfbfbf">
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 60px;">
                            <div class="col d-flex justify-content-center">
                                <span class="polymorph-block" data-id="register" hidden>
                                    <button type="submit" class="btn btn-primary m-auto">
                                        Add
                                    </button>
                                </span>
                                <span class="polymorph-block" data-id="change" hidden>
                                    <button type="submit" class="btn btn-primary m-auto">
                                        Save
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('view.script')

    <script name="table">

        // (Creating a DataTable)
        new simpleDatatables.DataTable( $('#resource_table')[0] );

        // (Click-Event on the element)
        $('body').delegate('#resource_table .table-row-controls .btn', 'click', async function () {
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
                    $('#register_resource_form')[0].swg.setInput( response.body[ resourceType ] );



                    // (Setting the attributes)
                    $('#register_resource_modal .polymorph-block').attr('hidden', true);
                    $('#register_resource_modal .polymorph-block[data-id="change"]').attr('hidden', false);



                    // (Showing the modal)
                    $('#register_resource_modal').modal('show');
                break;

                case 'delete':
                    if ( !confirm( 'Are you sure to remove this entry ?' ) )
                    {// (Confirmation failed)
                        // Returning the value
                        return;
                    }



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



        // (Change-Event on the element)
        $('.document-visibility-checkbox').on('change', async function () {
            // (Sending an http request)
            const response = await Solenoid.HTTP.sendRequest
            (
                '',
                'RPC',
                {
                    'Action': `${ resourceType }::set_active`
                },
                JSON.stringify
                (
                    {
                        'id':    $(this).attr('data-id'),
                        'value': $(this).prop('checked')
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
        });

    </script>

    <script name="button">
    
        // (Click-Event on the element)
        $('#register_resource_button').on('click', function () {
            // (Resetting the form)
            $('#register_resource_form')[0].swg.reset();



            // (Setting the attributes)
            $('#register_resource_modal .polymorph-block').attr('hidden', true);
            $('#register_resource_modal .polymorph-block[data-id="register"]').attr('hidden', false);



            // (Showing the modal)
            $('#register_resource_modal').modal('show');
        });

    </script>

    <script name="form">

        // (Setting the value)
        const resourceType = 'tag';
    


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



            // (Setting the location)
            window.location.href = '';
        });
    
    </script>

@endsection