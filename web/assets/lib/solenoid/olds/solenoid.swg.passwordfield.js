// © Solenoid Team



if ( typeof Solenoid === 'undefined' ) Solenoid = {};

if ( typeof Solenoid.SWG === 'undefined' ) Solenoid.SWG = {};



Solenoid.SWG.PasswordField = function (settings)
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
        public.element               = settings.element;
        public.strengthMeter         = typeof settings.strengthMeter === 'undefined' ? null : settings.strengthMeter;
        public.generator             = typeof settings.generator === 'undefined' ? null : settings.generator;

        public.inputElement          = public.element.closest('.swg').querySelector('.input');
        public.generateButtonElement = public.element.querySelector('.passwordfield-button[value="generate"]');
        public.toggleButtonElement   = public.element.querySelector('.passwordfield-button[value="toggle"]');



        // (Setting the value)
        public.thresholds =
        [
            {
                'color': '#ad3a00',
                'description': 'Bad'
            }
            ,
            {
                'color': '#ad7400',
                'description': 'Very Weak'
            }
            ,
            {
                'color': '#adad00',
                'description': 'Weak'
            }
            ,
            {
                'color': '#74ad00',
                'description': 'Good'
            }
            ,
            {
                'color': '#3aad00',
                'description': 'Strong'
            }
        ]
        ;



        if ( public.strengthMeter )
        {// Value found
            if ( typeof public.strengthMeter.entropy === 'undefined' ) public.strengthMeter.entropy = 128;



            // (Listening for the event)
            public.inputElement.addEventListener('input', function () {
                // (Getting the values)
                const rank         = public.rank( this.value );

                let thresholdIndex = Math.floor( ( rank * public.thresholds.length ) / 100 );
                thresholdIndex     = thresholdIndex === public.thresholds.length ? public.thresholds.length - 1 : thresholdIndex;

                const threshold    = public.thresholds[ thresholdIndex ];



                // (Getting the element)
                const progressValueElement = public.strengthMeter.element.querySelector('.progress-value');

                // (Setting the html content)
                progressValueElement.innerHTML = Math.floor( rank ) + ' %';

                // (Setting the style)
                progressValueElement.style.width           = rank + '%';
                progressValueElement.style.backgroundColor = threshold['color'];



                // (Getting the element)
                const progressDescriptionElement = public.strengthMeter.element.querySelector('.progress-description');

                // (Setting the html content)
                progressDescriptionElement.innerHTML = `${ threshold['description'] } ( ${ Math.floor( public.absRank( this.value ) ) } bits )`;
            });
        }



        if ( public.generator )
        {// Value found
            if ( typeof public.generator.length === 'undefined' ) public.generator.length = 32;
            if ( typeof public.generator.minEntropy === 'undefined' ) public.generator.minEntropy = 128;

            if ( public.generateButtonElement )
            {// Value found
                // (Listening for the events)
                public.generateButtonElement.addEventListener('click', function () {
                    // (Setting the value)
                    public.inputElement.value = public.generate( public.generator.length, public.generator.minEntropy );

                    // (Triggering the event)
                    public.inputElement.dispatchEvent( new Event('input') );
                });
            }
        }

        if ( public.toggleButtonElement )
        {// Value found
            // (Listening for the events)
            public.toggleButtonElement.addEventListener('click', function () {
                // (Iterating each entry)
                public.toggleButtonElement.querySelectorAll('.passwordfield-button-state').forEach
                (
                    function (el)
                    {
                        // (Removing the attribute)
                        el.setAttribute('hidden', true);
                    }
                )
                ;



                // (Getting the value)
                const currentType = public.inputElement.getAttribute('type');



                // (Setting the attribute)
                public.toggleButtonElement.querySelector(`.passwordfield-button-state[data-value="${ currentType }"]`).removeAttribute('hidden');



                // (Setting the value)
                let newType = null;

                switch ( currentType )
                {
                    case 'text':
                        // (Setting the type)
                        newType = 'password';

                        // (Setting the attribute)
                        this.setAttribute('title', 'show');
                    break;

                    case 'password':
                        // (Setting the type)
                        newType = 'text';

                        // (Setting the attribute)
                        this.setAttribute('title', 'hide');
                    break;
                }



                // (Setting the attribute)
                public.inputElement.setAttribute('type', newType);
            });
        }



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



    // Returns [number]
    public.absRank = function (password)
    {
        // (Setting the value)
        let numPossibleCharacters = 0;



        if ( /[a-z]/.test( password ) )
        {// (Lowercase Character)
            // (Incrementing the value)
            numPossibleCharacters += 26;
        }

        if ( /[A-Z]/.test( password ) )
        {// (Uppercase Character)
            // (Incrementing the value)
            numPossibleCharacters += 26;
        }

        if ( /[0-9]/.test( password ) )
        {// (Number)
            // (Incrementing the value)
            numPossibleCharacters += 10;
        }

        if ( /[^a-zA-Z0-9]/.test( password ) )
        {// (Symbol)
            // (Incrementing the value)
            numPossibleCharacters += 33;
        }



        // (Getting the value)
        const entropy = Math.log2( Math.pow( numPossibleCharacters, password.length ) );



        // Returning the value
        return entropy;
    }

    // Returns [number]
    public.rank = function (password)
    {
        // (Getting the value)
        let absRank = public.absRank( password );

        if ( absRank > public.strengthMeter.entropy ) absRank = public.strengthMeter.entropy;



        // Returning the value
        return ( absRank / public.strengthMeter.entropy ) * 100;
    }



    // Returns [number]
    public.random = function (min, max)
    {
        // Returning the value
        return Math.floor( Math.random() * ( max - min + 1 ) ) + min;
    }

    // Returns [string]
    public.generate = function (length, minEntropy)
    {
        if ( typeof length === 'undefined' ) length = 32;
        if ( typeof minEntropy === 'undefined' ) minEntropy = 128;



        // (Setting the value)
        const characters = '!#$%&*+,-./0123456789:;@ABCDEFGHIJKLMNOPQRSTUVWXYZ[]^_abcdefghijklmnopqrstuvwxyz{|}~';



        // (Setting the value)
        let password = null;



        while ( true )
        {// Processing each clock
            // (Setting the value)
            password = '';

            for (let i = 0; i < length; i++)
            {// Iterating each index
                // (Appending the value)
                password += characters.at( public.random( 0, characters.length - 1 ) );
            }



            if ( public.absRank( password ) >= minEntropy ) break;
        }



        // Returning the value
        return password;
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
            .swg.swg-passwordfield
            {
                
            }



            .swg.swg-passwordfield-strengthmeter
            {

            }

            .swg.swg-passwordfield-strengthmeter .progress-value
            {
                font-size: 10px;
                font-weight: 400;
                color: #ffffff;
                border-radius: 4px;

                transition: .2s all ease-in-out;
            }

            .swg.swg-passwordfield-strengthmeter .progress-description
            {
                font-size: 10px;
                font-weight: 700;
            }
        `
    ;



    // (Appending the element)
    document.head.appendChild( styleElement );
});