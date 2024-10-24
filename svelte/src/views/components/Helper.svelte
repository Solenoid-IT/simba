<script>

    export let orient = 'bottom';

</script>

<div class="helper" data-orient="{ orient }">
    <i class="fa-solid fa-circle-info"></i>

    <span class="tooltip-text">
        <slot/>
    </span>
</div>

<style>

    .helper
    {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black;
    }

    .helper i
    {

    }

    .helper:hover i
    {
        color: var( --simba-primary );
    }

    .helper .tooltip-text
    {
        visibility: hidden;
        display: block;
        /*min-width: 300px;*/
        /*background-color: black;*/
        background-color: #545560;
        color: #fff;
        text-align: left;
        padding: 6px 12px;
        border-radius: 6px;

        position: absolute;
        z-index: 1;

        white-space: nowrap;
    }

    .helper:hover .tooltip-text
    {
        visibility: visible;
    }



    /*

    .helper[data-orient="top"] .tooltip-text::after
    {
        content: " ";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: black transparent transparent transparent;
    }

    .helper[data-orient="bottom"] .tooltip-text::after
    {
        content: " ";
        position: absolute;
        bottom: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent black transparent;
    }

    .helper[data-orient="left"] .tooltip-text::after
    {
        content: " ";
        position: absolute;
        top: 50%;
        right: 100%;
        margin-top: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent black transparent transparent;
    }

    .helper[data-orient="right"] .tooltip-text::after
    {
        content: " ";
        position: absolute;
        top: 50%;
        left: 100%;
        margin-top: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: transparent transparent transparent black;
    }

    */

</style>