<script>

    export let id   = null;
    export let api  = null;

    export let data = {};



    let element;



    // (Setting the value)
    api = {};

</script>

<div class="swg swg-p-table" id="{ id }" bind:this={ element }>
    <table>
        <tbody>
            { #if Object.keys( data ).length === 0 }
                NO DATA
            { :else}
                { #each Object.entries( data ) as entry }
                    <tr>
                        <th>
                            { @html entry[0] }
                        </th>
                        <td>
                            { @html entry[1] }
                        </td>
                    </tr>
                { /each }
            { /if }
        </tbody>
    </table>
</div>

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