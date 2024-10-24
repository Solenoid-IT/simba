@php

    // (Getting the values)
    $default_profile_photo = '/assets/images/user.png';
    $profile_photo         = $user['profile']['photo'] ? 'data:' . $user['profile']['photo']['type'] . ';base64,' . $user['profile']['photo']['content'] : $default_profile_photo;

@endphp



<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark fixed-top">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="/">
        Simba
    </a>

    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!" title="Sidebar ON/OFF (S)">
        <i class="fas fa-bars"></i>
    </button>



    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
        {{--
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
            <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
        </div>
        --}}
    </form>

    <!-- GitHub Link -->
    <a class="btn btn-link btn-sm me-2" href="https://github.com/Solenoid-IT/simba" target="_blank" title="GitHub Link">
        <i class="fa-brands fa-github" style="height: 20px; color: #9a9c9e;"></i>
    </a>

    <!-- Fullscreen Button -->
    <button class="btn btn-link btn-sm me-2" id="fullscreen_button" title="Fullscreen ON/OFF (F)">
        <i class="fa-solid fa-expand" style="height: 20px; color: #9a9c9e;"></i>
    </button>

    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw"></i>
            </a>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item" data-toggle="modal" data-target="#change_profile_modal">
                        Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" data-toggle="modal" data-target="#change_security_modal">
                        Security
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>

                @if ( $user['hierarchy']['type'] === 'root' )
                    <li>
                        <a class="dropdown-item" href="/admin/users">
                            Users
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                @endif

                <li>
                    <a class="dropdown-item" href="/admin/logout">
                        Logout
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>



