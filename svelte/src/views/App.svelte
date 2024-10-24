<script>

    import { onMount } from 'svelte';

    import { appReady } from '../stores/appReady.js';

    import ActivityBar from './components/ActivityBar.svelte';

    import '../styles.css';

    import { envs } from '../envs.js';

    import { idk } from '../stores/idk.js';



    let activityBarVisible = false;



    // (Listening for the event)
    onMount
    (
        function ()
        {
            if ( $appReady ) return;



            // (Listening for the event)
            Solenoid.HTTP.addEventListener('start', function (event) {
                // (Setting the value)
                activityBarVisible = true;
            });

            // (Listening for the event)
            Solenoid.HTTP.addEventListener('end', function (event) {
                // (Setting the value)
                activityBarVisible = false;
            });



            // (Setting the value)
            Solenoid.HTTP.debug = 'RPC';



            // (Logging the value)
            console.log( `APP ${ envs.APP_ID } :: ${ envs.APP_NAME }/${ envs.APP_VERSION } -> READY` );



            // (Setting the value)
            $appReady = true;



            // (Getting the value)
            $idk = localStorage.getItem( 'idk' );
        }
    )
    ;

</script>

{ #if activityBarVisible }
    <ActivityBar/>
{ /if}

<slot/>