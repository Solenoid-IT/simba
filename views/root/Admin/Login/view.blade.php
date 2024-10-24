@extends('layouts/empty.blade.php')

@section('view.head')

    <title>
        Login
    </title>

@endsection

@section('view.body')

    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Login</h3>
                                </div>
                                <div class="card-body">
                                    <form class="swg swg-form" id="login_form">
                                        <input type="hidden" class="input" name="idk" value="">

                                        <div class="std-login-box">
                                            <div class="row mt-3">
                                                <div class="col">
                                                    <input type="text" class="form-control input" name="username" placeholder="username" data-required>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col">
                                                    <div class="swg swg-passwordfield input-group mb-3" id="passwordfield">
                                                        <input type="password" class="form-control input" name="password" placeholder="password" data-required>
                                                        <div class="input-group-append">
                                                            <button type="button" class="btn btn-secondary passwordfield-button" value="toggle" title="show">
                                                                <span class="passwordfield-button-state" data-value="text">
                                                                    <i class="fa-solid fa-eye"></i>
                                                                </span>
                                                                <span class="passwordfield-button-state" data-value="password" hidden>
                                                                    <i class="fa-solid fa-eye-slash"></i>
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-check mb-3">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input input" name="remember">
                                                    Remember
                                                </label>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center mt-4 mb-0">
                                            {{--<a class="small" href="password.html">Forgot Password?</a>--}}
                                            <button type="submit" class="btn btn-primary">
                                                Login
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small">
                                        <a id="import_idk_button" style="cursor: pointer;">
                                            Import IDK
                                        </a>
                                        <a id="eject_idk_button" style="cursor: pointer;" hidden>
                                            Eject IDK
                                        </a>

                                        <form id="import_idk_form">
                                            <input type="file" class="input" name="file" accept=".idk" hidden>

                                            <button type="submit" hidden>
                                                Import IDK
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            @include ('components/footer.blade.php')
        </div>
    </div>

@endsection



@section('view.script')

    <script name="passwordfield">

        // (Creating a PasswordField)
        const passwordField = new Solenoid.SWG.PasswordField
        (
            {
                'element': $('#passwordfield')[0]
            }
        )
        ;

    </script>

    <script name="form">

        // (Creating a Form)
        const loginForm = new Solenoid.SWG.Form( $('#login_form')[0] );

        // (Listening for the event)
        loginForm.addEventListener('submit', async function (event) {
            if ( Object.keys( loginForm.validate( true ) ).length > 0 ) return;



            // (Sending an http request)
            const response = await Solenoid.HTTP.sendRequest
            (
                '',
                'RPC',
                {
                    'Action': 'user::start_session'
                },
                JSON.stringify( await loginForm.getInput() ),
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



            if ( response.body && response.body['mfa'] )
            {// Value found
                // (Creating a connection)
                const connection = new Solenoid.SSE.Connection( '/admin/authorization' );

                // (Listening for the event)
                connection.addEventListener('close', function (event) {
                    // (Closing the connection)
                    connection.close();



                    // (Stopping the spinner)
                    spinner.stop();



                    // (Setting the location)
                    window.location.href = '/admin';
                });



                // (Opening the connection)
                connection.open();

                // (Starting the spinner)
                spinner.start( 'Confirm Authorization via EMAIL', response.body['exp_time'] - Solenoid.DateTime.fetchCurrentTimestamp() );
            }
            else
            {// Value not found
                // (Setting the location)
                window.location.href = '/admin';
            }
        });

    </script>

    <script name="idk">

        // (Getting the value)
        let idk = Solenoid.LocalStorage.read( 'idk' );

        if ( idk !== null )
        {// Value found
            // (Setting the class)
            $('#login_form .input:not([name="idk"])').addClass('input-ignore');

            // (Setting the attribute)
            $('.std-login-box').attr('hidden', true);



            // (Getting the value)
            $('#login_form .input[name="idk"]').val( idk.value );
        }



        // (Setting the attributes)
        $('#import_idk_button').attr( 'hidden', idk ? true : false );
        $('#eject_idk_button').attr( 'hidden', idk ? false : true );



        // (Creating a Form)
        const importIDKForm = new Solenoid.SWG.Form( $('#import_idk_form')[0] );

        // (Listening for the event)
        importIDKForm.addEventListener('submit', async function (event) {
            // (Saving the IDK)
            idk = Solenoid.LocalStorage.save( 'idk', atob( ( await importIDKForm.getInput() )['file']['content'] ) );



            // (Setting the location)
            window.location.href = '';
        });



        // (Change-Event on the element)
        $('#import_idk_form .input[name="file"]').on('change', function () {
            // (Triggering the event)
            importIDKForm.triggerEvent('submit');
        });

        // (Click-Event on the element)
        $('#import_idk_button').on('click', function () {
            // (Triggering the event)
            $('#import_idk_form .input[name="file"]').trigger('click');
        });

        // (Click-Event on the element)
        $('#eject_idk_button').on('click', function () {
            if ( !confirm( 'Are you sure to remove the current IDK from local memory ?' ) ) return;



            // (Setting the value)
            idk = null;

            // (Setting the value)
            Solenoid.LocalStorage.clear( 'idk' );

            // (Setting the location)
            window.location.href = '';
        });
    
    </script>

@endsection