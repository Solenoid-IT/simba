<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">



        <title>
            Blank Document
        </title>

        <meta name="description" content="This is a blank document">
        <meta name="keywords" content="tag-1, tag-2, tag-3">



        <?php

            use \Solenoid\Core\App\WebApp;



            // (Getting the value)
            $app = WebApp::fetch();
        ?>



        <!-- Template styles -->
        <link rel="stylesheet" href="https://{{ $host }}/assets/tpl/sb-admin/dist/css/styles.css">

        <!-- Custom styles -->
        <link rel="stylesheet" href="https://{{ $host }}{{ $app->asset('/assets/styles/custom.css') }}">



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
        <script src="https://{{ $host }}/assets/lib/solenoid/solenoid.swg.table.js"></script>
        <script src="https://{{ $host }}/assets/lib/solenoid/solenoid.swg.form.js"></script>
        <script src="https://{{ $host }}/assets/lib/solenoid/solenoid.swg.passwordfield.js"></script>
        <script src="https://{{ $host }}/assets/lib/solenoid/solenoid.swg.spinner.js"></script>






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
            });

        </script>



        <style>

            #layoutSidenav
            {
                height: 100%;

                /* height: 100vh; */

                /*
                
                position: fixed;
                top: 0;
                bottom: 0;
                
                */
            }

            #layoutSidenav_content
            {
                overflow-y: auto;
            }



            .block-ptr
            {
                cursor: pointer;
            }



            .fn-list
            {
                padding: 0;
                list-style: none;
            }

            .fn-name-color
            {
                color: #b56d11;
            }

            .fn-square-brackets-color
            {
                color: #ec9900;
            }

            .fn-input-list
            {
                padding: 0;
                display: inline-block;
                list-style: none;
            }

            .fn-input-list li
            {
                display: inline-block;
                color: #ec4200;
            }

            .fn-input-list li.fn-input-list-separator
            {
                color: #a2a2a2;
            }

            .fn-object-color
            {
                color: #00aaec;
                font-weight: 700;
            }

            .fn-object-color:hover
            {
                text-decoration: underline;
            }
        
        </style>
    </head>

    <body>
        <div class="swg swg-spinner" id="page-loader">
            <div class="spinner-box">
                <div class="spinner-message" hidden></div>
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
                <div class="spinner-countdown" hidden></div>
                <div class="spinner-progress" hidden>
                    <div class="spinner-progress-value"></div>
                </div>
            </div>
            <div class="spinner-blackscreen" style="background-color: #737373;"></div>
        </div>



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



        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Main</div>
                            <a class="nav-link block-ptr" data-ref="/main/overview">
                                <div class="sb-nav-link-icon">
                                    <i class="fa-solid fa-house"></i>
                                </div>
                                
                                Overview
                            </a>
                            <a class="nav-link block-ptr" data-ref="/main/requirements">
                                <div class="sb-nav-link-icon">
                                    <i class="fa-solid fa-list-check"></i>
                                </div>
                                
                                Requirements
                            </a>
                            <a class="nav-link block-ptr" data-ref="/main/setup">
                                <div class="sb-nav-link-icon">
                                    <i class="fa-solid fa-gear"></i>
                                </div>
                                
                                Setup
                            </a>



                            <div class="sb-sidenav-menu-heading">Objects</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePages" aria-expanded="false" aria-controls="collapsePages">
                                <div class="sb-nav-link-icon">
                                    {{--<i class="fas fa-book-open"></i>--}}
                                    <i class="fa-solid fa-box"></i>
                                </div>
                                HTTP
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                    <a class="nav-link collapsed block-ptr" data-ref="/HTTP/Request" data-bs-toggle="collapse" data-bs-target="#pagesCollapseAuth" aria-expanded="false" aria-controls="pagesCollapseAuth">
                                        <i class="fa-solid fa-cube sidebar-icon"></i>
                                        Request
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseAuth" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link block-ptr" data-ref="/HTTP/Request/read">
                                                <i class="fa-solid fa-play sidebar-icon"></i>
                                                read
                                            </a>
                                            <a class="nav-link block-ptr" data-ref="/HTTP/Request/send">
                                                <i class="fa-solid fa-play sidebar-icon"></i>
                                                send
                                            </a>
                                            <a class="nav-link block-ptr" data-ref="/HTTP/Request/forward">
                                                <i class="fa-solid fa-play sidebar-icon"></i>
                                                forward
                                            </a>
                                        </nav>
                                    </div>
                                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#pagesCollapseError" aria-expanded="false" aria-controls="pagesCollapseError">
                                        <i class="fa-solid fa-cube sidebar-icon"></i>
                                        Cookie
                                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                                    </a>
                                    <div class="collapse" id="pagesCollapseError" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordionPages">
                                        <nav class="sb-sidenav-menu-nested nav">
                                            <a class="nav-link block-ptr" data-ref="/HTTP/Cookie/fetch_value">
                                                <i class="fa-solid fa-play sidebar-icon"></i>
                                                fetch_value
                                            </a>
                                            <a class="nav-link block-ptr" data-ref="/HTTP/Cookie/set">
                                                {{--<i class="fa-solid fa-terminal sidebar-icon"></i>--}}
                                                <i class="fa-solid fa-play sidebar-icon"></i>
                                                set
                                            </a>
                                            <a class="nav-link block-ptr" data-ref="/HTTP/Cookie/delete">
                                                <i class="fa-solid fa-play sidebar-icon"></i>
                                                delete
                                            </a>
                                        </nav>
                                    </div>
                                </nav>
                            </div>



                            <div class="sb-sidenav-menu-heading">Examples</div>
                            <a class="nav-link block-ptr" data-ref="/examples/advanced-proxy">
                                <div class="sb-nav-link-icon">
                                    <i class="fa-solid fa-rocket"></i>
                                </div>
                                
                                Advanced Proxy
                            </a>
                        </div>
                    </div>

                    {{--<div class="sb-sidenav-footer"></div>--}}
                </nav>
            </div>

            <div id="layoutSidenav_content">
                <main class="page-main">
                    <div class="container-fluid px-4">
                        <div class="block" data-id="/main/overview">
                            <h1 class="mt-4">Overview</h1>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item">Main</li>
                                <li class="breadcrumb-item active">Overview</li>
                            </ol>

                            <p>
                                This library contains functions to take control over <b>HTTP</b> requests.
                            </p>
                        </div>

                        <div class="block mt-5" data-id="/main/requirements">
                            <h1 class="mt-4">Requirements</h1>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item">Main</li>
                                <li class="breadcrumb-item active">Requirements</li>
                            </ol>

                            <p>
                                This library requires some dependencies :
                                <br>
                                <ul>
                                    <li>
                                        <b>php-curl</b> extension
                                    </li>
                                    <li>
                                        <b>composer</b>
                                    </li>
                                </ul>
                            </p>
                        </div>

                        <div class="block mt-5" data-id="/main/setup">
                            <h1 class="mt-4">Setup</h1>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item">Main</li>
                                <li class="breadcrumb-item active">Setup</li>
                            </ol>

                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus augue et justo lacinia congue. Donec condimentum risus mi, vel interdum risus iaculis vitae. Pellentesque eleifend felis eget feugiat finibus. Donec efficitur scelerisque ipsum, non vestibulum felis sollicitudin nec. Donec purus eros, lobortis in facilisis sed, commodo ac nunc. Nullam bibendum magna id ligula auctor, vitae sodales nunc elementum. Suspendisse nibh eros, convallis quis aliquam ac, blandit quis est. Curabitur in justo malesuada, feugiat nisl sed, suscipit nunc. Pellentesque ac ipsum aliquet, rutrum arcu a, consectetur diam. Mauris nec quam accumsan tortor tempus mattis. Curabitur rutrum convallis metus ut venenatis. Nullam viverra, massa ut euismod maximus, risus nisl pellentesque sapien, at tristique turpis magna id velit. Curabitur at vehicula est. Duis luctus felis nec imperdiet aliquam. 
                                <br><br>
                                Etiam mi diam, eleifend nec tempus nec, gravida non nibh. Pellentesque at tempus purus, sit amet hendrerit est. Praesent vitae iaculis lorem. Aliquam aliquam lacinia libero quis convallis. Sed a ullamcorper ligula, at dignissim erat. Vivamus at nisi a libero rutrum gravida. Pellentesque aliquam ante blandit feugiat porttitor. Fusce vitae tellus mauris. Nulla feugiat vestibulum velit, convallis mattis mi ullamcorper vitae. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus facilisis ac risus sit amet sodales. Phasellus aliquet nulla sapien. Nunc in libero pellentesque, aliquet neque ac, gravida neque. 
                            </p>
                        </div>

                        <div class="block mt-5" data-id="/HTTP/Request">
                            <h1 class="mt-4">Request</h1>

                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item">HTTP</li>
                                <li class="breadcrumb-item active">Request</li>
                            </ol>

                            <p>
                                This class is able to parse and extract incoming request data, send requests to specific url (through <b>cURL</b> extension) or forward them (like a proxy).
                            </p>

                            <ul class="fn-list">
                                <li>
                                    <div class="block mt-5" data-id="/HTTP/Request/read">
                                        <h4 class="mt-4">
                                            :: <span class="fn-name-color">read</span> <span class="fn-square-brackets-color">()</span>
                                        </h4>

                                        <p class="ps-3 pe-3">
                                            Takes no input args and returns a <span class="fn-object-color block-ptr" data-ref="/HTTP/Request">Request</span> object.
                                            <br><br>
                                            It contains incoming HTTP request data like <b>host</b>, <b>ip</b>, <b>path</b>, <b>method</b>, <b>headers</b> and <b>body</b>.
                                            <br>
                                            It contains many functions to extract and manipulate other data like <b>query string</b>.
                                        </p>
                                    </div>
                                </li>

                                <li>
                                    <div class="block mt-5" data-id="/HTTP/Request/send">
                                        <h4 class="mt-4">
                                            :: <span class="fn-name-color">send</span>

                                            <span class="fn-square-brackets-color">(</span>
                                            <ul class="fn-input-list">
                                                <li>
                                                    url
                                                </li>
                                                <li class="fn-input-list-separator">
                                                    ,
                                                </li>
                                                <li>
                                                    method
                                                </li>
                                                <li class="fn-input-list-separator">
                                                    ,
                                                </li>
                                                <li>
                                                    headers
                                                </li>
                                                <li class="fn-input-list-separator">
                                                    ,
                                                </li>
                                                <li>
                                                    body
                                                </li>
                                                <li class="fn-input-list-separator">
                                                    ,
                                                </li>
                                                <li>
                                                    response_type
                                                </li>
                                            </ul>
                                            <span class="fn-square-brackets-color">)</span>
                                        </h4>

                                        <p class="ps-3 pe-3">
                                            Takes request parameters and returns a string containing the response body.
                                        </p>
                                    </div>
                                </li>

                                <li>
                                    <div class="block mt-5" data-id="/HTTP/Request/forward">
                                        <h4 class="mt-4">
                                            :: <span class="fn-name-color">forward</span>

                                            <span class="fn-square-brackets-color">(</span>
                                            <ul class="fn-input-list">
                                                <li>
                                                    url
                                                </li>
                                            </ul>
                                            <span class="fn-square-brackets-color">)</span>
                                        </h4>

                                        <p class="ps-3 pe-3">
                                            Takes the destination url and returns a boolean.
                                        </p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </main>
            </div>
        </div>



        <div class="swg swg-spinner" id="spinner" hidden>
            <div class="spinner-box">
                <div class="spinner-message" hidden></div>
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
                <div class="spinner-countdown" hidden></div>
                <div class="spinner-progress" hidden>
                    <div class="spinner-progress-value"></div>
                </div>
            </div>
            <div class="spinner-blackscreen"></div>
        </div>

        <script name="spinner">

            // (Creating a Spinner)
            const spinner = new Solenoid.SWG.Spinner
            (
                {
                    'element': $('#spinner')[0]
                }
            )
            ;

        </script>



        <!-- Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

        <!-- ChartJS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>

        

        <script name="block">
        
            // (Click-Event on the element)
            $('.block-ptr').on('click', function (event) {
                // (Preventing the default action)
                event.preventDefault();

                // (Getting the value)
                const id = $(this).attr('data-ref');

                // (Scrolling to the element)
                $(`.block[data-id="${ id }"]`)[0].scrollIntoView( { 'behavior': 'smooth' } );
            });

        </script>

        <script name="custom">

            // (Doing nothing)

        </script>
    </body>
</html>