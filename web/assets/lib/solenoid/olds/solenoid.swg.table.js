// © Solenoid Team



if ( typeof Solenoid === 'undefined' ) Solenoid = {};

if ( typeof Solenoid.SWG === 'undefined' ) Solenoid.SWG = {};



Solenoid.SWG.Table = {};



Solenoid.SWG.Table.Entry = function (element)
{
    const private = {};
    const public  = this;



    // Returns [self]
    private.__construct = function (element)
    {
        if ( element.swg ) return element.swg;



        // (Getting the value)
        public.element = element;



        // (Getting the value)
        element.swg = public;
    }



    // Returns [string]
    public.getId = function ()
    {
        // Returning the value
        return element.closest('tr').getAttribute('data-id');
    }

    // Returns [object]
    public.getValues = function ()
    {
        // (Setting the value)
        let values = {};



        // (Iterating each entry)
        element.querySelectorAll('td[data-column]').forEach
        (
            function (el)
            {
                // (Getting the value)
                const value = el.getAttribute('data-real-value') ? el.getAttribute('data-real-value') : el.getAttribute('data-value');



                // (Getting the value)
                values[ el.getAttribute('data-column') ] = value;
            }
        )
        ;



        // Returning the value
        return values;
    }



    private.__construct(element);
}



Solenoid.SWG.Table.Table = function (element)
{
    const private = {};
    const public  = this;



    // Returns [self]
    private.__construct = function (element)
    {
        if ( element.swg ) return element.swg;



        // (Setting the values)
        private.eventCallbacks = {};
        private.sortReverse   = false;



        // (Getting the values)
        public.element          = element;

        public.searchBoxElement = element.querySelector('.search-box');
        public.tableElement     = element.querySelector('table');



        // (Iterating each entry)
        public.tableElement.querySelectorAll('thead th[data-column] .title').forEach
        (
            function (el)
            {
                // (Listening for the event)
                el.addEventListener('click', function (event) {
                    // (Getting the value)
                    const column = el.closest('th').getAttribute('data-column');



                    // (Sorting the table)
                    public.sort( column, private.sortReverse );



                    // (Getting the value)
                    private.sortReverse = !private.sortReverse;
                });
            }
        )
        ;

        // (Listening for the event)
        public.tableElement.addEventListener('click', function (event) {
            // (Getting the value)
            const target = event.target.closest('.btn');

            if ( target && target.closest('.table-row-controls') )
            {// Value found
                // (Triggering the event)
                public.triggerEvent
                (
                    'entry.action',
                    {
                        'entry':  new Solenoid.SWG.Table.Entry( target.closest('tr') ),
                        'action': target.value
                    }
                )
                ;
            }
        });

        // (Iterating each entry)
        public.tableElement.querySelectorAll('thead th[data-column] .key-search-box').forEach
        (
            function (el)
            {
                // (Listening for the event)
                el.addEventListener('change', function (event) {
                    if ( event.target.closest('li') === null ) return;



                    if ( event.target.closest('ul').getAttribute('data-type') === 'controls' )
                    {// Match OK
                        // (Getting the value)
                        const isChecked = event.target.closest('ul').querySelector('.input').checked;

                        // (Iterating each entry)
                        this.querySelectorAll(`ul[data-type="values"] li${ isChecked ? ':not(.box-hidden)' : '' } .input`).forEach
                        (
                            function (el)
                            {
                                // (Getting the value)
                                el.checked = isChecked;
                            }
                        )
                        ;
                    }



                    // (Setting the html content)
                    el.querySelector('.num-results[data-type="selected"]').innerHTML = Array.from( this.querySelectorAll('ul[data-type="values"] li:not(.box-hidden) .input:checked') ).length;



                    if ( this.querySelector('ul li:not(.box-hidden) .input:checked') !== null )
                    {// Match OK
                        // (Adding the class)
                        this.closest('th').querySelector('.key-search-box-button').setAttribute('data-state', 'filter-active');
                    }
                    else
                    {// Match failed
                        // (Adding the class)
                        this.closest('th').querySelector('.key-search-box-button').removeAttribute('data-state');
                    }



                    // (Triggering the event)
                    public.triggerEvent
                    (
                        'key-search',
                        {
                            'values': public.getKeyValues()
                        }
                    )
                    ;
                });

                // (Listening for the event)
                el.querySelector('.input[data-type="key-search"]').addEventListener('input', function (event) {
                    // (Iterating each entry)
                    el.querySelectorAll('.input').forEach
                    (
                        function (inputElement)
                        {
                            // (Setting the value)
                            inputElement.checked = false;
                        }
                    )
                    ;

                    // (Removing the attribute)
                    el.closest('th').querySelector('.key-search-box-button').removeAttribute('data-state');

                    // (Setting the html content)
                    el.closest('th').querySelector('.num-results[data-type="selected"]').innerHTML = '0';



                    // (Setting the value)
                    let numResults = 0;



                    // (Getting the value)
                    const keySearchInputElement = this;

                    // (Iterating each entry)
                    el.querySelectorAll('ul[data-type="values"] .key-value').forEach
                    (
                        function (kvElement)
                        {
                            // (Getting the value)
                            const liElement = kvElement.closest('li');

                            if ( kvElement.innerHTML.toLowerCase().indexOf( keySearchInputElement.value.toLowerCase() ) === -1 )
                            {// Value not found
                                // (Adding the class)
                                liElement.classList.add('box-hidden');
                            }
                            else
                            {// Value found
                                // (Removing the class)
                                liElement.classList.remove('box-hidden');



                                // (Incrementing the value)
                                numResults += 1;
                            }
                        }
                    )
                    ;



                    // (Setting the html content)
                    el.closest('th').querySelector('.num-results[data-type="keys"]').innerHTML = numResults;
                });
            }
        )
        ;



        if ( public.searchBoxElement )
        {// Value found
            // (Listening for the event)
            public.searchBoxElement.querySelector('.input').addEventListener('input', function (event) {
                // (Triggering the event)
                public.triggerEvent
                (
                    'global-search',
                    {
                        'value': this.value
                    }
                )
                ;
            });



            // (Setting the html content)
            public.searchBoxElement.querySelector('.num-results').innerHTML = public.countValues();
        }

        // (Iterating each entry)
        element.querySelectorAll('thead th[data-column] .input[data-type="local-search"]').forEach
        (
            function (el)
            {
                // (Listening for the event)
                el.addEventListener('input', function (event) {
                    // (Triggering the event)
                    public.triggerEvent
                    (
                        'local-search',
                        {
                            'values': public.getSearchValues()
                        }
                    )
                    ;
                });
            }
        )
        ;

        // (Iterating each entry)
        element.querySelectorAll('thead td[data-column] ul[data-type="controls"] .input').forEach
        (
            function (el)
            {
                // (Listening for the event)
                el.addEventListener('input', function (event) {
                    // (Triggering the event)
                    public.triggerEvent
                    (
                        'key-search',
                        {
                            'values': public.getSearchValues()
                        }
                    )
                    ;
                });
            }
        )
        ;



        // (Click-Event on the element)
        element.querySelectorAll('thead th[data-column] .key-search-box-button').forEach
        (
            function (el)
            {
                // (Listening for the event)
                el.addEventListener('click', function (event) {
                    this.querySelectorAll('*').forEach
                    (
                        function (iconElement)
                        {
                            // (Adding the class)
                            iconElement.classList.add('box-hidden');
                        }
                    )
                    ;



                    // (Getting the element)
                    const boxElement = el.closest('th').querySelector('.key-search-box');

                    if ( boxElement.classList.contains('box-hidden') )
                    {// Match OK
                        // (Removing the class)
                        boxElement.classList.remove('box-hidden');

                        // (Removing the class)
                        this.querySelector('.fa-caret-up').classList.remove('box-hidden');
                    }
                    else
                    {// Match failed
                        // (Adding the class)
                        boxElement.classList.add('box-hidden');

                        // (Removing the class)
                        this.querySelector('.fa-caret-down').classList.remove('box-hidden');
                    }
                });



                // (Triggering the event)
                el.click();
            }
        )
        ;



        // (Getting the value)
        element.swg = public;



        // (Calculating the column values)
        public.calculateColumnValues();
    }



    // Returns [void]
    public.addEventListener = function (type, callback)
    {
        if ( typeof private.eventCallbacks[ type ] === 'undefined' ) private.eventCallbacks[ type ] = [];



        // Appending the value
        private.eventCallbacks[ type ].push( callback );
    }

    // Returns [void]
    public.triggerEvent = function (type, data)
    {
        if ( typeof private.eventCallbacks[ type ] === 'undefined' ) return;



        for (const callback of private.eventCallbacks[ type ])
        {// Processing each entry
            // (Calling the function)
            callback( data );
        }
    }



    // Returns [self]
    public.sort = function (column, reverse)
    {
        if (typeof reverse === 'undefined') reverse = false;



        // Returns [string|number]
        function autoConvert (value)
        {
            // (Getting the value)
            let v = value;

            if ( /^[0-9]+$/.test(value) )
            {// Match OK
                // (Getting the value)
                v = parseInt(value);
            }
            else
            if ( /^[0-9]+\.[0-9]+$/.test(value) )
            {// Match OK
                // (Getting the value)
                v = parseFloat(value);
            }



            // Returning the value
            return v;
        }



        // (Setting the value)
        let rows = [];

        // (Iterating each entry)
        element.querySelectorAll('tbody tr[data-id]').forEach
        (
            function (el)
            {
                // (Appending the value)
                rows.push
                (
                    {
                        'element': el,
                        'value':   autoConvert( el.querySelector(`td[data-column="${ column }"]`).getAttribute('data-value') )
                    }
                )
                ;
            }
        )
        ;



        // (Sorting the array)
        rows.sort
        (
            function (a, b)
            {
                if ( a['value'] < b['value'] )
                {// Match OK
                    // (Switching the elements)
                    reverse ? a['element'].parentNode.insertBefore( b['element'], a['element'] ) : b['element'].parentNode.insertBefore( a['element'], b['element'] );



                    // Returning the value
                    return reverse ? 1 : -1;
                }
                else
                if ( a['value'] > b['value'] )
                {// Match OK
                    // (Switching the elements)
                    reverse ? b['element'].parentNode.insertBefore( a['element'], b['element'] ) : a['element'].parentNode.insertBefore( b['element'], a['element'] );



                    // Returning the value
                    return reverse ? -1 : 1;
                }



                // Returning the value
                return 0;
            }
        )
        ;



        // (Iterating each entry)
        element.querySelectorAll(`thead th[data-column] .sort-dir-indicator-box > *`).forEach
        (
            function (el)
            {
                // (Removing the class)
                el.classList.remove('current-indicator');
            }
        )
        ;

        // (Setting the class)
        element.querySelector(`thead th[data-column="${ column }"] .sort-dir-indicator-box > *:${ private.sortReverse ? 'last' : 'first' }-child`).classList.add('current-indicator');



        // Returning the value
        return public;
    }



    // Returns [number]
    public.countValues = function (all)
    {
        if ( typeof all === 'undefined' ) all = false;



        // Returning the value
        return element.querySelectorAll(`tbody tr${ all ? '' : ':not(.box-hidden)' }`).length;
    }



    // Returns [array<string>]
    public.getColumns = function ()
    {
        // Returning the value
        return Array.from( element.querySelectorAll('thead tr:first-child th[data-column]') ).map( function (el) { return el.getAttribute('data-column'); } );
    }

    // Returns [array<object>]
    public.getValues = function (columns, all)
    {
        if ( typeof columns === 'undefined' ) columns = [];
        if ( typeof all === 'undefined' ) all = false;



        // (Setting the value)
        let records = [];

        // (Iterating each entry)
        element.querySelectorAll(`tbody tr${ all ? '' : ':not(.box-hidden)' }`).forEach
        (
            function (el)
            {
                // (Setting the value)
                let record = {};



                if ( columns.length === 0 )
                {// Match OK
                    // (Iterating each entry)
                    el.querySelectorAll('td[data-column]').forEach
                    (
                        function (el)
                        {
                            // (Getting the value)
                            record[ el.getAttribute('data-column') ] = el.getAttribute('data-value');
                        }
                    )
                    ;
                }
                else
                {// Match failed
                    for (const column of columns)
                    {// Processing each entry
                        // (Getting the value)
                        record[ column ] = el.querySelector(`td[data-column=${ column }]`).getAttribute('data-value');
                    }
                }



                // (Appending the value)
                records.push(record);
            }
        )
        ;



        // Returning the value
        return records;
    }



    // Returns [array<object>]
    public.getSearchValues = function ()
    {
        // (Setting the value)
        let values = {};

        // (Iterating each entry)
        element.querySelectorAll('thead th[data-column] .input[data-type="local-search"]').forEach
        (
            function (inputElement)
            {
                // (Getting the value)
                values[ inputElement.closest('th').getAttribute('data-column') ] = inputElement.value;
            }
        )
        ;



        // Returning the value
        return values;
    }

    // Returns [array<object>]
    public.getKeyValues = function ()
    {
        // (Setting the value)
        let values = {};

        for (const column of public.getColumns())
        {// Processing each entry
            // (Iterating each entry)
            element.querySelectorAll(`thead th[data-column="${ column }"] .key-search-box ul[data-type="values"] .input:checked`).forEach
            (
                function (inputElement)
                {
                    if ( typeof values[ column ] === 'undefined' ) values[ column ] = [];



                    // (Appending the value)
                    values[ column ].push( inputElement.closest('li').querySelector('.key-value').innerHTML );
                }
            )
            ;
        }



        // Returning the value
        return values;
    }



    // Returns [self]
    public.resetKeySearchBox = function (columns)
    {
        if ( typeof columns === 'undefined' ) columns = public.getColumns();



        for (const column of columns)
        {// Processing each entry
            // (Getting the element)
            const thElement = element.querySelector(`th[data-column="${ column }"]`);



            // (Getting the element)
            const keySearchBoxButtonElement = thElement.querySelector('.key-search-box-button');

            if ( keySearchBoxButtonElement.getAttribute('data-state') !== null )
            {// Match OK
                // (Triggering the event)
                keySearchBoxButtonElement.click();
            }

            // (Removing the attribute)
            keySearchBoxButtonElement.removeAttribute('data-state');



            // (Iterating each entry)
            thElement.querySelectorAll('ul .input').forEach
            (
                function (inputElement)
                {
                    // (Setting the value)
                    inputElement.checked = false;

                    // (Removing the class)
                    inputElement.closest('li').classList.remove('box-hidden');
                }
            )
            ;



            // (Setting the html content)
            thElement.querySelector('.num-results[data-type="selected"]').innerHTML = '0';
        }



        // Returning the value
        return public;
    }



    // Returns [string]
    public.exportCSV = function (columns, header, columnSeparator, rowSeparator)
    {
        if ( typeof columns === 'undefined' ) columns = [];
        if ( typeof header === 'undefined' ) header = false;
        if ( typeof columnSeparator === 'undefined' ) columnSeparator = ';';
        if ( typeof rowSeparator === 'undefined' ) rowSeparator = "\n";



        // Returning the value
        return ( header ? ( columns.length === 0 ? public.getColumns() : columns ).join(columnSeparator) + rowSeparator : '' ) + public.getValues(columns).map( function (record) { return Object.values(record).join(columnSeparator); } ).join(rowSeparator);
    }



    // Returns [self]
    public.filter = function (condition)
    {
        if ( typeof condition === 'undefined' ) condition = 'FILTER_GLOBAL';



        if ( condition === 'FILTER_GLOBAL' )
        {// Match OK
            // (Getting the value)
            condition = function (entry)
            {
                // Returning the value
                return Object.values( entry.getValues() ).join('').toLowerCase().indexOf( public.searchBoxElement.querySelector('.input').value.toLowerCase() ) !== -1;
            }
        }
        else
        if ( condition === 'FILTER_LOCAL' )
        {// Match OK
            // (Getting the value)
            condition = function (entry)
            {
                // (Getting the values)
                const entryValues  = entry.getValues();
                const searchValues = public.getSearchValues();

                for (const column in searchValues)
                {// Processing each entry
                    if ( entryValues[ column ].toLowerCase().indexOf( searchValues[ column ].toLowerCase() ) === -1 ) return false;
                }



                // Returning the value
                return true;
            }
        }
        else
        if ( condition === 'FILTER_KEY' )
        {// Match OK
            // (Getting the value)
            condition = function (entry)
            {
                // (Getting the values)
                const entryValues = entry.getValues();
                const keyValues   = public.getKeyValues();

                for (const column in keyValues)
                {// Processing each entry
                    if ( !keyValues[ column ].map( function (key) { return key.toLowerCase() } ).includes( entryValues[ column ].toLowerCase() ) ) return false;
                }



                // Returning the value
                return true;
            }
        }



        // (Setting the value)
        let numResults = 0;



        // (Iterating each entry)
        element.querySelectorAll('tbody tr').forEach
        (
            function (rowElement)
            {
                // (Getting the value)
                const entry = new Solenoid.SWG.Table.Entry( rowElement );

                if ( condition( entry ) )
                {// Value is true
                    // (Removing the class)
                    rowElement.classList.remove('box-hidden');



                    // (Incrementing the value)
                    numResults += 1;
                }
                else
                {// Value is false
                    // (Adding the class)
                    rowElement.classList.add('box-hidden');
                }
            }
        )
        ;



        if ( public.searchBoxElement )
        {// Value found
            // (Setting the html content)
            public.searchBoxElement.querySelector('.num-results').innerHTML = numResults;
        }



        // Returning the value
        return public;
    }

    // Returns [self]
    public.resetFilter = function ()
    {
        // (Setting the value)
        element.querySelector('.search-box .input').value = '';



        // (Resetting the key search box)
        public.resetKeySearchBox();



        // (Filtering the values)
        public.filter();



        // Returning the value
        return public;
    }



    // Returns [self]
    public.calculateColumnValues = function ()
    {
        // (Setting the value)
        let uniqueValues = {};



        // (Getting the value)
        const values = public.getValues( [], true );

        for (const record of values)
        {// Processing each entry
            for (const column in record)
            {// Processing each entry
                if ( typeof uniqueValues[ column ] === 'undefined' ) uniqueValues[ column ] = [];



                // (Appending the value)
                uniqueValues[ column ].push( record[ column ] );
            }
        }



        for (const column in uniqueValues)
        {// Processing each entry
            // (Getting the value)
            uniqueValues[ column ] = [ ... new Set( uniqueValues[ column ] ) ] ;



            // (Getting the element)
            const ulElement = element.querySelector(`thead th[data-column="${ column }"] .key-search-box ul[data-type="values"]`);

            // (Setting the html content)
            ulElement.innerHTML = '';



            for (const value of uniqueValues[ column ])
            {// Processing each entry
                // (Creating an element)
                const liElement = document.createElement('li');

                // (Setting the html content)
                liElement.innerHTML =
                    `
                        <label>
                            <input type="checkbox" class="input">
                            <span class="key-value">${ value }</span>
                        </label>
                    `
                ;



                // (Appending the value)
                ulElement.appendChild( liElement );
            }



            // (Setting the html contents)
            ulElement.closest('.key-search-box').querySelector('.num-results[data-type="total"]').innerHTML = uniqueValues[ column ].length;
            ulElement.closest('.key-search-box').querySelector('.num-results[data-type="keys"]').innerHTML  = uniqueValues[ column ].length;
        }



        // Returning the value
        return public;
    }



    // Returns [object]
    public.getValuesByRowElement = function (rowElement)
    {
        // (Setting the value)
        let values = {};



        // (Iterating each entry)
        rowElement.forEach
        (
            function (el)
            {
                // (Getting the value)
                values[ el.getAttribute('data-column') ] = el.getAttribute('data-value');
            }
        )
        ;



        // Returning the value
        return values;
    }

    // Returns [object]
    public.getValuesById = function (id)
    {
        // Returning the value
        return public.getValuesByRowElement( element.querySelector(`tbody tr[data-id="${ id }"] td[data-column]`) );
    }



    private.__construct( element );
}
;



