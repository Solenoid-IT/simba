<svelte:window on:keydown={ onKeyDown }/>

<script>

    import { envs } from '../../envs.js';
    import { appData } from '../../stores/appData.js';
    import { idk } from '../../stores/idk.js';

    import Modal from '../../views/components/Modal.svelte';
    import Form from '../../views/components/Form.svelte';
    import PasswordField from './PasswordField.svelte';
    import Switch from './Switch.svelte';
    import Helper from './Helper.svelte';
    import PropTable from './PropTable.svelte';

    import { goto } from '$app/navigation';



    let profileModal;
    let securityModal;



    let changeNameForm;

    // Returns [Promise:bool]
    async function onChangeNameFormSubmit ()
    {
        // (Validating the form)
        let result = changeNameForm.validate();

        if ( !result.valid ) return false;



        if ( !confirm('Are you sure to change the name ?') ) return false;



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::change_name',
                'Content-Type: application/json'
            ],
            JSON.stringify( result.fetch() ),
            'json',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
            {// (Client not authorized)
                // (Moving to the URL)
                window.location.href = '/admin/login';



                // Returning the value
                return false;
            }



            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // (Getting the value)
        $appData.user.user.name = result.entries['name'].value;



        // (Alerting the value)
        //alert('Name has been changed');



        // Returning the value
        return true;
    }



    let changeEmailForm;
    let changeEmailFormMsg = '';

    // Returns [Promise:bool]
    async function onChangeEmailFormSubmit ()
    {
        // (Validating the form)
        let result = changeEmailForm.validate();

        if ( !result.valid ) return false;



        if ( !confirm('Are you sure to change the email ?') ) return false;



        // (Setting the value)
        changeEmailFormMsg = '';



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::change_email',
                'Content-Type: application/json'
            ],
            JSON.stringify( result.fetch() ),
            'json',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
            {// (Client not authorized)
                // (Moving to the URL)
                window.location.href = '/admin/login';



                // Returning the value
                return false;
            }



            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // (Alerting the value)
        alert(`Confirm operation by email "${ $appData.user.user.email }" ...\n\nClick on "OK" after you have confirmed`);

        // (Setting the location)
        window.location.href = '';



        // Returning the value
        return true;
    }



    let changeBirthDataForm;

    // Returns [Promise:bool]
    async function onChangeBirthDataFormSubmit ()
    {
        // (Validating the form)
        let result = changeBirthDataForm.validate();

        if ( !result.valid ) return false;



        if ( !confirm('Are you sure to change the birth data ?') ) return false;



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::change_birth_data',
                'Content-Type: application/json'
            ],
            JSON.stringify( result.fetch() ),
            'json',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
            {// (Client not authorized)
                // (Moving to the URL)
                window.location.href = '/admin/login';



                // Returning the value
                return false;
            }



            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // (Getting the values)
        $appData.user.user.birth.name    = result.entries['birth']['name'].value;
        $appData.user.user.birth.surname = result.entries['birth']['surname'].value;



        // (Alerting the value)
        //alert('Birth data have been changed');



        // Returning the value
        return true;
    }



    let userDestroyMsg = '';

    // Returns [Promise:bool]
    async function destroyUser ()
    {
        if ( !confirm('Are you sure to destroy the current user ?') ) return false;



        // (Setting the value)
        userDestroyMsg = '';



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::destroy',
                'Content-Type: application/json'
            ],
            '',
            'json',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
            {// (Client not authorized)
                // (Moving to the URL)
                window.location.href = '/admin/login';



                // Returning the value
                return false;
            }



            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // (Getting the value)
        userDestroyMsg = `Confirm operation by email '${ $appData.user.user.email }' ...`;



        // (Sending the request)
        const res = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::wait_authorization',
                'Content-Type: application/json'
            ],
            '',
            'json',
            true
        )
        ;

        if ( res.status.code !== 200 )
        {// (Request failed)
            if ( res.status.code === 401 )
            {// (Client is not authorized)
                // (Setting the location)
                window.location.href = '/admin/login';



                // Returning the value
                return false;
            }



            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // (Setting the location)
        window.location.href = '';



        // Returning the value
        return true;
    }



    let changePasswordForm;

    // Returns [Promise:bool]
    async function onChangePasswordFormSubmit ()
    {
        // (Validating the form)
        let result = changePasswordForm.validate();

        if ( !result.valid ) return false;



        if ( !confirm('Are you sure to change the password ?') ) return false;



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::change_password',
                'Content-Type: application/json'
            ],
            JSON.stringify( result.fetch() ),
            'json',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
            {// (Client not authorized)
                // (Moving to the URL)
                window.location.href = '/admin/login';



                // Returning the value
                return false;
            }



            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // (Setting the value)
        $appData.user['password_set'] = true;



        // (Resetting the form)
        changePasswordForm.reset();



        // (Alerting the value)
        //alert('Password has been changed');



        // Returning the value
        return true;
    }



    let mfaSwitch;

    // Returns [Promise:bool]
    async function onSetMfa (event)
    {
        // (Getting the value)
        const input =
        {
            'security.mfa': event.detail.target.checked
        }
        ;



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::change_mfa',
                'Content-Type: application/json'
            ],
            JSON.stringify( input ),
            'json',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
            {// (Client not authorized)
                // (Moving to the URL)
                window.location.href = '/admin/login';



                // Returning the value
                return false;
            }



            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    let idkSwitch;

    // Returns [Promise:bool]
    async function onSetIdk (event)
    {
        // (Getting the value)
        const checked = event.detail.target.checked;

        if ( checked )
        {// Value is true
            if ( !confirm("Are you sure to enable the IDK authentication ?\n\nWhen enabled you can authenticate only by providing the generated key") )
            {// (Confirmation is failed)
                // (Setting the property)
                jQuery( '#set_idk_form .form-input[name="idk"]' ).prop( 'checked', false );



                // Returning the value
                return false;
            }
        }



        // (Getting the value)
        const input =
        {
            'security.idk.authentication': checked
        }
        ;



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::change_idk',
                'Content-Type: application/json'
            ],
            JSON.stringify( input ),
            '',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
            {// (Client not authorized)
                // (Moving to the URL)
                window.location.href = '/admin/login';



                // Returning the value
                return false;
            }



            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        if ( checked )
        {// Value found
            // (Downloading the file)
            Solenoid.File.download( 'text/plain', window.location.host + '.idk', response.body );
        }



        // (Alerting the value)
        alert( 'IDK has been ' + ( checked ? "enabled.\n\nSave the downloaded key in a safe place !" : 'disabled' ) );



        // (Getting the value)
        $appData.user.idk = checked;



        // (Getting the value)
        $idk = response.body;

        // (Setting the item)
        localStorage.setItem( 'idk', $idk );



        // Returning the value
        return true;
    }



    let logoutModal;

    // Returns [Promise:bool]
    async function logout ()
    {
        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::logout',
                'Content-Type: application/json'
            ],
            '',
            'json',
            true
        )
        ;

        if ( response.status.code === 200 )
        {// (Request OK)
            // (Moving to the URL)
            window.location.href = '/admin/login';
        }
        else
        {// (Request failed)
            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    $:
        if ( $appData )
        {// Value found
            if ( !$appData.user['password_set'] )
            {// (Password is not set)
                if ( securityModal && changePasswordForm )
                {// Values found
                    // (Showing the modal)
                    securityModal.show();   



                    // (Resetting the form)
                    changePasswordForm.reset();

                    // (Validating the form)
                    changePasswordForm.validate();
                }
            }



            if ( mfaSwitch )
            {// Value found
                // (Getting the value)
                mfaSwitch.checked = $appData.user.mfa;
            }



            if ( idkSwitch )
            {// Value found
                // (Getting the value)
                idkSwitch.checked = $appData.user.idk;
            }
        }



    // Returns [Promise:void]
    async function importIDK ()
    {
        // (Selecting the files)
        const file = ( await Solenoid.File.select( '.idk' ) )[0];



        // (Getting the value)
        $idk = await Solenoid.File.read( file );

        // (Setting the item)
        localStorage.setItem( 'idk', $idk );
    }

    // Returns [void]
    function ejectIDK ()
    {
        if ( !confirm('Are you sure to remove the IDK from the local memory ?') ) return;



        // (Removing the item)
        localStorage.removeItem( 'idk' );

        // (Setting the value)
        $idk = null;
    }



    // Returns [void]
    function toggleFullscreen ()
    {
        if ( document.fullscreenElement )
        {// Value is true
            // (Disabling the fullscreen)
            document.exitFullscreen();
        }
        else
        {// Value is false
            // (Enabling the fullscreen)
            document.body.requestFullscreen();
        }
    }



    // Returns [void]
    function onKeyDown (event)
    {
        if ( document.activeElement.classList.contains('input') ) return;

        switch ( event.key )
        {
            case 'f':// (Fullscreen controls)
                // (Calling the function)
                toggleFullscreen();
            break;

            case 'o':// (Logout controls)
                // (Showing the modal)
                logoutModal.show();
            break;
        }
    }



    let alertModal;

    let alertIndex = null;
    let alertData  = {};

    // Returns [void]
    function viewAlert (index)
    {
        // (Getting the values)
        alertIndex = index;
        alertData  =
        {
            'datetime.insert': $appData.alerts[index]['datetime']['insert'],

            'action':          $appData.alerts[index]['action'],
            'description':     $appData.alerts[index]['description'],
            'ip':              `${ $appData.alerts[index]['ip'] } - ${ $appData.alerts[index]['ip_info']['country']['code'] } - ${ $appData.alerts[index]['ip_info']['isp'] }`,
            'browser':         $appData.alerts[index]['ua_info']['browser'],
            'os':              $appData.alerts[index]['ua_info']['os'],
            'hardware':        $appData.alerts[index]['ua_info']['hw'],

            'severity':        $appData.alerts[index]['alert_severity']
        }
        ;



        // (Showing the modal)
        alertModal.show();
    }

    // Returns [Promise:bool]
    async function markAlertAsRead (index)
    {
        // (Getting the value)
        const id = $appData.alerts[index].id;



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::mark_alert_as_read',
                'Content-Type: application/json'
            ],
            JSON.stringify
            (
                {
                    'id': id
                }
            ),
            'json',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request OK)
            // (Alerting the value)
            alert( response.body['error']['message'] );



            // Returning the value
            return false;
        }



        // (Getting the value)
        $appData.alerts = $appData.alerts.filter( function (entry) { return entry.id !== id } );



        // (Hiding the modal)
        alertModal.hide();



        // Returning the value
        return true;
    }