<div class="modal fade" id="change_profile_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 900px;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                Profile
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form class="swg swg-form" id="change_profile_form">
                <div class="p-2">
                    <div class="row mt-2">
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control input" name="name" value="{{ $user['profile']['name'] }}" placeholder="name">
                                <label>Name</label>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control input" name="surname" value="{{ $user['profile']['surname'] }}" placeholder="surname">
                                <label>Surname</label>
                            </div>
                        </div>
                        <div class="col" style="position: relative;">
                            <div class="form-floating mb-3">
                                <select class="form-control input" name="photo_action">
                                    <option value=""></option>
                                    <option disabled></option>

                                    <option value="change">
                                        Change
                                    </option>
                                    <option value="remove">
                                        Remove
                                    </option>
                                </select>

                                <label>Photo</label>

                                <input type="file" class="form-control input" name="photo" placeholder="photo" hidden>
                            </div>

                            <img class="profile-image" src="{{ $profile_photo }}" alt="" style="margin: 10px; margin-right: 20px; position: absolute; top: 0; right: 0;">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <button type="submit" class="btn btn-primary m-auto d-table">
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <form class="swg swg-form mt-3" id="change_username_form">
                <div class="row">
                    <div class="col">Username</div>
                </div>

                <div class="row p-2 mt-2">
                    <div class="col">
                        <input type="text" class="form-control input" name="username" value="{{ $user['username'] }}" placeholder="username" data-required>
                    </div>
                    <div class="col flex-grow-0">
                        <button type="submit" class="btn btn-primary m-auto d-table">
                            Save
                        </button>
                    </div>
                </div>
            </form>

            <form class="swg swg-form mt-3" id="change_email_form">
                <div class="row">
                    <div class="col">Email</div>
                </div>

                <div class="row p-2 mt-2">
                    <div class="col">
                        <input type="text" class="form-control input" name="email" value="{{ $user['email'] }}" placeholder="email" data-required>
                    </div>
                    <div class="col flex-grow-0">
                        <button type="submit" class="btn btn-primary m-auto d-table">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="change_security_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 700px;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                Security
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form class="swg swg-form form-not-idk" id="change_password_form" {{ $user['security']['idk']['authentication'] ? 'hidden' : '' }}>
                <div class="row">
                    <div class="col">Password</div>
                </div>

                <div class="row p-2 mt-2">
                    <div class="col">
                        <div class="swg swg-passwordfield input-group mb-3" id="security_passwordfield">
                            <input type="password" class="form-control input" name="password" placeholder="password" data-required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-secondary passwordfield-button" value="generate" title="generate">
                                    <i class="fa-solid fa-dice"></i>
                                </button>

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

                        <div class="swg swg-passwordfield-strengthmeter" id="security_passwordfield_strengthmeter">
                            <div class="progress-bar">
                                <div class="progress-value"></div>
                            </div>
                            <div class="progress-description"></div>
                        </div>
                    </div>
                    <div class="col flex-grow-0">
                        <button type="submit" class="btn btn-primary m-auto d-table">
                            Save
                        </button>
                    </div>
                </div>
            </form>

            <form class="swg swg-form form-not-idk mt-3" id="change_mfa_form" {{ $user['security']['idk']['authentication'] ? 'hidden' : '' }}>
                <div class="row">
                    <div class="col">Multi-Factor Authentication ( <b>MFA</b> )</div>
                </div>

                <div class="row p-2 mt-2">
                    <div class="col d-flex justify-content-start align-items-center">
                        <label class="toggle">
                            <input type="checkbox" class="toggle__input input" name="mfa" {{ $user['security']['mfa'] ? 'checked' : '' }}>

                            <span class="toggle-track">
                                <span class="toggle-indicator">
                                    <!-- 	This check mark is optional	 -->
                                    <span class="checkMark">
                                        <svg viewBox="0 0 24 24" role="presentation" aria-hidden="true">
                                            <path d="M9.86 18a1 1 0 01-.73-.32l-4.86-5.17a1.001 1.001 0 011.46-1.37l4.12 4.39 8.41-9.2a1 1 0 111.48 1.34l-9.14 10a1 1 0 01-.73.33h-.01z"></path>
                                        </svg>
                                    </span>
                                </span>
                            </span>

                            Enabled
                        </label>
                    </div>
                </div>
            </form>

            <form class="swg swg-form mt-3" id="idk_form">
                <div class="row">
                    <div class="col">Identity-Key ( <b>IDK</b> )</div>
                </div>

                <div class="row p-2 mt-2">
                    <div class="col">
                        <button type="submit" class="btn btn-primary" value="generate">
                            <i class="fa-solid fa-dice"></i>
                            <span class="ms-2">
                                Generate
                            </span>
                        </button>

                        <button type="submit" class="btn btn-secondary ms-2" value="import">
                            <i class="fa-solid fa-upload"></i>
                            <span class="ms-2">
                                Import
                            </span>

                            <input type="file" class="input" name="file" accept=".idk" hidden>
                        </button>
                    </div>
                    <div class="col d-flex justify-content-end align-items-center">
                        <div class="eject-idk-box" hidden>
                            <button type="submit" class="btn btn-danger" value="eject">
                                <i class="fa-solid fa-eject"></i>
                                <span class="ms-2">
                                    Eject
                                </span>
                            </button>

                            <span class="import-datetime-box ms-3">
                                Imported at <span class="import-datetime-value ms-2">-</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="row p-2 mt-2">
                    <div class="col">
                        <label class="toggle">
                            <input type="checkbox" class="toggle__input input" name="idk_authentication" {{ $user['security']['idk']['authentication'] ? 'checked' : '' }}>

                            <span class="toggle-track">
                                <span class="toggle-indicator">
                                    <!-- 	This check mark is optional	 -->
                                    <span class="checkMark">
                                        <svg viewBox="0 0 24 24" role="presentation" aria-hidden="true">
                                            <path d="M9.86 18a1 1 0 01-.73-.32l-4.86-5.17a1.001 1.001 0 011.46-1.37l4.12 4.39 8.41-9.2a1 1 0 111.48 1.34l-9.14 10a1 1 0 01-.73.33h-.01z"></path>
                                        </svg>
                                    </span>
                                </span>
                            </span>

                            IDK Authentication
                        </label>
                    </div>
                </div>
            </form>

            <form class="swg swg-form mt-3" id="view_access_form">
                <div class="row">
                    <div class="col">Access Log</div>
                </div>

                <div class="row p-2 mt-2">
                    <div class="col">
                        <a class="btn btn-primary" href="/admin/access_log">
                            <i class="fa-solid fa-list"></i>
                            <span class="ms-2">
                                View
                            </span>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
  </div>