// (Listening for the event)
window.addEventListener('load', function () {
    // (Appending the value)
    const styleElement = document.createElement('style');

    // (Setting the html content)
    styleElement.innerHTML =
        `
            .swg.swg-table
            {
                width: 100%;
                font-size: 14px;
            }

            .swg.swg-table table thead th[data-column] .title
            {
                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;

                cursor: pointer;
            }

            .swg.swg-table table thead th[data-column] .sort-dir-indicator-box
            {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;

                font-size: 8px;
                color: #c8c8c8;
            }

            .swg.swg-table .sort-dir-indicator-box > *
            {

            }

            .swg.swg-table .sort-dir-indicator-box .current-indicator
            {
                color: #000000;
            }



            .swg.swg-table table thead th,
            .swg.swg-table table tbody td
            {
                padding: 4px 12px;
            }
            .swg.swg-table table tbody tr:nth-child(even)
            {
                background-color: #e4e4e4;
            }

            .swg.swg-table table tbody tr:hover
            {
                background-color: #d9d9d9;
            }



            .swg.swg-table .box-hidden
            {
                display: none !important;
            }



            .swg.swg-table .search-box
            {
                margin: 6px 0;

                display: flex;
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }

            .swg.swg-table .num-results-box
            {
                font-size: 12px;
                white-space: nowrap;
            }

            .swg.swg-table .num-results
            {
                color: #0d6efd;
                font-weight: 600;
            }

            .swg.swg-table .num-results[data-type="keys"]
            {
                margin-left: 6px;
            }

            .swg.swg-table .search-box .input
            {
                margin-left: 20px;
                max-width: 400px;
            }



            .swg.swg-table .input[data-type="local-search"]
            {
                margin: 10px 0;
            }



            .swg.swg-table .key-search-box-button
            {
                width: 100%;
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
                background-color: #dbdbdb;
                border-radius: 2px;
                cursor: pointer;
            }

            .swg.swg-table .key-search-box-button[data-state="filter-active"]
            {
                background-color: #0d6efd;
                color: #ffffff;
            }



            .swg.swg-table .key-search-box
            {
                position: relative;
            }

            .swg.swg-table .key-search-box-inner
            {
                padding: 14px 6px;
                position: absolute;
                left: 0;
                right: 0;
                top: 0;
                background-color: #e6e6e6;
                border-radius: 4px;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }

            .swg.swg-table .key-search-box ul
            {
                margin: 10px 0;
                padding: 0;
                list-style: none;
            }

            .swg.swg-table .key-search-box ul[data-type="values"]
            {
                min-height: 160px;
                max-height: 300px;
                overflow-y: auto;
            }

            .swg.swg-table .key-search-box ul li label
            {
                display: flex;
                flex-direction: row;
                justify-content: start;
                align-items: center;
            }

            .swg.swg-table .key-search-box ul li .input
            {
                margin-right: 20px;
                font-size: 10px;
            }

            .swg.swg-table .input[data-type="key-search"]
            {
                margin-top: 10px;
            }

            .swg.swg-table .input[data-type="local-search"],
            .swg.swg-table .key-search-box *
            {
                font-size: 10px;
            }

            .swg.swg-table .key-search-box .key-value
            {
                position: relative;
                bottom: 1px;
            }

            .swg.swg-table .key-search-box .num-results[data-type="keys"]
            {
                margin-top: -1px;
            }
        `
    ;



    // (Appending the element)
    document.head.appendChild( styleElement );
});