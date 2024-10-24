<script>

    import { createEventDispatcher } from 'svelte';

    const dispatch = createEventDispatcher();



    // (Setting the values)
    const pvt = {};
    const pub = {};



    // (Setting the values)
    pvt.message                = '';
    pvt.duration               = 0;
    pvt.progressValue          = 0;
    pvt.interval               = null;
    pvt.visible                = false;
    pvt.eventListener          = {};



    // Returns [object]
    pub.parseDuration = function (duration)
    {
        // (Getting the value)
        const parsed =
        {
            'D': Math.floor( duration / (24 * 60 * 60) ),

            'H': Math.floor( (duration % (24 * 60 * 60) ) / (60 * 60) ),
            'M': Math.floor( (duration % (60 * 60)) / (60) ),
            'S': Math.floor( duration % 60 )
        }
        ;



        // Returning the value
        return parsed;
    }

    // Returns [string]
    pub.displayDuration = function (duration)
    {
        // (Getting the value)
        const parsed = pub.parseDuration( duration );



        // (Setting the value)
        let display = [];

        for (let k in parsed)
        {// Processing each entry
            // (Getting the value)
            const v = parsed[ k ];

            if ( v === 0 ) continue;



            // (Appending the value)
            display.push( `<b>${ v }</b> ${ k }` );
        }



        // (Getting the value)
        display = display.join( ' ' );

        if ( display === '' ) display = '0';



        // Returning the value
        return display;
    }



    // Returns [void]
    pub.stop = function ()
    {
        // (Clearing the interval)
        clearInterval( pvt.interval );



        // (Setting the timeout)
        setTimeout
        (
            function ()
            {
                // (Setting the values)
                pvt.message       = '';
                pvt.progressValue = 0;
                pvt.visible       = false;
            },
            1000
        )
        ;
    }

    // Returns [void]
    pub.start = function (message, duration)
    {
        if ( typeof message === 'undefined' ) message = null;
        if ( typeof duration === 'undefined' ) duration = null;

        if ( duration < 0 ) duration = 0;



        // (Getting the values)
        pvt.message  = message;
        pvt.duration = duration;



        // (Triggering the event)
        document.activeElement.blur();



        // (Setting the value)
        pvt.visible = true;



        if ( duration )
        {// Value found
            // (Getting the value)
            let s = duration;

            // (Setting the interval)
            pvt.interval = setInterval
            (
                function ()
                {
                    if ( s === 0 )
                    {// (Countdown is terminated)
                        // (Stopping the spinner)
                        pub.stop();

                        // (Triggering the event)
                        dispatch('stop');



                        // Returning the value
                        return;
                    }



                    // (Decrementing the value)
                    s -= 1;



                    // (Getting the values)
                    pvt.duration      = s;
                    pvt.progressValue = ( ( duration - s ) / duration * 100 );
                },
                1000
            )
            ;
        }
    }



    // Returns [void]
    pub.addEventListener = function (type, callback)
    {
        if ( typeof pvt.eventListener[ type ] === 'undefined' ) pvt.eventListener[ type ] = [];

        // (Appending the value)
        pvt.eventListener[ type ].push( callback );
    }

    // Returns [void]
    pub.triggerEvent = function (type, data)
    {
        for (const callback of pvt.eventListener[ type ])
        {// Processing each entry
            // (Calling the function)
            pvt.eventListener[ type ]( data );
        }
    }



    export const api = pub;



    // Returns [void]
    function onKeyUp (event)
    {
        if ( event.key === 'Escape' ) pub.stop();

        // (Triggering the event)
        dispatch('stop');
    }

</script>

<svelte:window on:keyup={ onKeyUp }/>

