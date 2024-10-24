<script>

    // (Setting the values)
    let element = null;
    let value   = '';
    let visible = false;



    export let name;
    export let placeholder;
    export let required;



    export let generator =
    {
        'length':     32,
        'minEntropy': 128
    }
    ;

    export let strengthMeter =
    {
        'thresholds':
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
        ],

        'entropy': 128
    }
    ;



    export let generable  = false;
    export let measurable = false;



    export let currentMeter =
    {
        'rank':                0,
        'threshold':
        {
            'color':           'transparent',
            'description':     ''
        },
        'progressDescription': '',
        'visible':             false
    }
    ;



    // Returns [void]
    function showPassword ()
    {
        // (Setting the property)
        element.querySelector('input').type = 'text';

        // (Setting the value)
        visible = true;
    }

    // Returns [void]
    function hidePassword ()
    {
        // (Setting the property)
        element.querySelector('input').type = 'password';

        // (Setting the value)
        visible = false;
    }



    // Returns [number]
    function random (min, max)
    {
        // Returning the value
        return Math.floor( Math.random() * ( max - min + 1 ) ) + min;
    }



    // Returns [number]
    function calcAbsRank (password)
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
    function calcRank (password)
    {
        // (Getting the value)
        let absRank = calcAbsRank( password );

        if ( absRank > strengthMeter.entropy ) absRank = strengthMeter.entropy;



        // Returning the value
        return ( absRank / strengthMeter.entropy ) * 100;
    }



    // Returns [string]
    function generatePassword (length, minEntropy)
    {
        if ( typeof length === 'undefined' ) length = generator.length;
        if ( typeof minEntropy === 'undefined' ) minEntropy = generator.minEntropy;



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
                password += characters.at( random( 0, characters.length - 1 ) );
            }



            if ( calcAbsRank( password ) >= minEntropy ) break;
        }



        // Returning the value
        return password;
    }



    // Returns [void]
    function togglePasswordVisibility ()
    {
        if ( visible )
        {// Match OK
            // (Hiding the password)
            hidePassword();
        }
        else
        {// Match failed
            // (Showing the password)
            showPassword();
        }
    }



    // Returns [void]
    function drawMeter ()
    {
        if ( value.length === 0 )
        {// Value is empty
            // (Setting the value)
            currentMeter.visible = false;
        }
        else
        {// Value is not empty
            // (Getting the values)
            currentMeter.rank = calcRank( value );

            let thresholdIndex = Math.floor( ( currentMeter.rank * strengthMeter.thresholds.length ) / 100 );
            thresholdIndex     = thresholdIndex === strengthMeter.thresholds.length ? strengthMeter.thresholds.length - 1 : thresholdIndex;

            currentMeter.threshold           = strengthMeter.thresholds[ thresholdIndex ];
            currentMeter.progressDescription = `${ currentMeter.threshold.description } ( ${ Math.floor( calcAbsRank( value ) ) } bits )`;



            // (Setting the value)
            currentMeter.visible = true;
        }
    }



    $:
        if ( value.length === 0 )
        {// Value is empty
            // (Setting the value)
            currentMeter.visible = false;
        }
    
    $:
        if ( value )
        {// Value found
            // (Drawing the meter)
            drawMeter();
        }

</script>

<div class="passwordfield">
    <div class="input-group" bind:this={ element }>
        <input type="password" class="form-control input" name="{ name }" placeholder="{ placeholder }" data-required={ required } bind:value={ value } on:input={ drawMeter }>
        <div class="input-group-append">
            { #if generable }
                <button class="btn btn-secondary p-0" type="button" style="width: 40px;" title="generate" on:click={ () => { value = generatePassword(); } }>
                    <i class="fas fa-fw fa-dice"></i>
                </button>
            { /if }

            <button class="btn btn-secondary p-0" type="button" style="width: 40px;" title="{ visible ? 'hide' : 'show' }" on:click={ togglePasswordVisibility }>
                { #if !visible }
                    <i class="fas fa-fw fa-eye"></i>
                { /if }

                { #if visible }
                    <i class="fas fa-fw fa-eye-slash"></i>
                { /if }
            </button>
        </div>
    </div>

    { #if measurable }
        <div class="passwordfield-strengthmeter">
            { #if currentMeter.visible }
                <div class="progress-bar">
                    <div class="progress-value" style="width: { currentMeter.rank + '%' }; background-color: { currentMeter.threshold.color };">{ Math.floor( currentMeter.rank ) + ' %' }</div>
                </div>
                <div class="progress-description">{ currentMeter.progressDescription }</div>
            { /if }
        </div>
    { /if }
</div>

<style>

    .passwordfield
    {
        flex-grow: 1;
    }

    .passwordfield-strengthmeter
    {
        margin-top: 10px;
    }

    .passwordfield-strengthmeter .progress-bar
    {
        background-color: transparent;
        border-radius: 4px;
    }

    .passwordfield-strengthmeter .progress-value
    {
        font-size: 10px;
        font-weight: 400;
        color: #ffffff;
        border-radius: 4px;

        transition: .2s all ease-in-out;
    }

    .passwordfield-strengthmeter .progress-description
    {
        font-size: 10px;
        font-weight: 700;
    }

</style>