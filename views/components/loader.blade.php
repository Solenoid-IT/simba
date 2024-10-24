<div class="loader" id="loader" hidden>
    <div class="loader-bar"></div>
    <div class="loader-blackscreen"></div>
</div>



<script name="loader">

    // (Setting the value)
    let currentActiveElement = null;



    // (Listening for the events)
    Solenoid.HTTP.addEventListener('start', function () {
        // (Getting the value)
        currentActiveElement = document.activeElement;

        // (Getting the value)
        currentActiveElement.blur();



        // (Setting the attribute)
        $('#loader').attr('hidden', false);

        // (Setting the class)
        $('#loader .loader-blackscreen').removeClass('show');



        // (Setting the timeout)
        setTimeout
        (
            function ()
            {
                // (Setting the class)
                $('#loader .loader-blackscreen').addClass('show');
            },
            2000
        )
        ;
    });

    Solenoid.HTTP.addEventListener('end', function () {
        // (Getting the value)
        currentActiveElement.focus();



        // (Setting the attribute)
        $('#loader').attr('hidden', true);

        // (Setting the class)
        $('#loader .loader-blackscreen').removeClass('show');
    });

</script>