{ #if pvt.visible }
    <div class="swg swg-spinner">
        <div class="spinner-box">
            <div class="spinner-message">{ pvt.message }</div>
            <div class="spinner-symbol">
                <div class="loadingio-spinner-dual-ball-i4rbxz429u">
                    <div class="ldio-ludptqkr5l">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>

                <style type="text/css">
                    @keyframes ldio-ludptqkr5l-o
                    {
                        0%    { opacity: 1; transform: translate(0 0) }
                        49.99% { opacity: 1; transform: translate(77.6px,0) }
                        50%    { opacity: 0; transform: translate(77.6px,0) }
                        100%    { opacity: 0; transform: translate(0,0) }
                    }

                    @keyframes ldio-ludptqkr5l
                    {
                        0% { transform: translate(0,0) }
                        50% { transform: translate(77.6px,0) }
                        100% { transform: translate(0,0) }
                    }

                    .ldio-ludptqkr5l div
                    {
                        position: absolute;
                        width: 77.6px;
                        height: 77.6px;
                        border-radius: 50%;
                        top: 58.199999999999996px;
                        left: 19.4px;
                    }

                    .ldio-ludptqkr5l div:nth-child(1)
                    {
                        background: #018382;
                        animation: ldio-ludptqkr5l 1.923076923076923s linear infinite;
                        animation-delay: -0.9615384615384615s;
                    }

                    .ldio-ludptqkr5l div:nth-child(2)
                    {
                        background: #bfc1c1;
                        animation: ldio-ludptqkr5l 1.923076923076923s linear infinite;
                        animation-delay: 0s;
                    }

                    .ldio-ludptqkr5l div:nth-child(3)
                    {
                        background: #018382;
                        animation: ldio-ludptqkr5l-o 1.923076923076923s linear infinite;
                        animation-delay: -0.9615384615384615s;
                    }

                    .loadingio-spinner-dual-ball-i4rbxz429u
                    {
                        width: 194px;
                        height: 194px;
                        display: inline-block;
                        overflow: hidden;
                        /*background: #f1f2f3;*/
                        background: transparent;
                    }

                    .ldio-ludptqkr5l
                    {
                        width: 100%;
                        height: 100%;
                        position: relative;
                        transform: translateZ(0) scale(1);
                        backface-visibility: hidden;
                        transform-origin: 0 0; /* see note above */
                    }

                    .ldio-ludptqkr5l div { box-sizing: content-box; }
                    /* generated by https://loading.io/ */
                </style>
            </div>
            <div class="spinner-countdown">{ @html pub.displayDuration( pvt.duration ) }</div>
            <div class="spinner-progress">
                <div class="spinner-progress-value" style="width: { pvt.progressValue }%;"></div>
            </div>
        </div>
        <div class="spinner-blackscreen"></div>
    </div>
{ /if }

<style>

    .swg.swg-spinner
    {
        position: fixed;
        z-index: 999999;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }

    .swg.swg-spinner .spinner-box
    {
        min-width: 400px;
        margin: 0 auto;
        padding: 20px;
        position: relative;
        z-index: 1;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        display: block;
        background-color: #959595;
        border-radius: 4px;
        box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
    }

    .swg.swg-spinner .spinner-message
    {
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        background-color: #cfcfcf;
        color: #000000;
        border-radius: 4px;
        text-align: center;
    }

    .swg.swg-spinner .spinner-symbol
    {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
    }

    .swg.swg-spinner .spinner-countdown
    {
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        background-color: #cfcfcf;
        color: #000000;
        border-radius: 4px;
        text-align: center;
    }

    .swg.swg-spinner .spinner-progress
    {
        width: 100%;
        height: 10px;
        margin-top: 10px;
        font-size: 12px;
        font-weight: 600;
        background-color: #bfc1c1;
        color: #000000;
        border-radius: 2px;
        text-align: center;
    }

    .swg.swg-spinner .spinner-progress-value
    {
        width: 0%;
        height: 100%;
        font-size: 12px;
        font-weight: 600;
        background-color: #018382;
        color: #000000;
        border-radius: 2px;
        text-align: center;

        transition: .2s all ease-in-out;
    }

    .swg.swg-spinner .spinner-blackscreen
    {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background-color: #00000099;
    }

</style>