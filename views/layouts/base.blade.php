@php



use \Solenoid\MySQL\Condition;

use \App\Stores\Sessions\Store as SessionsStore;
use \App\Models\User as UserModel;



// (Getting the value)
$session = SessionsStore::fetch()->sessions['user'];

// (Getting the value)
$user = UserModel::fetch()->find( ( new Condition() )->filter( [ [ 'id' => $session->data['user'] ] ] ) );



@endphp



<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @include ('assets/head.blade.php')

        @yield ('view.head')



        <style>

            #layoutSidenav
            {
                position: relative;
                top: 56px;
            }

            #sidenavAccordion .nav
            {
                top: 56px;
            }

        </style>
    </head>

    <body>
        @include ( 'components/page-loader.blade.php' )

        @include ( 'components/loader.blade.php' )



        @include ( 'components/header.blade.php', [ 'session' => $session, 'user' => $user ] )

        <div id="layoutSidenav">
            @include ( 'components/sidebar.blade.php', [ 'user' => $user ] )

            <div id="layoutSidenav_content">
                <main class="page-main">
                    <div class="container-fluid px-4">
                        @yield ( 'view.body' )
                    </div>
                </main>

                @include ( 'components/footer.blade.php' )
            </div>
        </div>



        @include( 'components/spinner.blade.php' )



        @include ( 'assets/body.blade.php' )

        @yield ( 'view.script' )



        <script name="session_validator">

            // (Setting the interval)
            setInterval
            (
                async function ()
                {
                    // (Sending an http request)
                    const response = await Solenoid.HTTP.sendRequest
                    (
                        '/user',
                        'RPC',
                        {
                            'Action': 'session::validate'
                        }
                    )
                    ;

                    if ( response.status.code === 401 )
                    {// (Session is not valid)
                        // (Setting the location)
                        window.location.href = '/admin/login';
                    }
                },
                5 * 60 * 1000
            )
            ;

        </script>

        <script name="modal">

            // (Click-Event on the element)
            $('a[data-toggle="modal"]').on('click', function () {
                // (Showing the modal)
                $( $(this).attr('data-target') ).modal('show');
            });

            // (Click-Event on the element)
            $('.close').on('click', function () {
                // (Hiding the modal)
                $(this).closest('.modal').modal('hide');
            });

        </script>

        <script name="presence">

            Solenoid.HumanPresenceDetector = function (settings)
            {
                const private = {};
                const public  = this;



                // Returns [self]
                private.__construct = function (settings)
                {
                    // (Setting the value)
                    const events =
                    [
                        'click',
                        'dblclick',
                        'contextmenu',
                        'keydown',
                        'keyup',
                        'mousedown',
                        'mouseup',
                        'mousemove',
                        'wheel'
                    ]
                    ;



                    // (Setting the values)
                    private.eventCallbacks = {};
                    private.lastPresence   = null;



                    // (Getting the value)
                    private.waitTime = typeof settings.waitTime === 'undefined' ? 0 : settings.waitTime;



                    for (let eventType of events)
                    {// Processing each entry
                        // (Listening for the event)
                        document.addEventListener(eventType, function (event) {
                            // (Clearing the timeout)
                            clearTimeout( private.timeout );



                            // (Getting the value)
                            private.lastPresence = new Date();



                            // (Setting the timeout)
                            private.timeout = setTimeout
                            (
                                function ()
                                {
                                    // (Triggering the event)
                                    public.triggerEvent
                                    (
                                        'idle',
                                        {
                                            'lastPresence': private.lastPresence,
                                            'lastEvent':    event
                                        }
                                    )
                                    ;
                                },
                                settings.waitTime * 1000
                            )
                            ;
                        });
                    }
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



                private.__construct( settings );
            }



            // (Creating a HumanPresenceDetector)
            const humanPresenceDetector = new Solenoid.HumanPresenceDetector
            (
                {
                    'waitTime': 10
                }
            )
            ;

            /*// (Listening for the event)
            humanPresenceDetector.addEventListener('idle', function (event) {
                console.debug(event);
            });*/

        </script>
    </body>
</html>
