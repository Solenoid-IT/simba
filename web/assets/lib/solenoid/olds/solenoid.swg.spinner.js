// © Solenoid Team



if ( typeof Solenoid === 'undefined' ) Solenoid = {};

if ( typeof Solenoid.SWG === 'undefined' ) Solenoid.SWG = {};



Solenoid.SWG.Spinner = function (settings)
{
    const private = {};
    const public  = this;



    // Returns [self]
    private.__construct = function (settings)
    {
        if ( settings.element.swg ) return settings.element.swg;



        // (Setting the value)
        private.eventCallbacks = {};

        // (Getting the values)
        public.element              = settings.element;
        public.messageElement       = public.element.querySelector('.spinner-message');
        public.countdownElement     = public.element.querySelector('.spinner-countdown');
        public.progressElement      = public.element.querySelector('.spinner-progress');
        public.progressValueElement = public.element.querySelector('.spinner-progress-value');



        // (Getting the value)
        public.element.swg = public;
    }



    // Returns [void]
    public.addEventListener = function (type, callback)
    {
        if ( typeof private.eventCallbacks[ type ] === 'undefined' ) private.eventCallbacks[ type ] = [];



        // Appending the value
        private.eventCallbacks[ type ].push( callback );
    }

    // Returns [void]
    public.triggerEvent = function (type, data)
    {
        if ( typeof private.eventCallbacks[ type ] === 'undefined' ) return;



        for (const callback of private.eventCallbacks[ type ])
        {// Processing each entry
            // (Calling the function)
            callback( data );
        }
    }



    // Returns [object]
    public.parseSeconds = function (seconds)
    {
        // (Getting the value)
        parsed =
        {
            'D': Math.floor( seconds / (24 * 60 * 60) ),

            'H': Math.floor( (seconds % (24 * 60 * 60) ) / (60 * 60) ),
            'M': Math.floor( (seconds % (60 * 60)) / (60) ),
            'S': Math.floor( seconds % 60 )
        }
        ;



        // Returning the value
        return parsed;
    }

    // Returns [string]
    public.displaySeconds = function (seconds)
    {
        // (Getting the value)
        const parsed = public.parseSeconds( seconds );



        // (Setting the value)
        let display = [];

        for (let k in parsed)
        {// Processing each entry
            // (Getting the value)
            const v = parsed[ k ];

            if ( v === 0 ) continue;



            // (Appending the value)
            display.push
            (
                `<b>${ v }</b> ${ k }`
            )
            ;
        }

        // (Getting the value)
        display = display.join( ' ' );



        // Returning the value
        return display;
    }



    // Returns [void]
    public.start = function (message, seconds)
    {
        if ( typeof message === 'undefined' ) message = null;
        if ( typeof seconds === 'undefined' ) seconds = null;

        if ( seconds < 0 ) seconds = 0;



        // (Triggering the event)
        document.activeElement.blur();



        if ( message )
        {// Value found
            // (Setting the html content)
            public.messageElement.innerHTML = message;

            // (Removing the attribute)
            public.messageElement.removeAttribute( 'hidden' );
        }



        // (Removing the attribute)
        public.element.removeAttribute( 'hidden' );

        // (Setting the attribute)
        public.countdownElement.setAttribute( 'hidden', true );



        if ( seconds )
        {// Value found
            // (Getting the value)
            let s = seconds;

            // (Getting the value)
            private.interval = setInterval
            (
                function ()
                {
                    if ( s === 0 )
                    {// (Countdown is terminated)
                        // (Stopping the countdown)
                        public.stop();
                    }



                    // (Getting the value)
                    s -= 1;



                    // (Setting the html content)
                    public.countdownElement.innerHTML = public.displaySeconds( s );

                    // (Setting the style)
                    public.progressValueElement.style.width = ( ( seconds - s ) / seconds * 100 ) + '%';



                    // (Removing the attributes)
                    public.countdownElement.removeAttribute( 'hidden' );
                    public.progressElement.removeAttribute( 'hidden' );
                },
                1000
            )
            ;
        }
    }

    // Returns [void]
    public.stop = function ()
    {
        // (Clearing the interval)
        clearInterval( private.interval );



        // (Setting the attributes)
        public.element.setAttribute( 'hidden', true );

        public.messageElement.setAttribute( 'hidden', true );
        public.countdownElement.setAttribute( 'hidden', true );
        public.progressElement.setAttribute( 'hidden', true );



        // (Setting the style)
        public.progressValueElement.style.width = '0%';
    }



    private.__construct( settings );
}
;



// (Listening for the event)
window.addEventListener('load', function () {
    // (Appending the value)
    const styleElement = document.createElement('style');

    // (Setting the html content)
    styleElement.innerHTML =
        `
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
        `
    ;



    // (Appending the element)
    document.head.appendChild( styleElement );
});