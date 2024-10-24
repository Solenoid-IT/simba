@php



use \App\Stores\Localizer as LocalizerStore;



// (Initializing the store)
LocalizerStore::read();



@endphp



@extends('layouts/base.blade.php')

@section('view.head')

    <title>
        Dashboard
    </title>

@endsection

@section('view.body')

    <h1 class="mt-4">Dashboard</h1>

    {{--

    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">Primary Card</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">Warning Card</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">Success Card</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">Danger Card</div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    --}}

    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Visitors • Current Month
                </div>
                <div class="card-body">
                    <canvas id="current_month_visitors_chart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Visitors • Current Year
                </div>
                <div class="card-body"><canvas id="current_year_visitors_chart" width="100%" height="40"></canvas></div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <div class="row">
                <div class="col d-flex align-items-center">
                    <i class="fas fa-table me-1"></i>
                    Visitors • List
                </div>
                <div class="col d-flex justify-content-end">
                    <button class="btn btn-danger ms-2" id="empty_log_button" title="Empty Log" {{ $user['hierarchy']['type'] === 'root' ? '' : 'hidden' }}>
                        <i class="fa-solid fa-trash"></i>
                        <span class="btn-label ms-1">
                            Empty
                        </span>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table id="visitor_table">
                <thead>
                    <tr>
                        <th>Document</th>
                        <th>IP</th>
                        <th>Browser</th>
                        <th>OS</th>
                        <th>HW</th>
                        <th>Access DateTime</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($visitors as $visitor)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="ms-2 me-2">
                                        {{ $visitor['route'] }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="ms-2 me-2">
                                        {{ $visitor['ip']['address'] }}
                                    </span>
                                     •
                                    <span class="ms-2 me-2">
                                        {{ $visitor['ip']['country']['code'] }}
                                    </span>
                                     •
                                    <span class="ms-2 me-2">
                                        {{ $visitor['ip']['country']['name'] }}
                                    </span>
                                     •
                                    <span class="ms-2 me-2">
                                        <img class="country-flag" src="https://flagsapi.com/{{ $visitor['ip']['country']['code'] }}/flat/64.png" alt="">
                                    </span>
                                     •
                                    <span class="ms-2 me-2">
                                        {{ $visitor['ip']['isp'] }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                {{ $visitor['browser'] }}
                            </td>
                            <td>
                                {{ $visitor['os'] }}
                            </td>
                            <td>
                                {{ $visitor['hw'] }}
                            </td>
                            <td>
                                <span class="local-datetime" title="{{ $visitor['datetime']['insert'] }} UTC">
                                    {{ LocalizerStore::fetch()->localize_datetime( $visitor['datetime']['insert'] ) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection



@section('view.script')

    <script name="chart">

        // (Setting the values)
        Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
        Chart.defaults.global.defaultFontColor = '#292b2c';



        // (Creating a Chart)
        const currentMonthVisitorsChart = new Chart
        (
            $('#current_month_visitors_chart'),
            {
                'type': 'line',

                'data':
                {
                    'labels': charts.currentMonthVisitors.labels,
                    'datasets':
                    [
                        {
                            'label': "Visitors",
                            'lineTension': 0.3,
                            'backgroundColor': "rgba(2,117,216,0.2)",
                            'borderColor': "rgba(2,117,216,1)",
                            'pointRadius': 5,
                            'pointBackgroundColor': "rgba(2,117,216,1)",
                            'pointBorderColor': "rgba(255,255,255,0.8)",
                            'pointHoverRadius': 5,
                            'pointHoverBackgroundColor': "rgba(2,117,216,1)",
                            'pointHitRadius': 50,
                            'pointBorderWidth': 2,
                            'data': charts.currentMonthVisitors.data
                        }
                    ],
                },

                'options':
                {
                    'scales':
                    {
                        'xAxes':
                        [
                            {
                                'time':
                                {
                                    'unit': 'date'
                                },

                                'gridLines':
                                {
                                    'display': false
                                },

                                'ticks':
                                {
                                    //'maxTicksLimit': 7
                                }
                            }
                        ],

                        /*

                        'yAxes':
                        [
                            {
                                'ticks':
                                {
                                    'min': 0,
                                    'max': 40000,
                                    'maxTicksLimit': 5
                                },
                                'gridLines':
                                {
                                    'color': "rgba(0, 0, 0, .125)",
                                }
                            }
                        ]

                        */
                    },

                    'legend':
                    {
                        'display': false
                    }
                }
            }
        )
        ;



        // (Creating a Chart)
        const currentYearVisitorsChart = new Chart
        (
            $('#current_year_visitors_chart')[0],
            {
                'type': 'bar',
                'data':
                {
                    'labels': charts.currentYearVisitors.labels,
                    'datasets':
                    [
                        {
                            'label': 'Visitors',
                            'backgroundColor': "rgba(2,117,216,1)",
                            'borderColor': "rgba(2,117,216,1)",
                            'data': charts.currentYearVisitors.data,
                        }
                    ],
                },

                'options':
                {
                    'scales':
                    {
                        'xAxes':
                        [
                            {
                                'time':
                                {
                                    'unit': 'month'
                                },

                                'gridLines':
                                {
                                    'display': false
                                },

                                'ticks':
                                {
                                    //'maxTicksLimit': 6
                                }
                            }
                        ],

                        /*

                        'yAxes':
                        [
                            {
                                'ticks':
                                {
                                    'min': 0,
                                    'max': 15000,
                                    'maxTicksLimit': 5
                                },

                                'gridLines':
                                {
                                    'display': true
                                }
                            }
                        ]

                        */
                    },

                    'legend':
                    {
                        'display': false
                    }
                }
            }
        )
        ;

    </script>



    <script name="table">

        // (Creating a DataTable)
        new simpleDatatables.DataTable( $('#visitor_table')[0] );

    </script>



    <script name="button">
    
        // (Click-Event on the element)
        $('#empty_log_button').on('click', async function () {
            if ( !confirm('Are you sure to empty the log ?') ) return;



            // (Sending an http request)
            const response = await Solenoid.HTTP.sendRequest
            (
                '/admin/visitor',
                'RPC',
                {
                    'Action': 'visitor::unregister'
                },
                '',
                'json'
            )
            ;

            if ( response.status.code !== 200 )
            {// (Request failed)
                // (Alerting the value)
                alert( response.body['error']['message'] );

                // Returning the value
                return;
            }



            // (Setting the location)
            window.location.href = '';
        });
    
    </script>

@endsection