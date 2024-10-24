<script>

    import { createEventDispatcher } from 'svelte';

    const dispatch = createEventDispatcher();



    // (Setting the values)
    const pvt = {};
    const self = {};



    // (Setting the values)
    pvt.visible            = true;
    pvt.eventListener      = {};

    self.element           = null;



    // Returns [void]
    self.addEventListener = function (type, callback)
    {
        if ( typeof pvt.eventListener[ type ] === 'undefined' ) pvt.eventListener[ type ] = [];

        // (Appending the value)
        pvt.eventListener[ type ].push( callback );
    }

    // Returns [void]
    self.triggerEvent = function (type, data)
    {
        if ( typeof pvt.eventListener[ type ] === 'undefined' ) return;

        for (const callback of pvt.eventListener[ type ])
        {// Processing each entry
            // (Calling the function)
            callback( data );
        }
    }



    export let id    = null;
    export let title = null;
    export let api   = null;



    // (Getting the value)
    api = self;



    // Returns [void]
    function onClick (event)
    {
        // (Triggering the event)
        dispatch( 'click', event );
    }

</script>

{ #if api }
    { #if pvt.visible }
        <!-- svelte-ignore a11y-click-events-have-key-events -->
        <div class="swg swg-button" id="{ id }" bind:this={ self.element } on:click={ onClick }>
            <button class="btn btn-link btn-sm" title={ title }>
                <slot name="body"/>
            </button>
        </div>
    { /if }
{ /if }

<style>

    .swg.swg-form
    {
        
    }

</style>