</div>



<div class="modal fade" id="set_password_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 540px;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                Required Action
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col text-center">
                    <b>Password</b> must be set !
                </div>
            </div>

            <div class="row mt-4">
                <div class="col">
                    <button type="submit" class="btn btn-primary m-auto d-table" id="set_password_button">
                        <span>
                            Set
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</div>

<div class="modal fade" id="idk_download_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 540px;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                IDK
            </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <b>IDK</b> has been generated :: Download it and keep it in a secure place !
            <br>
            <br>

            <button type="submit" class="btn btn-primary m-auto d-table" id="download_idk_button">
                <i class="fa-solid fa-download"></i>
                <span class="ms-2">
                    Download
                </span>
            </button>
        </div>
    </div>
  </div>
</div>



<script name="sidebar">

    // (KeyUp-Event on the element)
    $(window).on('keyup', function (event) {
        if ( document.activeElement !== document.body ) return;

        if ( event.key === 's' )
        {// Match OK
            // (Triggering the event)
            $('#sidebarToggle').trigger('click');
        }
    });

</script>

<script name="fullscreen">

    // (Click-Event on the element '#fullscreen_button')
    $('#fullscreen_button').on('click', function () {
        if ( window.fullScreen )
        {// Value is true
            // (Exiting from the fullscreen)
            document.exitFullscreen();
        }
        else
        {// Value is false
            // (Requesting the fullscreen)
            document.body.requestFullscreen();
        }
    });

    // (KeyUp-Event on the element window)
    $(window).on('keyup', function (event) {
        if ( document.activeElement !== document.body )
        {// Match failed
            // Returning the value
            return;
        }



        if ( event.key === 'f' )
        {// Match OK
            // (Triggering the event)
            $('#fullscreen_button').trigger('click');
        }
    });

</script>

<script name="select">

    // (Change-Event on the element)
    $('#change_profile_form .input[name="photo_action"]').on('change', function () {
        // (Getting the value)
        const value = $(this).val();

        switch ( value )
        {
            case '':
                // (Setting the value)
                $('#change_profile_form .input[name="photo"]').val('');

                // (Setting the value)
                $('#change_profile_form .profile-image').attr('src', '{{ $profile_photo }}');
            break;

            case 'change':
                // (Triggering the event)
                $('#change_profile_form .input[name="photo"]').trigger('click');
            break;

            case 'remove':
                // (Setting the value)
                $('#change_profile_form .input[name="photo"]').val('');

                // (Setting the value)
                $('#change_profile_form .profile-image').attr('src', '{{ $default_profile_photo }}');
            break;
        }
    });

</script>

<script name="file">

    // (Change-Event on the element)
    $('#change_profile_form .input[name="photo"]').on('change', async function () {
        // (Getting the value)
        const input = await changeProfileForm.getInput();

        // (Setting the value)
        $('#change_profile_form .profile-image').attr('src', `data:${ input['photo']['type'] };base64,${ input['photo']['content'] }`);
    });

</script>

