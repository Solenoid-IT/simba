<script>

    import Toast from './Toast.svelte';

    import { activityStack } from '../../stores/activityStack.js';



    const pvt = {};
    const pub = {};



    pvt.element = null;



    export let api = null;



    api = pub;



    // Returns [void]
    function onToastClose (index)
    {
        // (Removing the activity)
        activityStack.remove(index);

        // (Getting the value)
        $activityStack = $activityStack;
    }
</script>

{ #if api }
    <div class="swg swg-activity-stack" bind:this={ pvt.element }>
        <div aria-live="polite" aria-atomic="true" style="position: relative; min-height: 200px;">
            <div style="position: absolute; top: 0; right: 0;">
                { #each $activityStack as activity, index }
                    <Toast title={ activity.title } message={ activity.message } on:close={ () => { onToastClose( index ); } }/>
                { /each }
            </div>
        </div>
    </div>
{ /if }

<style>

    .swg.swg-activity-stack
    {
        position: absolute;
        right: 0;
        top: 0;
    }

</style>