<script>

    import { browser } from '$app/environment';

    import { onMount } from 'svelte';

    import { createEventDispatcher } from 'svelte';



    const dispatch = createEventDispatcher();



    export let title    = '';

    export let input    = null;
    export let required = null;

    export let controls = false;



    onMount
    (
        function ()
        {
            // (Listening for the event)
            jQuery(element).delegate('.action-input[data-action]', 'click', function (event) {
                // (Getting the value)
                const rowElement = this.closest('tr');

                // (Triggering the event)
                dispatch
                (
                    'record.action',
                    {
                        'action':     this.getAttribute('data-action'),
                        'id':         rowElement.getAttribute('data-id'),

                        'element':    this,
                        'rowElement': rowElement
                    }
                )
                ;
            });

            // (Listening for the event)
            jQuery(element).delegate('.selection-controls-box .btn', 'click', function (event) {
                // (Setting the property)
                jQuery(element).find('.table .selection .table-input[value="all"]').prop( 'checked', false );



                // (Iterating each entry)
                element.querySelectorAll('.table tbody .selectable .table-input').forEach
                (
                    function (el)
                    {
                        // (Setting the property)
                        el.checked = false;
                    }
                )
                ;



                // (Getting the value)
                api.numSelectedRecords = api.fetchSelectedRecords().length;
            });



            // (Getting the value)
            element.api =
            {
                'transformRecord': null,

                'listRecords': function ()
                {
                    // Returning the value
                    return element.api.transformRecord ? records.map( element.api.transformRecord ) : records;
                },

                

                'listIds': function ()
                {
                    // Returning the value
                    return records.map( function (record) { return record.id; } );
                },



                'emptyRecords': function ()
                {
                    // (Setting the value)
                    records = [];
                },

                'setRecords': function (value)
                {
                    // (Getting the value)
                    records = value;
                }
            }
            ;



            if ( input )
            {// Value found
                // (Setting the class name)
                element.classList.add('form-input');
            }



            // (Getting the value)
            api.element = element;
        }
    )
    ;



    let element;



    let sortColumn  = null;
    let sortReverse = false;



    export let records = [];

    export let selectable = false;



    export let api = {};



    // (Setting the values)
    api.useKeys            = false;
    api.keys               = {};

    api.filterEnabled      = false;
    api.activeFilter       = null;
    api.lastFilter         = null;
    api.numSelectedRecords = 0;



    // Returns [number|false]
    api.getColumnIndex = function (column)
    {
        // (Setting the value)
        let index = false;

        for ( const i in records[0].values )
        {// Iterating each index
            if ( records[0].values[i].column === column )
            {// Match OK
                // (Getting the value)
                index = i;

                // Breaking the iteration
                break;
            }
        }



        // Returning the value
        return index;
    }

    // Returns [Array<string>]
    api.getColumnValues = function (column, filtered)
    {
        if ( typeof filtered === 'undefined' ) filtered = false;



        // Returning the value
        return [ ...new Set( records.filter( function (record) { return filtered ? !record.hidden : true; } ).map( function (record) { return record.values[ api.getColumnIndex(column) ].value; } ) ) ];
    }



    // Returns [void]
    api.sort = function (column, reverse)
    {
        if ( typeof reverse === 'undefined' ) reverse = sortReverse;



        // (Getting the value)
        const index = api.getColumnIndex(column);



        // (Sorting the array)
        records.sort
        (
            function (a, b)
            {
                if ( a.values[index].value < b.values[index].value )
                {// (A is lesser than B)
                    // Returning the value
                    return reverse ? 1 : -1;
                }
                else
                if ( a.values[index].value > b.values[index].value )
                {// (A is greater than B)
                    // Returning the value
                    return reverse ? -1 : 1;
                }



                // Returning the value
                return 0;
            }
        )
        ;



        // (Getting the value)
        records = records;



        // (Setting the value)
        sortColumn = column;

        // (Getting the value)
        sortReverse = !sortReverse;
    }



    // Returns [object]
    api.getSearchValues = function ()
    {
        // (Getting the value)
        const result =
        {
            'global': element.querySelector('.search-box .table-input').value,
            'local': {},
            'keys':  {}
        }
        ;



        // (Iterating each entry)
        element.querySelectorAll('.jtable thead tr th[data-column]').forEach
        (
            function (el)
            {
                // (Getting the values)
                result.local[ el.getAttribute('data-column') ] = el.querySelector('.table-input').value;
                result.keys[ el.getAttribute('data-column') ]  = api.useKeys ? api.keys[ el.getAttribute('data-column') ].entries.filter( function (entry) { return entry.checked && !entry.hidden; } ).map( function (entry) { return entry.value; } ) : [];
            }
        )
        ;



        // Returning the value
        return result;
    }

    // Returns [void]
    api.setSearchValues = function (searchValues)
    {
        // (Getting the value)
        element.querySelector('.search-box .table-input').value = searchValues.global;



        // (Iterating each entry)
        element.querySelectorAll('.jtable thead tr th[data-column]').forEach
        (
            function (el)
            {
                // (Getting the value)
                const column = el.getAttribute('data-column');



                // (Getting the value)
                el.querySelector('.table-input').value = searchValues.local[ column ];

                if ( api.useKeys )
                {// Value is true
                    for ( const i in api.keys[ column ].entries )
                    {// Processing each entry
                        if ( searchValues.keys[ column ].includes( api.keys[ column ].entries[i].value ) )
                        {// Match OK
                            // (Setting the properties)
                            api.keys[ column ].entries[i].checked = true;
                            api.keys[ column ].entries[i].hidden  = false;
                        }
                    }
                }
            }
        )
        ;
    }



    // Returns [void]
    api.filter = function (fn)
    {
        if ( selectable )
        {// Value is true
            // (Deselecting the records)
            api.deselectRecords();
        }



        switch ( fn )
        {
            case 'SEARCH_GLOBAL':// (OR of values)
                // (Getting the value)
                const searchValue = api.getSearchValues().global;

                for ( const i in records )
                {// Iterating each index
                    // (Setting the value)
                    let resultFound = false;

                    for ( const column of records[i].values )
                    {// Processing each entry
                        // (Getting the value)
                        const value = column.value ?? '';

                        if ( value.toString().toLowerCase().indexOf( searchValue.toLowerCase() ) !== -1 )
                        {// Match OK
                            // (Setting the value)
                            resultFound = true;

                            // Breaking the iteration
                            break;
                        }
                    }



                    // (Getting the value)
                    records[i].hidden = !resultFound;
                }
            break;

            case 'SEARCH_LOCAL':// (AND of values)
                // (Getting the value)
                const values = api.getSearchValues().local;

                for ( const i in records )
                {// Processing each entry
                    // (Setting the value)
                    let resultFound = true;

                    for ( const k in values )
                    {// Processing each entry
                        // (Getting the value)
                        const value = records[i].values[ api.getColumnIndex(k) ].value ?? '';

                        if ( value.toString().toLowerCase().indexOf( values[k].toString().toLowerCase() ) === -1 )
                        {// Match failed
                            // (Setting the value)
                            resultFound = false;

                            // Breaking the iteration
                            break;
                        }
                    }



                    // (Getting the value)
                    records[i].hidden = !resultFound;
                }
            break;

            case 'SEARCH_KEYS':// (OR of keys)
                // (Getting the value)
                const keys = api.getSearchValues().keys;

                for ( const i in records )
                {// Processing each entry
                    // (Setting the value)
                    let resultFound = true;

                    for ( const k in records[i].values )
                    {// Processing each entry
                        // (Getting the values)
                        const column = records[i].values[k].column;
                        const value  = records[i].values[k].value ?? '';

                        if ( keys[column].length > 0 && !keys[column].includes( value.toString() ) )
                        {// Match failed
                            // (Setting the value)
                            resultFound = false;

                            // Breaking the iteration
                            break;
                        }
                    }



                    // (Getting the value)
                    records[i].hidden = !resultFound;
                }
            break;

            default:
                // (Calling the function)
                fn();
        }
    }



    // Returns [object]
    api.extractKeys = function ()
    {
        // (Setting the value)
        const keys = {};



        if ( records.length === 0 ) return;



        for ( const i in records[0].values )
        {// Processing each entry
            // (Getting the value)
            const column = records[0].values[i].column;

            // (Getting the value)
            keys[ column ] =
            {
                'menuOpen':     false,
                'filterActive': false,

                'entries':      api.getColumnValues( column ).map( function (entry) { return { 'value': entry, 'checked': false, 'hidden': false }; } )
            }
            ;
        }



        // Returning the value
        return keys;
    }



    // Returns [void]
    api.resetFilter = function ()
    {
        for ( const i in records )
        {// Processing each entry
            // (Setting the value)
            records[i].hidden = false;
        }

        for ( const column in api.keys )
        {// Processing each entry
            for ( const i in api.keys[column].entries )
            {// Processing each entry
                // (Setting the values)
                api.keys[column].entries[i].checked = false;
                api.keys[column].entries[i].hidden  = false;
            }
        }



        // (Setting the value)
        element.querySelector('.search-box .table-input').value = '';

        // (Iterating each entry)
        element.querySelectorAll('.column-search-box .table-input').forEach
        (
            function (el)
            {
                // (Setting the value)
                el.value = '';
            }
        )
        ;

        // (Iterating each entry)
        element.querySelectorAll('.column-key-search-box .key-list .table-input[value="all"]').forEach
        (
            function (el)
            {
                // (Setting the value)
                el.checked = false;
            }
        )
        ;
    }

    // Returns [void]
    api.applyFilter = function (filter)
    {
        // (Setting the search values)
        api.setSearchValues( filter );
    }



    // Returns [void]
    api.saveFilter = function ()
    {
        // (Getting the value)
        api.lastFilter = api.getSearchValues();

        // (Resetting the filter)
        api.resetFilter();



        // (Getting the value)
        api.filterEnabled = !api.filterEnabled;
    }

    // Returns [void]
    api.restoreFilter = function ()
    {
        if ( !api.lastFilter ) return;



        // (Applying the filter)
        api.applyFilter( api.lastFilter );

        // (Filtering the table)
        api.filter( api.activeFilter );



        // (Getting the value)
        api.filterEnabled = !api.filterEnabled;
    }



    // Returns [string]
    api.buildCSV = function (columns, columnSeparator, rowSeparator, enclosure, escape)
    {
        if ( records.length === 0 ) return '';



        if ( typeof columns === 'undefined' ) columns = records[0].values.map( function (entry) { return entry.column; } );
        if ( typeof columnSeparator === 'undefined' ) columnSeparator = ';'
        if ( typeof rowSeparator === 'undefined' ) rowSeparator = "\n";
        if ( typeof enclosure === 'undefined' ) enclosure = '"';
        if ( typeof escape === 'undefined' ) escape = '\\';



        // (Setting the value)
        let lines = [];



        // (Setting the value)
        let schema = [];

        for ( const column of columns )
        {// Processing each entry
            // (Appending the value)
            schema.push( /\s/.test( column ) ? `${ enclosure }${ column.replace( new RegExp( enclosure ), escape + enclosure ) }${ enclosure }` : column );
        }

        // (Getting the value)
        schema = schema.join( columnSeparator );




        // (Appending the value)
        lines.push( schema );



        for ( const i in records )
        {// Processing each entry
            if ( records[i].hidden ) continue;



            let cols = [];

            for ( const column of columns )
            {// Processing each entry
                for ( const j in records[i].values )
                {// Processing each entry
                    if ( records[i].values[j].column === column )
                    {// Match OK
                        // (Getting the value)
                        const value = records[i].values[j].value;

                        // (Appending the value)
                        cols.push( /\s/.test( value ) ? `${ enclosure }${ value.replace( new RegExp( enclosure ), escape + enclosure ) }${ enclosure }` : value );

                        // Breaking the iteration
                        break;
                    }
                }
            }



            // (Appending the value)
            //lines.push( records[i].values.map( function (entry) { return /\s/.test( entry.value ) ? `${ enclosure }${ entry.value.replace( new RegExp( enclosure ), escape + enclosure ) }${ enclosure }` : entry.value; } ).join( columnSeparator ) );
            lines.push( cols.join( columnSeparator ) );
        }



        // Returning the value
        return lines.join( rowSeparator );
    }

    // Returns [void]
    api.downloadCSV_OLD = function (filename)
    {
        if ( typeof filename === 'undefined' ) filename = 'export.csv';



        // (Getting the values)
        const blob = new Blob( [ api.buildCSV() ], { 'type': 'application/csv' } );
        const url  = window.URL.createObjectURL( blob );



        // (Creating an element)
        const a = document.createElement('a');

        // (Getting the values)
        a.href     = url;
        a.download = filename;



        // (Appending the child)
        document.body.appendChild(a);



        // (Triggering the event)
        a.click();

        // (Removing the element)
        a.remove();



        // (Revoking the url object)
        window.URL.revokeObjectURL(url);
    }

    // Returns [void]
    api.downloadCSV = function (filename)
    {
        if ( typeof filename === 'undefined' ) filename = 'export.csv';



        // (Creating an element)
        const a = document.createElement('a');

        // (Getting the values)
        a.href     = `data:application/csv;base64,${ btoa( api.buildCSV() ) }`;
        a.download = filename;



        // (Triggering the event)
        a.click();

        // (Removing the element)
        a.remove();
    }



    // Returns [Array<number>]
    api.fetchSelectedRecords = function ()
    {
        // (Setting the value)
        let recordsIds = [];

        // (Iterating each entry)
        element.querySelectorAll('.table tbody .selectable .table-input').forEach
        (
            function (el)
            {
                if ( el.checked )
                {// Value is true
                    // (Appending the value)
                    recordsIds.push( parseInt( el.closest('tr').getAttribute('data-index') ) );
                }
            }
        )
        ;



        // Returning the value
        return recordsIds;
    }

    // Returns [void]
    api.deselectRecords = function ()
    {
        // (Setting the property)
        element.querySelector('.table .selection .table-input[value="all"]').checked = false;

        // (Iterating each entry)
        element.querySelectorAll('.table .selectable .table-input[value="select"]').forEach
        (
            function (el)
            {
                // (Setting the property)
                el.checked = false;
            }
        )
        ;



        // (Setting the value)
        api.numSelectedRecords = 0;
    }



    // Returns [void]
    function onGlobalSearch (event)
    {
        // (Setting the value)
        api.activeFilter = 'SEARCH_GLOBAL';

        // (Getting the value)
        api.filterEnabled = event.target.value.length > 0;



        // (Filtering the records)
        api.filter( api.activeFilter );
    }

    // Returns [void]
    function onLocalSearch (event)
    {
        // (Setting the value)
        api.activeFilter = 'SEARCH_LOCAL';

        // (Getting the value)
        api.filterEnabled = Object.values( api.getSearchValues().local ).filter( function (value) { return value.length > 0; } ).length > 0;



        // (Filtering the records)
        api.filter( api.activeFilter );
    }



    /*

    // Returns [void]
    function onDataChange (rr)
    {
        // (Triggering the event)
        dispatch('datachange');
    }



    $:
        // (Calling the function)
        onDataChange(records);

    */
    
    
    
    // Returns [void]
    function onKeySearchMenuBtnClick (column)
    {
        // (Getting the value)
        api.keys[ column ].menuOpen = !api.keys[ column ].menuOpen;
    }



    // Returns [void]
    function onKeySelect (event, column, value)
    {
        for ( const i in api.keys[ column ].entries )
        {// Processing each entry
            if ( api.keys[ column ].entries[i].value === value )
            {// Value found
                // (Getting the value)
                api.keys[ column ].entries[i].checked = event.target.checked;
            }
        }



        // (Setting the value)
        let filterEnabled = false;

        loop: for ( const column in api.keys )
        {// Processing each entry
            for ( const i in api.keys[ column ].entries )
            {// Processing each entry
                if ( api.keys[ column ].entries[i].checked )
                {// Value is true
                    // (Setting the value)
                    filterEnabled = true;

                    // Breaking the iteration
                    break loop;
                }
            }
        }



        // (Getting the value)
        //api.keys[ column ].filterActive = api.keys[ column ].entries.filter( function (entry) { return entry.checked; } ).length > 0;



        // (Setting the value)
        api.activeFilter = 'SEARCH_KEYS';

        // (Getting the value)
        api.filterEnabled = filterEnabled;



        // (Filtering the records)
        api.filter( api.activeFilter );
    }

    // Returns [void]
    function onKeySelectAll (event, column)
    {
        for ( const i in api.keys[ column ].entries )
        {// Processing each entry
            // (Getting the value)
            api.keys[ column ].entries[i].checked = event.target.checked;
        }



        // (Setting the value)
        let filterEnabled = false;

        loop: for ( const column in api.keys )
        {// Processing each entry
            for ( const i in api.keys[ column ].entries )
            {// Processing each entry
                if ( api.keys[ column ].entries[i].checked )
                {// Value is true
                    // (Setting the value)
                    filterEnabled = true;

                    // Breaking the iteration
                    break loop;
                }
            }
        }



        // (Getting the value)
        //api.keys[ column ].filterActive = api.keys[ column ].entries.filter( function (entry) { return entry.checked; } ).length > 0;



        // (Setting the value)
        api.activeFilter = 'SEARCH_KEYS';

        // (Getting the value)
        api.filterEnabled = filterEnabled;



        // (Filtering the records)
        api.filter( api.activeFilter );
    }



    // Returns [void]
    function extractKeys ()
    {
        // (Getting the value)
        api.useKeys = !api.useKeys;

        if ( api.useKeys )
        {// Value is true
            // (Getting the value)
            api.keys = api.extractKeys();
        }
        else
        {// Value is false
            // (Setting the value)
            api.keys = {};
        }
    }



    // Returns [void]
    function onKeySearch (event, column)
    {
        // (Getting the value)
        const searchValue = event.target.value;

        for ( const i in api.keys[ column ].entries )
        {// Processing each entry
            // (Getting the value)
            api.keys[ column ].entries[i].hidden = api.keys[ column ].entries[i].value.toLowerCase().indexOf( searchValue.toLowerCase() ) === -1;
        }



        if ( api.keys[ column ].entries.filter( function (entry) { return entry.checked && !entry.hidden; } ).length > 0 )
        {// (All of the keys are checked)
            // (Filtering the records)
            api.filter('SEARCH_KEYS');
        }
    }



    /*
    
    $:
        if ( records.length - records.filter( function (record) { return record.hidden; } ).length > 0 )
        {// (There are hidden records)
            // (Setting the value)
            api.filterEnabled = true;
        }
        else
        {// (There are no hidden records)
            // (Setting the value)
            api.filterEnabled = false;
        }

    */



    // Returns [void]
    function toggleSelectAllRecords (event)
    {
        // (Getting the value)
        const checked = event.target.checked;

        // (Iterating each entry)
        element.querySelectorAll('.table tbody .selectable .table-input').forEach
        (
            function (el)
            {
                // (Setting the property)
                el.checked = checked;
            }
        )
        ;



        // (Getting the value)
        api.numSelectedRecords = api.fetchSelectedRecords().length;



        // (Triggering the event)
        dispatch( 'selection.change' );
    }

    // Returns [void]
    function onSelectionChange (event)
    {
        // (Getting the value)
        api.numSelectedRecords = api.fetchSelectedRecords().length;

        // (Triggering the event)
        dispatch
        (
            'selection.change',
            {
                'element': event.target.closest('tr')
            }
        )
        ;
    }

