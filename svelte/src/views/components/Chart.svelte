<script>

    import { browser } from '$app/environment';

    import { onMount } from 'svelte';

    import { createEventDispatcher } from 'svelte';



    const dispatch = createEventDispatcher();



    onMount
    (
        function ()
        {
            // (Getting the value)
            element.api = {};



            // (Getting the value)
            api.element = element;
        }
    )
    ;



    let element;



    export let api    = {};

    export let width  = 640;
    export let height = 360;



    let chart;



    // Returns [void]
    api.build = function (config)
    {
        if ( chart )
        {// Value found
            // (Destroying the chart)
            chart.destroy();
        }



        // (Creating a Chart)
        chart = new Chart( element, config );
    }

</script>

<canvas class="component chart-component" bind:this={ element } width={ width } height={ height }></canvas>

<style>

    .component
    {
        /*width: 100% !important;*/
        margin: 0 auto;
    }

</style>