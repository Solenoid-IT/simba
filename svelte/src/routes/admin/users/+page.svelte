<svelte:head>
    <title>{ title }</title>
</svelte:head>

<script>

    import App from '../../../views/App.svelte';
    import Base from '../../../views/components/Base.svelte';
    import Table from '../../../views/components/Table.svelte';
    import Modal from '../../../views/components/Modal.svelte';
    import Form from '../../../views/components/Form.svelte';

    import { envs } from '../../../envs.js';

    import { appReady } from '../../../stores/appReady.js';
    import { appData } from '../../../stores/appData.js';



    let resourceType = 'User';
    let resourceName = 'Users';



    let title = '';



    let hierarchies = {};



    let tableRecords = [];



    // Returns [Promise:bool]
    async function fetchData ()
    {
        // (Sending a request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::fetch_data',
                'Content-Type: application/json',

                'Route: ' + window.location.pathname
            ],
            '',
            '',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
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



        // (Getting the value)
        $appData = response.body;



        // (Getting the value)
        title = `${ resourceName } ( ${ $appData.records.length } )`;



        // (Getting the value)
        hierarchies = $appData.hierarchies;



        // (Setting the value)
        const records = [];

        for ( const record of $appData.records )
        {// Processing each entry
            // (Getting the value)
            const r =
            {
                'id':             record['id'],

                'values':
                [
                    {
                        'column':  'hierarchy',
                        'value':   hierarchies[ record['hierarchy'] ]['type'],

                        'content':
                            `
                                <span class="tag-label" style="background-color: ${ hierarchies[ record['hierarchy'] ]['color'] };">${ hierarchies[ record['hierarchy'] ]['type'] }</span>
                            `
                    },

                    {
                        'column': 'name',
                        'value':  record['name']
                    },

                    {
                        'column': 'email',
                        'value':  record['email']
                    },

                    {
                        'column': 'birth.name',
                        'value':  record['birth']['name']
                    },

                    {
                        'column': 'birth.surname',
                        'value':  record['birth']['surname']
                    },

                    {
                        'column': 'datetime.insert',
                        'value':  record['datetime']['insert']
                    },
                ],

                'controls':
                    $appData.user.user.hierarchy === 1
                        ?
                    `
                        <button class="btn btn-sm btn-danger" value="user::remove" title="remove">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `
                        :
                    ''
                ,

                'hidden': false
            }
            ;



            // (Appending the value)
            records.push( r );
        }



        // (Getting the value)
        tableRecords = records;



        // Returning the value
        return true;
    }

    $:
        if ( $appReady )
        {// Value is true
            // (Fetching the data)
            fetchData();
        }



    // Returns [Promise:bool]
    async function removeUser (ids)
    {
        if ( !confirm('Are you sure to remove the selected users ?') ) return;



        // (Sending a request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::remove',
                'Content-Type: application/json'
            ],
            JSON.stringify( ids ),
            '',
            true
        )
        ;

        if ( response.status.code !== 200 )
        {// (Request failed)
            if ( response.status.code === 401 )
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



        // (Alerting the value)
        alert(`Confirm operation by email (for each user) ...`);



        // Returning the value
        return true;
    }



    let table;

    // Returns [Promise:void]
    async function onTableRecordAction (event)
    {
        // (Setting the value)
        let result = false;



        // (Getting the value)
        const entry = event.detail;

        switch ( entry.action )
        {
            case 'user::remove':
                // (Removing the user)
                result = await removeUser( [ entry.id ] );

                if ( result )
                {// (User has been removed)
                    // (Removing the element)
                    jQuery(entry.element).find('.controls .btn[value="' + entry.action + '"]').remove();
                }
            break;
        }



        // Returning the value
        return result;
    }

    // Returns [void]
    function onTableSelectionChange (event)
    {
        // (Getting the value)
        const ids = table.fetchSelectedRecords();

        // (Consoling the value)
        console.debug(ids);// ahcid
    }

    // Returns [Promise:bool]
    async function onBulkRemove ()
    {
        // (Setting the value)
        const ids = [];

        for ( const id of table.fetchSelectedRecords() )
        {// Processing each entry
            // (Appending the value)
            ids.push( tableRecords[id].id );
        }



        // (Removing the users)
        const result = await removeUser( ids );



        // Returning the value
        return result;
    }



    let addUserModal;
    


    let addUserForm;

    let addUserFormMsg = '';

    // Returns [Promise:bool]
    async function onAddUserFormSubmit ()
    {
        // (Validating the form)
        const result = addUserForm.validate();

        if ( !result.valid ) return false;



        // (Setting the value)
        addUserFormMsg = '';



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::add',
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



        // (Setting the value)
        addUserForm.disabled = true;

        // (Setting the value)
        addUserFormMsg = `Confirm operation by email '${ result.entries['email'].value }' ...`;



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
            alert( res.body['error']['message'] );



            // Returning the value
            return false;
        }



        // (Fetching the data)
        await fetchData();



        // (Setting the value)
        addUserForm.disabled = false;

        // (Setting the value)
        addUserFormMsg = '';



        // Returning the value
        return true;
    }

</script>

<App>
    <Base>
        <Table title={ title } controls bind:api={ table } bind:records={ tableRecords } on:record.action={ onTableRecordAction } selectable on:selection.change={ onTableSelectionChange }>
            <div slot="fixed-controls">
                { #if $appData.user.user.hierarchy === 1 }
                    <button class="btn btn-primary btn-sm" title="add" on:click={ addUserModal.show }>
                        <i class="fa-solid fa-plus"></i>
                    </button>
                { /if }
            </div>
            <div slot="selection-controls">
                { #if $appData.user.user.hierarchy === 1 }
                    <button class="btn btn-danger btn-sm" on:click={ onBulkRemove } title="remove">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                { /if }
            </div>
        </Table>

        <Modal title="{ resourceType }" bind:api={ addUserModal }>
            <Form bind:api={ addUserForm } on:submit={ onAddUserFormSubmit }>
                <div class="row">
                    <div class="col">
                        <label class="d-block m-0">
                            Name
                            <input type="text" class="form-control input form-input" name="name" data-required>
                        </label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col">
                        <label class="d-block m-0">
                            Email
                            <input type="text" class="form-control input form-input" name="email" data-required>
                        </label>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col">
                        <label class="d-block m-0">
                            Hierarchy
                            <select class="form-control input form-input" name="hierarchy" data-required>
                                <option value=""></option>
                                <option disabled></option>

                                { #each Object.values( hierarchies ) as hierarchy }
                                    <option value="{ hierarchy.id }">{ hierarchy.type }</option>
                                { /each }
                            </select>
                        </label>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col text-center">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col text-center">
                        <span class="color-warning">{ addUserFormMsg }</span>
                    </div>
                </div>
            </Form>
        </Modal>
    </Base>
</App>