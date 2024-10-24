<script>

    import { browser } from '$app/environment';

    import { createEventDispatcher, onMount } from 'svelte';

    import Table from '../components/Table.svelte';



    const dispatch = createEventDispatcher();



    export let title    = '';

    export let input    = null;
    export let required = null;



    let element;



    export let api = {};



    export let availableRecords = [];
    export let selectedRecords  = [];



    /*

    // debug
    availableRecords =
    [
        {
            'id': 1,

            'values':
            [
                {
                    'column': 'name',
                    'value':  'iOS'
                }
            ],

            'controls': ''
        },
        {
            'id': 2,

            'values':
            [
                {
                    'column': 'name',
                    'value':  'Android'
                }
            ],

            'controls': ''
        },
    ]
    ;

    */



    let availableTable;
    export let selectedTable;

    let availableSelection = [];
    let selectedSelection  = [];

    // Returns [void]
    function onAvailableSelectionChange (event)
    {
        // (Getting the value)
        availableSelection = availableTable.fetchSelectedRecords();
    }

    // Returns [void]
    function onSelectedSelectionChange (event)
    {
        // (Getting the value)
        selectedSelection = selectedTable.fetchSelectedRecords();
    }



    // Returns [void]
    function selectEntries ()
    {
        // (Getting the value)
        availableSelection = availableTable.fetchSelectedRecords();

        for ( const i of availableSelection )
        {// Iterating each index
            // (Getting the value)
            const record = JSON.parse( JSON.stringify( availableRecords[i] ) );

            if ( selectedRecords.filter( function (r) { return r.id === record.id; } ).length > 0 ) continue;



            // (Setting the property)
            record.hidden = false;



            // (Appending the value)
            selectedRecords.push( record );
        }



        // (Getting the value)
        selectedRecords = selectedRecords;



        // (Setting the value)
        availableSelection = [];

        // (Deselecting the records)
        availableTable.deselectRecords();
    }

    // Returns [void]
    function deselectEntries ()
    {
        // (Getting the value)
        selectedSelection = selectedTable.fetchSelectedRecords();



        // (Setting the value)
        const results = [];

        for ( let i = 0; i < selectedRecords.length; i++ )
        {// Iterating each index
            if ( selectedSelection.includes( i ) ) continue;

            // (Appending the value)
            results.push( selectedRecords[i] );
        }



        // (Setting the value)
        selectedSelection = [];

        // (Getting the value)
        selectedRecords = results;
    }



    // Returns [void]
    function onSelectedRecordAction (event)
    {
        // (Triggering the event)
        dispatch( 'record.action', event.detail );
    }



    // (Listening for the event)
    onMount
    (
        function ()
        {
            /*
            
            // (Setting the value)
            element.api =
            {
                'selectIds': function (ids)
                {
                    // ahcid to implementt
                }
            }
            ;

            */
        }
    )
    ;

</script>

<div class="row">
    <div class="col">
        <Table title="Available" bind:api={ availableTable } bind:records={ availableRecords } selectable on:selection.change={ onAvailableSelectionChange }/>
    </div>
    <div class="col d-flex justify-content-center align-items-center flex-grow-0" style="flex-direction: column;">
        <button type="button" class="btn" title="select" class:btn-secondary={ availableSelection.length === 0 } class:btn-primary={ availableSelection.length > 0 } on:click={ selectEntries }>
            <i class="fa-solid fa-arrow-right"></i>
        </button>
        <button type="button" class="btn mt-2" title="deselect" class:btn-secondary={ selectedSelection.length === 0 } class:btn-primary={ selectedSelection.length > 0 } on:click={ deselectEntries }>
            <i class="fa-solid fa-arrow-left"></i>
        </button>
    </div>
    <div class="col">
        <Table title="Selected" input={ input } required={ required } controls bind:api={ selectedTable } bind:records={ selectedRecords } selectable on:selection.change={ onSelectedSelectionChange } on:record.action={ onSelectedRecordAction }/>
    </div>
</div>

<style>

    .jtable
    {
        font-size: 12px;
        font-weight: 700;
    }

    .table tbody tr:nth-child(even)
    {
        background-color: #e1e1e1;
    }

    .table tbody tr:hover
    {
        background-color: #d4d4d4;
    }

    .table tbody tr td
    {
        vertical-align: middle;
    }

    .table .column-header
    {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    .controls-left
    {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .search-box
    {
        margin: 10px 4px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-box .input
    {
        flex-grow: 1;
    }

    .column-key-search-box
    {
        position: relative;
    }

    .column-key-search-btn[data-state="idle"]
    {
        color: #ffffff;
        background-color: #858796;
        border-radius: 2px;
        cursor: pointer;
    }

    .column-key-search-btn[data-state="active"]
    {
        color: #ffffff;
        background-color: var( --simba-primary );
        border-radius: 2px;
        cursor: pointer;
    }

    .column-key-search-menu
    {
        max-width: 400px;
    }

    .column-key-search-menu[data-state="open"]
    {
        width: 100%;
        display: table;
        position: fixed;
        background-color: #ffffff;
        border-radius: 2px;
    }

    .column-key-search-menu[data-state="closed"]
    {
        display: none;
    }

    .column-key-search-menu ul
    {
        margin: 0;
        padding: 10px;
        list-style: none;
    }

    .column-key-search-menu .row .col
    {
        padding: 18px;
    }

    .active-filter-indicator
    {
        position: relative;
    }

    .active-filter-indicator span
    {
        margin-left: -4px;
        margin-bottom: -4px;
        position: absolute;
        left: 0;
        bottom: 0;
        font-size: 10px;
    }

</style>