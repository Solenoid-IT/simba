<svelte:head>
    <title>{ title }</title>
</svelte:head>

<script>

    import App from '../../../views/App.svelte';
    import Base from '../../../views/components/Base.svelte';
    import Table from '../../../views/components/Table.svelte';

    import { envs } from '../../../envs.js';
    import { appReady } from '../../../stores/appReady.js';
    import { appData } from '../../../stores/appData.js';



    let title = '';



    let table;
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
        title = `Activity Log ( ${ $appData.records.length } )`;



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
                        'column': 'id',
                        'value':  record['id']
                    },

                    {
                        'column': 'action',
                        'value':  record['action']
                    },

                    {
                        'column': 'description',
                        'value':  record['description']
                    },

                    {
                        'column': 'ip',
                        'value':  [ record['ip'], record['ip_info']['country']['code'], record['ip_info']['isp'] ].join(' - ')
                    },

                    {
                        'column': 'browser',
                        'value':  record['ua_info']['browser']
                    },

                    {
                        'column': 'os',
                        'value':  record['ua_info']['os']
                    },

                    {
                        'column': 'hw',
                        'value':  record['ua_info']['hw']
                    },

                    {
                        'column': 'resource.action',
                        'value':  record['resource']['action']
                    },

                    {
                        'column': 'resource.type',
                        'value':  record['resource']['type']
                    },

                    {
                        'column': 'resource.key',
                        'value':  record['resource']['key']
                    },

                    {
                        'column': 'datetime.insert',
                        'value':  record['datetime']['insert']
                    },
                ],

                'controls':
                    `
                        <button class="btn btn-sm btn-danger" value="user::terminate_session" title="terminate session" ${ record['session'] && !record['current_session'] ? '' : 'disabled' }>
                            <i class="fa-solid fa-right-from-bracket"></i>
                        </button>
                    `
                ,

                'hidden': false
            }
            ;



            if ( $appData.user.user.hierarchy === 1 )
            {// (User is an admin)
                // (Inserting an element)
                r.values.splice
                (
                    1,
                    0,
                    {
                        'column': 'user',
                        'value':  record['user']['name']
                    },
                )
                ;
            }



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
    async function terminateSession (ids)
    {
        if ( !confirm('Are you sure to terminate the selected sessions ?') ) return false;



        // (Sending a request)
        const response = await Solenoid.HTTP.sendRequest
        (
            envs.APP_URL + '/api',
            'RPC',
            [
                'Action: user::terminate_session',
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



        // Returning the value
        return true;
    }



    // Returns [Promise:void]
    async function onTableRecordAction (event)
    {
        // (Setting the value)
        let result = false;



        // (Getting the value)
        const entry = event.detail;

        switch ( entry.action )
        {
            case 'user::terminate_session':
                // (Terminating the session)
                result = await terminateSession( [ entry.id ] );

                if ( result )
                {// Value is true
                    // (Setting the property)
                    jQuery(entry.element).find('.controls .btn[value="' + entry.action + '"]').prop( 'disabled', true );
                }
            break;
        }



        // Returning the value
        return result;
    }

</script>

<App>
    <Base>
        <Table title={ title } controls bind:api={ table } bind:records={ tableRecords } on:record.action={ onTableRecordAction } selectable>
            <div slot="selection-controls">
                <button class="btn btn-sm btn-danger" title="terminate session" on:click={ () => { terminateSession( table.fetchSelectedRecords().map( function (index) { return tableRecords[index].id; } ) ); } }>
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </div>
        </Table>
    </Base>
</App>