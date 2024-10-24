<script>

    import { createEventDispatcher } from 'svelte';



    const dispatch = createEventDispatcher();



    export let id;
    export let name;

    let element;



    // Returns [void]
    function onInputChange (event)
    {
        // (Triggering the event)
        dispatch( 'change', event );
    }



    // Returns [void]
    function onCheckedChange (value)
    {
        // (Getting the property)
        element.querySelector('.input').checked = value;
    }

    // Returns [void]
    function onDisabledChange (value)
    {
        // (Getting the property)
        element.querySelector('.input').disabled = value;
    }



    export let api;

    $:
        if ( element )
        {// Value found
            // (Setting the value)
            api = {};

            // (Setting the values)
            api.checked  = false;
            api.disabled = false;
        }

    $:
        if ( element )
        {// Value found
            // (Calling the functions)
            onCheckedChange( api.checked );
            onDisabledChange( api.disabled );
        }

</script>

<div class="switch { $$restProps.class }" id={ id } bind:this={ element }>
    <label class="toggle">
        <input type="checkbox" class="toggle__input input" name="{ name }" on:change={ (event) => { onInputChange(event); } }>

        <span class="toggle-track">
            <span class="toggle-indicator">
                <!-- 	This check mark is optional	 -->
                <span class="checkMark">
                    <svg viewBox="0 0 24 24" role="presentation" aria-hidden="true">
                        <path d="M9.86 18a1 1 0 01-.73-.32l-4.86-5.17a1.001 1.001 0 011.46-1.37l4.12 4.39 8.41-9.2a1 1 0 111.48 1.34l-9.14 10a1 1 0 01-.73.33h-.01z"></path>
                    </svg>
                </span>
            </span>
        </span>

        <slot/>
    </label>
</div>

<style>

    .toggle
    {
        align-items: center;
        border-radius: 100px;
        display: flex;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .toggle:last-of-type
    {
        margin: 0;
    }

    .toggle__input
    {
        clip: rect(0 0 0 0);
        clip-path: inset(50%);
        height: 1px;
        overflow: hidden;
        position: absolute;
        white-space: nowrap;
        width: 1px;
    }
    .toggle__input:not([disabled]):active + .toggle-track, .toggle__input:not([disabled]):focus + .toggle-track
    {
        border: 1px solid transparent;
        box-shadow: 0px 0px 0px 2px #6c757d;
    }
    .toggle__input:disabled + .toggle-track
    {
        cursor: not-allowed;
        opacity: 0.7;
    }

    .toggle-track
    {
        width: 60px;
        height: 30px;
        margin-right: 20px;
        position: relative;
        display: flex;
        background-color: transparent;
        border: 1px solid #6c757d;
        border-radius: 100px;
        cursor: pointer;
    }

    .toggle-indicator
    {
        align-items: center;
        background-color: #6c757d;
        border-radius: 24px;
        bottom: 2px;
        display: flex;
        height: 24px;
        justify-content: center;
        left: 2px;
        outline: solid 2px transparent;
        position: absolute;
        transition: 0.25s;
        width: 24px;
    }

    .checkMark
    {
        width: 20px;
        height: 20px;
        margin-top: -6px;
        fill: #fff;
        opacity: 0;
        transition: opacity 0.25s ease-in-out;
    }

    .toggle__input:checked + .toggle-track .toggle-indicator
    {
        background-color: var( --simba-primary );
        transform: translateX(30px);
    }
    .toggle__input:checked + .toggle-track .toggle-indicator .checkMark
    {
        opacity: 1;
        transition: opacity 0.25s ease-in-out;
    }



    @media screen and (-ms-high-contrast: active)
    {
        .toggle-track
        {
            border-radius: 0;
        }
    }

</style>