<script>

    import { createEventDispatcher } from 'svelte';

    const dispatch = createEventDispatcher();



    const pvt = {};
    const pub = {};



    pvt.element = null;



    export let title   = '';
    export let message = '';

    export let api     = null;



    api = pub;



    // Returns [void]
    function onClose ()
    {
        // (Triggering the event)
        dispatch('close');
    }
</script>

{ #if api }
    <div class="toast show mt-2" role="alert" aria-live="assertive" aria-atomic="true" bind:this={ pvt.element }>
        <div class="toast-header">
            <div class="toast-title">{ @html title }</div>

            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close" on:click={ onClose }>
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            { @html message }
        </div>
    </div>
{ /if }

<style>

    .toast .toast-title :global( a )
    {
        color: #000000;
        text-decoration: none;
    }

    .toast :global( .toast-body ul )
    {
        margin-bottom: 0;
    }

</style>