<script>

    import { createEventDispatcher, onMount } from 'svelte';

    const dispatch = createEventDispatcher();



    export let id;
    export let width = '640px';
    export let title;

    export let urlkey = '';

    let element;

    export let api;

    $:
        if ( element )
        {// Value found
            // (Setting the value)
            api = {};

            // Returns [void]
            api.show = function ()
            {
                // (Showing the modal)
                jQuery(element).modal('show');



                if ( !urlkey ) return;



                // (Getting the value)
                const urlParams = new URLSearchParams( window.location.search );

                // (Setting the param)
                urlParams.set( 'modal', urlkey );

                // (Pushing the state)
                history.pushState( {}, null, '?' + urlParams );
            }

            // Returns [void]
            api.hide = function ()
            {
                // (Showing the modal)
                jQuery(element).modal('hide');



                if ( !urlkey ) return;



                // (Getting the value)
                const urlParams = new URLSearchParams( window.location.search );

                // (Setting the param)
                urlParams.delete( 'modal' );

                // (Pushing the state)
                history.pushState( {}, null, '?' + urlParams );
            }

            // (Listening for the event)
            jQuery(element).on('hidden.bs.modal', function () {
                // (Triggering the event)
                dispatch('close');



                if ( !urlkey ) return;



                // (Getting the value)
                const urlParams = new URLSearchParams( window.location.search );

                // (Setting the param)
                urlParams.delete( 'modal' );

                // (Pushing the state)
                history.pushState( {}, null, '?' + urlParams );
            });



            if ( urlkey )
            {// Value found
                // (Getting the value)
                const urlParams = new URLSearchParams( window.location.search );

                if ( urlParams.has( 'modal' ) && urlParams.get( 'modal' ) === urlkey )
                {// Match OK
                    // (Showing the modal)
                    api.show();
                }
            }
        }
    


    // (Listening for the event)
    onMount
    (
        function ()
        {
            // (Removing the element)
            jQuery('.modal-backdrop').remove();
        }
    )
    ;

</script>

<!-- Modal -->
<div class="modal fade" id="{ id }" tabindex="-1" role="dialog" aria-hidden="true" bind:this={ element }>
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: { width }">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{ title }</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <slot/>
            </div>
            <!--
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            -->
        </div>
    </div>
</div>