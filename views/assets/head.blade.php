<?php

    use \Solenoid\Core\App\WebApp;



    // (Getting the value)
    $app = WebApp::fetch();

?>



<!-- DataTable -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css">

<!-- Template styles -->
<link rel="stylesheet" href="/assets/tpl/sb-admin/dist/css/styles.css">

<!-- Custom styles -->
<link rel="stylesheet" href="{{ $app->asset('/assets/styles/custom.css') }}">



<!-- FontAwesome -->
<script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

<!-- Luxon -->
<script src="https://www.solenoid.it/cdn/lib/js/luxon/luxon.min.js"></script>



<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>



<!-- Solenoid/HTTP -->
<script src="https://www.solenoid.it/cdn/lib/js/solenoid/solenoid.http.js"></script>

<!-- Solenoid/SSE -->
<script src="https://www.solenoid.it/cdn/lib/js/solenoid/solenoid.sse.js"></script>

<!-- Solenoid/URL -->
<script src="https://www.solenoid.it/cdn/lib/js/solenoid/solenoid.url.js"></script>

<!-- Solenoid/File -->
<script src="https://www.solenoid.it/cdn/lib/js/solenoid/solenoid.file.js"></script>

<!-- Solenoid/LocalStorage -->
<script src="https://www.solenoid.it/cdn/lib/js/solenoid/solenoid.local_storage.js"></script>

<!-- Solenoid/DateTime -->
<script src="https://www.solenoid.it/cdn/lib/js/solenoid/solenoid.datetime.js"></script>



<!-- SWG -->
<script src="/assets/lib/solenoid/solenoid.swg.table.js"></script>
<script src="/assets/lib/solenoid/solenoid.swg.form.js"></script>
<script src="/assets/lib/solenoid/solenoid.swg.passwordfield.js"></script>
<script src="/assets/lib/solenoid/solenoid.swg.spinner.js"></script>






<script name="set_locales">

    // (Setting the cookies)
    document.cookie = `timezone=${ encodeURI( Intl.DateTimeFormat().resolvedOptions().timeZone ) };Path=/`;
    document.cookie = `language=${ encodeURI( navigator.language ) }; Path=/`;

</script>



<script name="page-loader">

    // (Listening for the event)
    $(document).ready(function () {
        // (Setting the timeout)
        setTimeout
        (
            function ()
            {
                // (Setting the attribute)
                $('#page-loader').attr('hidden', true);
            },
            500
        )
        ;



        // (Listening for the events)
        $(window).on('dragover', function (event) {
            // (Preventing the default)
            event.preventDefault();
        });

        $(window).on('drop', async function (event) {
            // (Preventing the default)
            event.preventDefault();

            if ( typeof event.originalEvent.dataTransfer.files === 'undefined' ) return;



            // (Getting the value)
            const file = event.originalEvent.dataTransfer.files[0];

            if ( file.name.split('.').at(-1) !== 'idk' ) return;



            // (Saving the IDK)
            idk = Solenoid.LocalStorage.save( 'idk', await Solenoid.File.read( file ) );

            // (Setting the location)
            window.location.href = '';
        });
    });

</script>