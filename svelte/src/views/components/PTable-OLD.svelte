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
    export let api   = null;

    export let props = [];



    // (Getting the value)
    api = self;

</script>

{ #if api }
    { #if pvt.visible }
        <div class="swg swg-p-table" id="{ id }" bind:this={ self.element }>
            <table>
                <tbody>
                    { #each props as prop }
                        <tr data-id="{ prop.label.id }">
                            <th>
                                { @html prop.label.value }
                            </th>
                            <td>
                                { @html prop.content }
                            </td>
                        </tr>
                    { /each }
                </tbody>
            </table>
        </div>
    { /if }
{ /if }

<style>

    .swg.swg-p-table table
    {
        width: 100%;
    }

    .swg.swg-p-table th
    {
        text-align: left;
    }

    .swg.swg-p-table td
    {
        text-align: right;
    }

    .swg.swg-p-table th,
    .swg.swg-p-table td
    {
        font-size: 12px;
    }

</style>