<script name="form">

    // (Creating a Form)
    const changeProfileForm = new Solenoid.SWG.Form( $('#change_profile_form')[0] );

    // (Listening for the event)
    changeProfileForm.addEventListener('submit', async function (event) {
        if ( Object.keys( changeProfileForm.validate( true ) ).length > 0 ) return;



        // (Sending an http request)
        const response = await Solenoid.HTTP.sendRequest
        (
            '/admin/user',
            'RPC',
            {
                'Action': 'profile::change'
            },
            JSON.stringify( await changeProfileForm.getInput() ),
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



        // (Alerting the value)
        alert( 'Profile has been changed' );

        // (Setting the location)
        window.location.href = '';
    });



    // (Creating a Form)
    const changeUsernameForm = new Solenoid.SWG.Form( $('#change_username_form')[0] );

    // (Listening for the event)
    changeUsernameForm.addEventListener('submit', async function (event) {
        if ( !confirm('Are you sure to change the username ?') ) return;



        if ( Object.keys( changeUsernameForm.validate( true ) ).length > 0 ) return;



        // (Sending an http request)
        const response = await Solenoid.HTTP.sendRequest
        (
            '/admin/user',
            'RPC',
            {
                'Action': 'username::change'
            },
            JSON.stringify( await changeUsernameForm.getInput() ),
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



        // (Alerting the value)
        alert( 'Username has been changed' );

        // (Setting the location)
        window.location.href = '';
    });



    // (Creating a Form)
    const changeEmailForm = new Solenoid.SWG.Form( $('#change_email_form')[0] );

    // (Listening for the event)
    changeEmailForm.addEventListener('submit', async function (event) {
        if ( !confirm('Are you sure to change the email ?') ) return;



        if ( Object.keys( changeEmailForm.validate( true ) ).length > 0 ) return;



        // (Sending an http request)
        const response = await Solenoid.HTTP.sendRequest
        (
            '/admin/user',
            'RPC',
            {
                'Action': 'email::change'
            },
            JSON.stringify( await changeEmailForm.getInput() ),
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



        /*

        // (Creating a Connection)
        const connection = new Solenoid.SSE.Connection( '/admin/authorization' );

        // (Listening for the event)
        connection.addEventListener('close', function (event) {
            // (Closing the connection)
            connection.close();



            // (Stopping the spinner)
            spinner.stop();



            // (Alerting the value)
            alert( 'Email has been changed' );

            // (Setting the location)
            window.location.href = '';
        });



        // (Opening the connection)
        connection.open();



        // (Starting the spinner)
        spinner.start( `Confirm Authorization via <b>${ response.body['receiver'] }</b>`, response.body['exp_time'] - Solenoid.DateTime.fetchCurrentTimestamp() );

        */



        // (Alerting the value)
        alert( 'Email has been changed' );

        // (Setting the location)
        window.location.href = '';
    });






    // (Creating a Form)
    const changePasswordForm = new Solenoid.SWG.Form( $('#change_password_form')[0] );

    // (Listening for the event)
    changePasswordForm.addEventListener('submit', async function (event) {
        if ( !confirm('Are you sure to change the password ?') ) return;



        if ( Object.keys( changePasswordForm.validate( true ) ).length > 0 ) return;



        // (Sending an http request)
        const response = await Solenoid.HTTP.sendRequest
        (
            '/admin/user',
            'RPC',
            {
                'Action': 'security::change'
            },
            JSON.stringify( await changePasswordForm.getInput() ),
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



        // (Alerting the value)
        alert( 'Password has been changed' );

        // (Setting the location)
        window.location.href = '';
    });



    // (Creating a Form)
    const changeMFAForm = new Solenoid.SWG.Form( $('#change_mfa_form')[0] );

    // (Listening for the event)
    changeMFAForm.addEventListener('submit', async function (event) {
        if ( Object.keys( changeMFAForm.validate( true ) ).length > 0 ) return;



        // (Sending an http request)
        const response = await Solenoid.HTTP.sendRequest
        (
            '/admin/user',
            'RPC',
            {
                'Action': 'security::change'
            },
            JSON.stringify( await changeMFAForm.getInput() ),
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

    // (Change-Event on the element)
    $('#change_mfa_form .input[name="mfa"]').on('change', function () {
        // (Triggering the event)
        changeMFAForm.triggerEvent('submit');
    });






    // (Setting the value)
    let idk = null;



    // (Creating a Form)
    const idkForm = new Solenoid.SWG.Form( $('#idk_form')[0] );

    // (Listening for the event)
    idkForm.addEventListener('submit', async function (event) {
        switch ( event.submitter.value )
        {
            case 'generate':
                if( !confirm( 'Are you sure to generate a new IDK ?' ) ) return;



                // (Sending an http request)
                const response = await Solenoid.HTTP.sendRequest
                (
                    '/admin/user',
                    'RPC',
                    {
                        'Action': 'idk::generate'
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



                // (Getting the value)
                idk = response.body['idk'];



                // (Showing the modal)
                $('#idk_download_modal').modal('show');
            break;
            
            case 'import':
                // (Triggering the event)
                $('#idk_form button[value="import"] .input').trigger('click');
            break;

            case 'eject':
                if( !confirm( 'Are you sure to remove the current IDK from local memory ?' ) ) return;



                // (Setting the value)
                idk = null;

                // (Setting the value)
                Solenoid.LocalStorage.clear( 'idk' );



                // (Setting the value)
                $('.import-datetime-value').html('');

                // (Setting the attribute)
                $('.eject-idk-box').attr('hidden', true);
            break;
        }
    });

    // (Change-Event on the element)
    $('#idk_form button[value="import"] .input').on('change', async function () {
        // (Saving the IDK)
        idk = Solenoid.LocalStorage.save( 'idk', atob( ( await idkForm.getInput() )['file']['content'] ) );



        // (Setting the value)
        $('.import-datetime-value').html( Solenoid.DateTime.format( idk['saved_time'] ) );

        // (Setting the attribute)
        $('.eject-idk-box').attr('hidden', false);
    });

    // (Change-Event on the element)
    $('#idk_form .input[name="idk_authentication"]').on('change', async function (event) {
        if ( $(this).prop('checked') )
        {// Value is true
            if ( !confirm("Are you sure to enable the IDK authentication ?\n\nThis login method allow you authenticating only by importing the IDK file.") )
            {// (Confirmation failed)
                // (Setting the value)
                $(this).prop( 'checked', false );



                // Returning the value
                return;
            }
        }



        // (Sending an http request)
        const response = await Solenoid.HTTP.sendRequest
        (
            '/admin/user',
            'RPC',
            {
                'Action': 'security::change'
            },
            JSON.stringify
            (
                {
                    'idk_authentication': $(this).prop('checked')
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



        // (Setting the attribute)
        $('.form-not-idk').attr('hidden', $(this).prop('checked'));
    });

</script>

<script name="passwordfield">

    // (Creating a PasswordField)
    const passwordField = new Solenoid.SWG.PasswordField
    (
        {
            'element': $('#security_passwordfield')[0],

            'strengthMeter':
            {
                'element': $('#security_passwordfield_strengthmeter')[0],
                'entropy': 128
            },

            'generator':
            {
                'length':     32,
                'minEntropy': 128
            }
        }
    )
    ;

</script>

<script name="button">
    
    // (Click-Event on the element)
    $('#download_idk_button').on('click', function () {
        // (Downloading the file)
        Solenoid.URL.download( Solenoid.URL.buildDataURL( idk, 'text/plain' ), `${ window.location.host }.idk` );



        // (Showing the modal)
        $('#idk_download_modal').modal('hide');
    });

</script>

<script name="idk-eject-box">

    // (Getting the value)
    idk = Solenoid.LocalStorage.read( 'idk' );

    if ( idk !== null )
    {// Value found
        // (Setting the value)
        $('.import-datetime-value').html( Solenoid.DateTime.format( idk['saved_time'] ) );

        // (Setting the attribute)
        $('.eject-idk-box').attr('hidden', false);
    }

</script>



@if ( $session->data['set_password'] )
    <script name="set_password">

        // (Setting the timeout)
        setTimeout
        (
            function ()
            {
                // (Showing the modal)
                $('#set_password_modal').modal('show');
            },
            2000
        )
        ;


        // (Click-Event on the element)
        $('#set_password_button').on('click', function () {
            // (Hiding the modal)
            $('#set_password_modal').modal('hide');

            // (Showing the modal)
            $('#change_security_modal').modal('show');
        });
    
    </script>
@endif