</script>

<!-- Topbar -->
<nav class="navbar navbar-expand topbar static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Brand -->
    <a class="brand-box nav-link d-flex align-items-center justify-content-center" href="/">
        <img src="{ envs.APP_URL }/assets/images/simba.jpg" alt="" class="app-logo">
        <div class="mx-3">SIMBA</div>
    </a>

    <!-- Topbar Search -->
    <form
        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
        <div class="input-group">
            <input type="text" class="form-control input form-input bg-light border-0 small" placeholder="Search ...">
            <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                    <i class="fas fa-search fa-sm"></i>
                </button>
            </div>
        </div>
    </form>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <!-- svelte-ignore a11y-invalid-attribute -->
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control input bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Nav Item - Alerts -->
        <li class="nav-item dropdown no-arrow mx-1">
            <!-- svelte-ignore a11y-invalid-attribute -->
            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="alerts">
                <i class="fas fa-bell fa-fw"></i>

                { #if $appData.alerts.length > 0 }
                    <!-- Counter - Alerts -->
                    <span class="badge badge-danger badge-counter">{ $appData.alerts.length }</span>
                { /if }
            </a>
            <!-- Dropdown - Alerts -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                    Alerts
                </h6>

                <!--
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="mr-3">
                            <div class="icon-circle bg-primary">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">December 12, 2019</div>
                            <span class="font-weight-bold">A new monthly report is ready to download!</span>
                        </div>
                    </a>
                    <a class="dropdown-item d-flex align-items-center" href="#">
                        <div class="mr-3">
                            <div class="icon-circle bg-success">
                                <i class="fas fa-donate text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">December 7, 2019</div>
                            $290.29 has been deposited into your account!
                        </div>
                    </a>
                -->

                { #each $appData.alerts as alert, i }
                    <!-- svelte-ignore a11y-click-events-have-key-events -->
                    <!-- svelte-ignore a11y-missing-attribute -->
                    <!-- svelte-ignore a11y-invalid-attribute -->
                    <a class="dropdown-item d-flex align-items-center" href="#" on:click={ () => { viewAlert(i); } }>
                        <div class="mr-3">
                            <div class="icon-circle bg-danger">
                                <i class="fas fa-exclamation-triangle text-white"></i>
                            </div>
                        </div>
                        <div>
                            <div class="small text-gray-500">{ alert['datetime']['insert'] }</div>
                            { alert['description'] }
                            <div class="small text-gray-500" style="font-size: 8px;">{ alert['ua_info']['browser'] } - { alert['ua_info']['os'] }</div>
                            <div class="small text-gray-500" style="font-size: 8px;">{ alert['ip'] } - { alert['ip_info']['country']['code'] }</div>
                        </div>
                    </a>
                { /each }

                <!--<a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>-->
            </div>
        </li>

        <!-- Nav Item - Messages -->
        <li class="nav-item dropdown no-arrow mx-1" style="display: none !important;">
            <!-- svelte-ignore a11y-invalid-attribute -->
            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">7</span>
            </a>

            <!-- Dropdown - Messages -->
            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                    Message Center
                </h6>
                <!-- svelte-ignore a11y-invalid-attribute -->
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="{ envs.APP_URL }/assets/tpl/sb-admin-2/img/undraw_profile_1.svg" alt="...">
                        <div class="status-indicator bg-success"></div>
                    </div>
                    <div class="font-weight-bold">
                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                            problem I've been having.</div>
                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                    </div>
                </a>
                <!-- svelte-ignore a11y-invalid-attribute -->
                <!-- svelte-ignore a11y-invalid-attribute -->
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="{ envs.APP_URL }/assets/tpl/sb-admin-2/img/undraw_profile_2.svg" alt="...">
                        <div class="status-indicator"></div>
                    </div>
                    <div>
                        <div class="text-truncate">I have the photos that you ordered last month, how
                            would you like them sent to you?</div>
                        <div class="small text-gray-500">Jae Chun · 1d</div>
                    </div>
                </a>
                <!-- svelte-ignore a11y-invalid-attribute -->
                <a class="dropdown-item d-flex align-items-center" href="#">
                    <div class="dropdown-list-image mr-3">
                        <img class="rounded-circle" src="{ envs.APP_URL }/assets/tpl/sb-admin-2/img/undraw_profile_3.svg" alt="...">
                        <div class="status-indicator bg-warning"></div>
                    </div>
                    <div>
                        <div class="text-truncate">Last month's report looks great, I am very happy with
                            the progress so far, keep up the good work!</div>
                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                    </div>
                </a>
                <!-- svelte-ignore a11y-invalid-attribute -->
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
            </div>
        </li>

        <!-- Nav Item - Fullscreen -->
        <li class="nav-item dropdown no-arrow mx-1">
            <!-- svelte-ignore a11y-invalid-attribute -->
            <a class="nav-link dropdown-toggle" href="#" role="button" title="Fullscreen ON/OFF (F)" on:click={ toggleFullscreen }>
                <i class="fa-solid fa-expand"></i>
            </a>
        </li>

        <div class="topbar-divider d-none d-sm-block"></div>

        { #if $appData }
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
                <!-- svelte-ignore a11y-invalid-attribute -->
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                    <span class="ml-2 mr-2 d-none d-lg-inline small">
                        <span style="border-bottom: 1px solid { $appData.hierarchies[ $appData.user.user.hierarchy ]['color'] }">{ $appData.user.user.name }</span>@{ $appData.user.tenant.name }
                    </span>
                </a>
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="userDropdown">
                    <!-- svelte-ignore a11y-invalid-attribute -->
                    <a class="dropdown-item" href="#" on:click={ profileModal.show }>
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <!-- svelte-ignore a11y-invalid-attribute -->
                    <a class="dropdown-item" href="#" on:click={ securityModal.show }>
                        <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                        Security
                    </a>
                    <a class="dropdown-item" href="/admin/activity_log">
                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                        Activity Log
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/admin/users">
                        <i class="fas fa-users fa-sm fa-fw mr-2 text-gray-400"></i>
                        Users
                    </a>
                    <div class="dropdown-divider"></div>
                    <!-- svelte-ignore a11y-invalid-attribute -->
                    <a class="dropdown-item" href="#" on:click={ logoutModal.show }>
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </a>
                </div>
            </li>
        { /if }

    </ul>

</nav>
<!-- End of Topbar -->



{ #if $appData }
    <Modal id="profile_modal" title="Profile" bind:api={ profileModal }>
        <Form id="change_name_form" bind:api={ changeNameForm } on:submit={ onChangeNameFormSubmit }>
            <fieldset class="fieldset">
                <legend>Name</legend>
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <input type="text" class="form-control input form-input" name="name" value="{ $appData.user.user.name }" data-required>

                        <button type="submit" class="btn btn-primary ml-3">Save</button>
                    </div>
                </div>
            </fieldset>
        </Form>

        <br>

        <Form id="change_email_form" bind:api={ changeEmailForm } on:submit={ onChangeEmailFormSubmit }>
            <fieldset class="fieldset">
                <legend>Email</legend>
                <div class="row">
                    <div class="col d-flex align-items-center">
                        <input type="text" class="form-control input form-input" name="email" value="{ $appData.user.user.email }" data-required>

                        <button type="submit" class="btn btn-primary ml-3">Save</button>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col">
                        <span class="color-warning">{ changeEmailFormMsg }</span>
                    </div>
                </div>
            </fieldset>
        </Form>

        <br>

        <Form id="change_birth_data_form" bind:api={ changeBirthDataForm } on:submit={ onChangeBirthDataFormSubmit }>
            <fieldset class="fieldset">
                <legend>Birth Data</legend>
                <div class="row">
                    <div class="col d-flex align-items-end" style="justify-content: space-between;">
                        <label class="m-0">
                            Name
                            <input type="text" class="form-control input form-input" name="birth.name" value="{ $appData.user.user.birth.name }">
                        </label>
                        
                        <label class="m-0 ml-3">
                            Surname
                            <input type="text" class="form-control input form-input" name="birth.surname" value="{ $appData.user.user.birth.surname }">
                        </label>

                        <button type="submit" class="btn btn-primary ml-3">Save</button>
                    </div>
                </div>
            </fieldset>
        </Form>

        <br>

        <div class="row">
            <div class="col text-center">
                <button class="btn btn-danger" on:click={ destroyUser }>Destroy</button>
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col text-center">
                <span class="color-warning">{ userDestroyMsg }</span>
            </div>
        </div>
    </Modal>

    <Modal id="security_modal" title="Security" bind:api={ securityModal }>
        <Form id="change_password_form" bind:api={ changePasswordForm } on:submit={ onChangePasswordFormSubmit }>
            <fieldset class="fieldset">
                <legend>Password</legend>
                <div class="row">
                    <div class="col d-flex align-items-start">
                        <PasswordField name="password" generable measurable required/>
    
                        <button type="submit" class="btn btn-primary ml-3">
                            Save
                        </button>
                    </div>
                </div>
            </fieldset>
        </Form>

        <br>

        <Form id="set_mfa_form">
            <fieldset class="fieldset">
                <legend class="d-flex">
                    <span class="mr-2">MFA (Multi-Factor Authentication)</span>

                    <Helper>
                        1) Login with your username and password
                        <br>
                        2) Confirm operation by email
                    </Helper>
                </legend>
                <div class="row">
                    <div class="col">
                        <Switch name="mfa" on:change={ onSetMfa } } bind:api={ mfaSwitch }>
                            Enabled
                        </Switch>
                    </div>
                </div>
            </fieldset>
        </Form>

        <br>

        <Form id="set_idk_form">
            <fieldset class="fieldset">
                <legend>
                    <span class="mr-2">IDK (Identity Key)</span>

                    <Helper>
                        <div style="position: relative;">
                            1) Import the IDK file
                            <br>
                            2) Login with IDK
                        </div>
                    </Helper>
                </legend>
                <div class="row">
                    <div class="col">
                        <Switch name="idk" on:change={ onSetIdk } } bind:api={ idkSwitch }>
                            Enabled
                        </Switch>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col text-right">
                        { #if $idk }
                            <button class="btn btn-danger ml-3" on:click={ ejectIDK } title="eject IDK">
                                <i class="fa-solid fa-eject"></i>
                            </button>
                        { :else }
                            <button class="btn btn-secondary" on:click={ importIDK } title="import IDK">
                                <i class="fa-solid fa-upload"></i>
                            </button>
                        { /if }
                    </div>
                </div>
            </fieldset>
        </Form>
    </Modal>

    <Modal id="logout_modal" title="Logout" width="480px" bind:api={ logoutModal }>
        <div class="row">
            <div class="col">
                Are you sure to terminate this session ?
            </div>
        </div>

        <div class="row mt-2">
            <div class="col text-right">
                <button class="btn btn-secondary" on:click={ logoutModal.hide }>Close</button>
                <!-- svelte-ignore a11y-click-events-have-key-events -->
                <botton class="btn btn-primary ml-3" on:click={ logout }>OK</botton>
            </div>
        </div>
    </Modal>



    <Modal id="alert_modal" title="Alert Info" bind:api={ alertModal }>
        <PropTable bind:data={ alertData }/>

        <div class="row mt-2">
            <div class="col text-center">
                <button class="btn btn-primary" on:click={ () => { markAlertAsRead( alertIndex ); } }>Mark as read</button>
            </div>
        </div>
    </Modal>
{ /if }

<style>

    .brand-box
    {
        width: 208px;
    }

    .app-logo
    {
        display: table;
        height: 50px;
        border-radius: 4px;
    }

    .navbar
    {
        /*margin-left: 224px;*/
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        background-color: var( --simba-dark );
    }

    .navbar .nav-link
    {
        color: #ffffff;
    }

</style>