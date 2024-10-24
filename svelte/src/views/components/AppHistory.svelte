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



    export let id      = null;
    export let api     = null;

    export let history = null;
    export let display = 'full';



    let tabOpen = false;



    // (Getting the value)
    api = self;

</script>

{ #if api }
    { #if pvt.visible }
        <div class="swg swg-app-history" id="{ id }" bind:this={ self.element }>
            { #if display === 'full' }
                <!-- svelte-ignore a11y-click-events-have-key-events -->
                <div class="toggle-button" on:click={ () => { tabOpen = !tabOpen; } }>
                    History
                    <div>
                        { #if tabOpen }
                            <i class="fa-solid fa-caret-up"></i>
                        { :else }
                            <i class="fa-solid fa-caret-down"></i>
                        { /if }
                    </div>
                </div>
            { /if }

            <ul class="main-ul">
                { #if history && ( tabOpen || display === 'last' ) }
                    { #each Object.entries(history) as [ versionId, version ], i }
                        { #if display === 'full' || display === 'last' && i === 0 }
                            <li>
                                <div>
                                    <!-- svelte-ignore a11y-missing-attribute -->
                                    <a><b>{ versionId }</b></a> -- { new Date( version.buildTime * 1000 ).toISOString() }
                                </div>

                                <div class="p-2">
                                    <!-- svelte-ignore a11y-missing-attribute -->
                                    <a>Fixed Bugs</a>
                                    <ul>
                                        { #each version.bugfixes as bugfix }
                                            <li>
                                                { @html bugfix }
                                            </li>
                                        { /each }
                                    </ul>
                                </div>

                                <div class="p-2">
                                    <!-- svelte-ignore a11y-missing-attribute -->
                                    <a>Improved Features</a>
                                    <ul>
                                        { #each version.features.improved as improvedFeature }
                                            <li>
                                                { @html improvedFeature }
                                            </li>
                                        { /each }
                                    </ul>
                                </div>

                                <div class="p-2">
                                    <!-- svelte-ignore a11y-missing-attribute -->
                                    <a>Added Features</a>
                                    <ul>
                                        { #each version.features.added as addedFeature }
                                            <li>
                                                { @html addedFeature }
                                            </li>
                                        { /each }
                                    </ul>
                                </div>
                            </li>
                        { /if }
                    { /each }
                { /if }
            </ul>
        </div>
    { /if }
{ /if }

<style>

    .swg.swg-app-history
    {
        font-size: 12px;
    }

    .toggle-button
    {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        font-weight: bolder;
        cursor: pointer;
    }

    .main-ul
    {
        margin: 20px 0;
    }

</style>