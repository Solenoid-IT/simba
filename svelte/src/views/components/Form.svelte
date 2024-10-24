<script>

    import { createEventDispatcher } from 'svelte';



    export let id;



    // (Getting the value)
    const dispatch = createEventDispatcher();



    // Returns [void]
    function onSubmit ()
    {
        // (Triggering the event)
        dispatch('submit');
    }



    let element;



    export let api;



    $:
        if ( element )
        {// Value found
            // (Setting the value)
            api = {};



            // (Getting the value)
            api.element = element;



            // (Setting the value)
            api.disabled = false;



            // Returns [void]
            api.reset = function ()
            {
                // (Resetting the form)
                element.reset();

                // (Iterating each entry)
                element.querySelectorAll('.form-input').forEach
                (
                    function (el)
                    {
                        // (Setting the property)
                        el.value = '';

                        if ( el.classList.contains('input-invalid') )
                        {// Match OK
                            // (Removing the class name)
                            el.classList.remove('input-invalid');
                        }
                    }
                )
                ;

                // (Iterating each entry)
                element.querySelectorAll('.table-component[data-input]').forEach
                (
                    function (el)
                    {
                        // (Emptying the records)
                        el.api.emptyRecords();
                    }
                )
                ;
            }

            // Returns [object]
            api.validate = function ()
            {
                // (Setting the value)
                const result =
                {
                    'entries': {},
                    'valid':   true,

                    'fetch':   function ()
                    {
                        // (Setting the value)
                        const values = {};

                        for ( const name in result.entries )
                        {// Processing each entry
                            // (Getting the value)
                            values[ name ] = result.entries[ name ].value;
                        }



                        // Returning the value
                        return values;
                    }
                }
                ;



                // (Iterating each entry)
                element.querySelectorAll('.form-input:not(.input-ignore)').forEach
                (
                    function (inputElement)
                    {
                        if ( inputElement.classList.contains('table-component') ) return;



                        // (Getting the value)
                        const name = inputElement.getAttribute('name');

                        // (Getting the value)
                        result.entries[ name ] =
                        {
                            'name':    name,
                            'value':   inputElement.getAttribute('type') === 'checkbox' ? inputElement.checked : inputElement.value,

                            'element': inputElement,

                            'valid':   true
                        }
                        ;



                        if ( typeof inputElement.value === 'string' )
                        {// Match OK
                            if ( inputElement.getAttribute('data-required') !== null )
                            {// (Entry is required)
                                // (Getting the value)
                                let regex = inputElement.getAttribute('data-regex');

                                if ( regex === null )
                                {// Value not found
                                    // (Getting the value)
                                    result.entries[ name ].valid = inputElement.value.length > 0;
                                }
                                else
                                {// Value found
                                    // (Getting the value)
                                    result.entries[ name ].valid = new RegExp( regex ).test( inputElement.value );
                                }
                            }



                            if ( result.entries[ name ].valid )
                            {// (Entry is valid)
                                // (Removing the class)
                                result.entries[ name ].element.classList.remove( 'input-invalid' );
                            }
                            else
                            {// (Entry is not valid)
                                // (Adding the class)
                                result.entries[ name ].element.classList.add( 'input-invalid' );
                            }
                        }



                        /*

                        if ( !result.entries[ name ].valid )
                        {// (Entry is not valid)
                            if ( result.valid )
                            {// Value is true
                                // (Focusing the element)
                                result.entries[ name ].element.focus();
                            }



                            // (Setting the value)
                            result.valid = false;
                        }

                        */
                    }
                )
                ;



                // (Iterating each entry)
                element.querySelectorAll('.table-component[data-input]').forEach
                (
                    function (el)
                    {
                        // (Getting the values)
                        const name  = el.getAttribute('data-input');
                        const value = el.api.listRecords();



                        // (Getting the value)
                        result.entries[ name ] =
                        {
                            'name':    name,
                            'value':   value,

                            'element': el,
                            'valid':   el.getAttribute('data-required') === null ? true : value.length > 0
                        }
                        ;



                        if ( result.entries[ name ].valid )
                        {// (Entry is valid)
                            // (Removing the class)
                            result.entries[ name ].element.classList.remove( 'input-invalid' );
                        }
                        else
                        {// (Entry is not valid)
                            // (Adding the class)
                            result.entries[ name ].element.classList.add( 'input-invalid' );
                        }
                    }
                )
                ;



                for ( const name in result.entries )
                {// Processing each entry
                    // (Getting the value)
                    const entry = result.entries[name];

                    if ( !entry.valid )
                    {// Match OK
                        // (Setting the value)
                        result.valid = false;



                        // (Focusing the element)
                        entry.element.focus();



                        // Breaking the iteration
                        break;
                    }
                }



                // Returning the value
                return result;
            }

            // Returns [void]
            api.setValues = function (values)
            {
                // (Setting the value)
                let el = null;

                for ( const k in values )
                {// Processing each entry
                    // (Getting the value)
                    el = element.querySelector(`.form-input[name="${ k }"]`);

                    if ( el === null )
                    {// Value not found
                        // (Getting the value)
                        el = element.querySelector(`.form-input.table-component[data-input="${ k }"]`);

                        if ( el === null )
                        {// Value not found
                            // (Doing nothing)
                        }
                        else
                        {// Value found
                            // (Setting the records)
                            el.api.setRecords( values[k] );
                        }
                    }
                    else
                    {// Value found
                        // (Setting the property)
                        el.value = values[k];

                        if ( el.tagName === 'INPUT' && el.getAttribute('type') === 'checkbox' )
                        {// Match OK
                            // (Setting the property)
                            el.checked = values[k];
                        }
                    }
                }
            }
        }

    
    
    $:
        if ( api )
        {// Value found
            if ( api.disabled )
            {// Value is true
                // (Setting the properties)
                jQuery(element).find('.form-input').prop( 'disabled', true );
                jQuery(element).find('.btn[type="submit"]').prop( 'disabled', true );
            }
            else
            {// Value is false
                // (Setting the properties)
                jQuery(element).find('.form-input').prop( 'disabled', false );
                jQuery(element).find('.btn[type="submit"]').prop( 'disabled', false );
            }
        }

</script>

<form class="form" id="{ id }" on:submit|preventDefault={ onSubmit } bind:this={ element }>
    <slot/>
</form>

<style>

    .form :global(.form-input.is-invalid),
    .form :global(.form-input.input-invalid)
    {
        border-color: #dc3545 !important;
        padding-right: calc(1.5em + .75rem) !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right calc(.375em + .1875rem) center !important;
        background-size: calc(.75em + .375rem) calc(.75em + .375rem) !important;
    }
    
    .form :global(.form-input.is-invalid:focus),
    .form :global(.form-input.input-invalid:focus)
    {
        box-shadow: 0 0 0 .2rem rgba(220,53,69,.25) !important;
    }

</style>