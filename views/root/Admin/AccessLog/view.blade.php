@php



use \App\Stores\Localizer as LocalizerStore;



// (Initializing the store)
LocalizerStore::read();



@endphp



@extends('layouts/base.blade.php')



@section('view.head')
    <title>
        Access Log
    </title>
@endsection



@section('view.body')

    <h1 class="mt-4">Access Log</h1>

    <div class="row">
        <div class="col d-flex justify-content-end">
            <button class="btn btn-danger ms-2" id="empty_log_button" title="Empty Log">
                <i class="fa-solid fa-trash"></i>
                <span class="btn-label ms-1">
                    Empty
                </span>
            </button>
        </div>
    </div>

    <div class="card mt-3 mb-4">
        <div class="card-body">
            <table id="resource_table">
                <thead>
                    <tr>
                        <th>DateTime</th>

                        @if ( $user['hierarchy']['type'] === 'root' )
                            <th>User</th>
                        @endif

                        <th>IP</th>
                        <th>Browser</th>
                        <th>OS</th>
                        <th>HW</th>
                        <th>Login Method</th>
                        <th>Session</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $access)
                        <tr>
                            <td>
                                <span class="local-datetime" title="{{ $access['datetime']['insert'] }} UTC">
                                    {{ LocalizerStore::fetch()->localize_datetime( $access['datetime']['insert'] ) }}
                                </span>
                            </td>

                            @if ( $user['hierarchy']['type'] === 'root' )
                                <td>
                                    <div title="{{ $access['user']['name'] }} {{ $access['user']['surname'] }}">
                                        {{ $access['user']['username'] }}
                                    </div>
                                </td>
                            @endif

                            <td>
                                {{ $access['ip']['address'] }}
                                 • 
                                {{ $access['ip']['country']['code'] }}
                                 • 
                                {{ $access['ip']['country']['name'] }}
                                 • 
                                <img class="country-flag" src="https://flagsapi.com/{{ $access['ip']['country']['code'] }}/flat/64.png" alt="">
                                 • 
                                {{ $access['ip']['isp'] }}
                            </td>
                            <td>
                                {{ $access['browser'] }}
                            </td>
                            <td>
                                {{ $access['os'] }}
                            </td>
                            <td>
                                {{ $access['hw'] }}
                            </td>
                            <td>
                                {{ $access['login_method'] }}
                            </td>
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    @if ( $access['session'] )
                                        <i class="fa-solid fa-circle" style="color: #49b500;"></i>
                                        <span class="ms-2" style="margin-bottom: 2px;">
                                            active
                                        </span>
                                    @else
                                        <i class="fa-solid fa-circle" style="color: #a0a0a0;"></i>
                                        <span class="ms-2" style="margin-bottom: 2px;">
                                            disconnected
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="table-row-controls" data-id="{{ $access['session'] }}">
                                    <button class="btn btn-danger ms-2" value="disconnect_client" title="Disconnect Client" {{ $access['session'] ? '' : 'disabled' }}>
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                        <span class="btn-label ms-1">
                                            Disconnect Client
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

@endsection



@section('view.script')

    <script name="button">
    
        // (Click-Event on the element)
        $('#empty_log_button').on('click', async function () {
            if ( !confirm('Are you sure to empty the log ?') ) return;



            // (Sending an http request)
            const response = await Solenoid.HTTP.sendRequest
            (
                '',
                'RPC',
                {
                    'Action': 'access::unregister'
                },
                '',
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
                case 'disconnect_client':
                    if ( !confirm( 'Are you sure to disconnect this device ?' ) )
                    {// (Confirmation failed)
                        // Returning the value
                        return;
                    }



                    // (Sending an http request)
                    response = await Solenoid.HTTP.sendRequest
                    (
                        '/admin/user',
                        'RPC',
                        {
                            'Action': `session::unregister`
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

@endsection