</script>

<div class="card shadow mb-4 jtable component table-component" data-input={ input } data-required={ required } bind:this={ element }>
    <div class="card-header py-3 d-flex align-items-center" style="justify-content: space-between;">
        <h6 class="m-0 font-weight-bold text-primary">{ title }</h6>

        <slot name="fixed-controls"/>
    </div>

    <div class="card-body">
        { #if records.length > 0 }
            <div class="table-responsive">
                <div class="dataTables_wrapper dt-bootstrap4">
                    <div class="row">
                        <div class="col d-flex align-items-center" style="justify-content: space-between;">
                            <div class="controls-left">
                                <div class="num-results">( <b>{ records.filter( function (record) { return !record.hidden; } ).length }</b> )</div>

                                <button type="button" class="btn btn-secondary btn-sm ml-3" title="download csv" on:click={ () => { api.downloadCSV(); } }>
                                    <i class="fa-solid fa-download"></i>
                                </button>

                                <div class="selection-controls-box d-flex align-items-center ml-5">
                                    { #if element && api.numSelectedRecords > 0 }
                                            <span class="num-results mr-3" style="color: var( --simba-primary );">( <b>{ api.numSelectedRecords }</b> )</span>
                                            <slot name="selection-controls"/>
                                    { /if }
                                </div>
                            </div>

                            <div class="search-box">
                                <button type="button" class="btn btn-secondary btn-sm mr-3" title="extract keys" on:click={ extractKeys }>
                                    { #if api.useKeys }
                                        <i class="fa-solid fa-caret-up"></i>
                                    { :else }
                                        <i class="fa-solid fa-caret-down"></i>
                                    { /if }
                                </button>

                                { #if api.filterEnabled }
                                    <button type="button" class="btn btn-danger btn-sm" title="remove filter { api.activeFilter }" on:click={ api.saveFilter }>
                                        <i class="fa-solid fa-filter-circle-xmark"></i>

                                        <div class="active-filter-indicator">
                                            <span>{ api.activeFilter?.split('_')[1].charAt(0).toUpperCase() }</span>
                                        </div>
                                    </button>
                                { :else }
                                    <button type="button" class="btn btn-secondary btn-sm" title="apply filter { api.activeFilter }" on:click={ api.restoreFilter }>
                                        <i class="fa-solid fa-filter"></i>
                                    </button>
                                { /if }

                                <input type="text" class="form-control form-control-sm input table-input ml-3" placeholder="Search ..." style="width: 250px;" on:input={ onGlobalSearch }>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered dataTable" width="100%" cellspacing="0" role="grid" style="width: 100%;">
                                <thead>
                                    <tr>
                                        { #if selectable }
                                            <th class="selection text-center">
                                                <input type="checkbox" class="input table-input" value="all" on:change={ toggleSelectAllRecords }>
                                            </th>
                                        { /if }

                                        { #each records[0].values.map( function (record) { return record.column; } ) as column }
                                            <th data-column={ column }>
                                                <!-- svelte-ignore a11y-click-events-have-key-events -->
                                                <div class="column-header" on:click={ () => { api.sort(column); } }>
                                                    <div class="column-name">{ column }</div>
                                                    <div class="column-controls d-flex" style="flex-direction: column; font-size: 12px;">
                                                        { #if sortColumn === column }
                                                            { #if sortReverse }
                                                                <i class="fa-solid fa-caret-up"></i>
                                                            { :else }
                                                                <i class="fa-solid fa-caret-down"></i>
                                                            { /if }
                                                        { /if }
                                                    </div>
                                                </div>

                                                <div class="column-search-box">
                                                    <input type="text" class="form-control form-control-sm input table-input" on:input={ onLocalSearch }>
                                                </div>

                                                { #if api.useKeys }
                                                    <div class="column-key-search-box mt-2">
                                                        <!-- svelte-ignore a11y-click-events-have-key-events -->
                                                        <div class="column-key-search-btn d-flex justify-content-center align-items-center" on:click={ onKeySearchMenuBtnClick(column) } data-state="{ api.keys[ column ].entries.filter( function (entry) { return entry.checked; } ).length > 0 ? 'active' : 'idle' }">
                                                            { #if Object.keys( api.keys ).length > 0 }
                                                                { #if api.keys[ column ].menuOpen }
                                                                    <i class="fa-solid fa-caret-up"></i>
                                                                { :else }
                                                                    <i class="fa-solid fa-caret-down"></i>
                                                                { /if }
                                                            { :else }
                                                                <i class="fa-solid fa-caret-down"></i>
                                                            { /if }
                                                        </div>

                                                        { #if Object.keys( api.keys ).length > 0 }
                                                            <div class="column-key-search-menu" data-state={ api.keys[ column ].menuOpen ? 'open' : 'closed' }>
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <input type="text" class="form-control form-control-sm input table-input" name="search" on:input={ onKeySearch( event, column ) }>
                                                                    </div>
                                                                </div>

                                                                <ul class="key-list">
                                                                    <li>
                                                                        <label class="m-0 d-block">
                                                                            <input type="checkbox" class="input table-input table-input-all mb-3" value="all" on:change={ onKeySelectAll( event, column ) }> ALL [ { api.keys[ column ].entries.filter( function (entry) { return !entry.hidden; } ).length } ]
                                                                        </label>
                                                                    </li>

                                                                    <!--{ #each api.getColumnValues( column ) as key }
                                                                        { #if !api.keys[ column ].entries.filter( function (entry) { return entry.value === key; } )[0].hidden }
                                                                            <li>
                                                                                <label class="m-0 d-block">
                                                                                    <input type="checkbox" class="input table-input" value={ key } on:change={ onKeySelect( event, column, key) }> { key }
                                                                                </label>
                                                                            </li>
                                                                        { /if }
                                                                    { /each }-->

                                                                    { #each api.keys[ column ].entries.filter( function (entry) { return !entry.hidden; } ) as entry }
                                                                        <li>
                                                                            <label class="m-0 d-block">
                                                                                <input type="checkbox" class="input table-input" value={ entry.value } checked={ entry.checked } on:change={ onKeySelect( event, column, entry.value ) }> { entry.value }
                                                                            </label>
                                                                        </li>
                                                                    { /each }
                                                                </ul>
                                                            </div>
                                                        { /if }
                                                    </div>
                                                { /if }
                                            </th>
                                        { /each }

                                        <th class="controls"></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    { #each records as record, i }
                                        { #if !record.hidden }
                                            <tr data-index={ i } data-id={ record.id }>
                                                { #if selectable }
                                                    <td class="selectable text-center">
                                                        <input type="checkbox" class="input table-input" value="select" on:change={ onSelectionChange }>
                                                    </td>
                                                { /if }

                                                { #each record.values as value }
                                                    { #if typeof value.content === 'undefined' }
                                                        <td>{ value.value ?? '' }</td>
                                                    { :else }
                                                        <td>{ @html value.content ?? '' }</td>
                                                    { /if }
                                                { /each }

                                                <td class="controls text-center">
                                                    { #if controls }
                                                        { #if record.controls }
                                                            { @html record.controls }
                                                        { /if }
                                                    { /if }
                                                </td>
                                            </tr>
                                        { /if }
                                    { /each }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        { :else }
            <div class="row">
                <div class="col d-flex justify-content-center align-items-center" style="flex-direction: column;">
                    <span class="mb-3" style="font-size: 18px; font-weight: 800;">NO DATA</span>

                    <i class="fa-solid fa-box" style="font-size: 32px;"></i>
                </div>
            </div>
        { /if }
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

    .search-box .table-input
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
        z-index: 1;
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