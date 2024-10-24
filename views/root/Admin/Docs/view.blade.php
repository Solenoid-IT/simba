@php



use \App\Stores\Localizer as LocalizerStore;



// (Initializing the store)
LocalizerStore::read();



@endphp



@extends('layouts/base.blade.php')



@section('view.head')
    <title>
        Documents
    </title>



    <!-- jTextEditor -->
    <link rel="stylesheet" href="https://www.solenoid.it/cdn/swg/jtexteditor/jtexteditor.css">
    <script src="https://www.solenoid.it/cdn/swg/jtexteditor/jtexteditor.js"></script>



    <style>
    
        .document-editor
        {
            width: 100%;
            height: 76vh;
            min-height: 700px;
            border-radius: 4px;
        }

        .document-preview
        {
            width: 100%;
            height: 100%;
        }
    
    </style>
@endsection



@section('view.body')

    <h1 class="mt-4">Documents</h1>

    <div class="row">
        <div class="col d-flex justify-content-end">
            <button class="btn btn-primary" id="register_resource_button" title="Add">
                <i class="fa-solid fa-plus"></i>
                <span class="btn-label ms-1">
                    Add
                </span>
            </button>
        </div>
    </div>

    <div class="card mt-3 mb-4">
        <div class="card-body">
            <div class="swg swg-table" id="resource_table">
                <div class="search-box">
                    <div class="num-results-box">
                        Results : <span class="num-results">0</span>
                    </div>

                    <input type="text" class="form-control input" name="value" placeholder="Search ...">
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th data-column="id">
                                <div class="title">
                                    ID

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="path">
                                <div class="title">
                                    Path

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="title">
                                <div class="title">
                                    Title

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="description">
                                <div class="title">
                                    Description

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="tag_list">
                                <div class="title">
                                    Tag List

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="datetime.insert">
                                <div class="title">
                                    Insert DateTime

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="datetime.update">
                                <div class="title">
                                    Update DateTime

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="owner">
                                <div class="title">
                                    Owner

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="sitemap">
                                <div class="title">
                                    Sitemap

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>
                            <th data-column="active">
                                <div class="title">
                                    Active

                                    <span class="sort-dir-indicator-box">
                                        <i class="fa-solid fa-caret-up"></i>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </span>
                                </div>

                                <input type="text" class="form-control input" data-type="local-search">

                                <div class="key-search-box-button">
                                    <i class="fa-solid fa-caret-up"></i>
                                    <i class="fa-solid fa-caret-down"></i>
                                </div>

                                <div class="key-search-box">
                                    <div class="key-search-box-inner">
                                        <div class="num-results-box">
                                            Total : <span class="num-results" data-type="total">0</span>
                                        </div>
                                        <div class="num-results-box">
                                            Selected : <span class="num-results" data-type="selected">0</span>
                                        </div>
                                        <input type="text" class="form-control input" data-type="key-search">
                                        <ul data-type="controls">
                                            <li>
                                                <label>
                                                    <input type="checkbox" class="input" data-all>
                                                    <span class="key-value">[ ALL ] : </span>
                                                    <span class="num-results" data-type="keys">0</span>
                                                </label>
                                            </li>
                                        </ul>
                                        <ul data-type="values"></ul>
                                    </div>
                                </div>
                            </th>

                            <th data-controls></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $document)
                            @php
                                // (Setting the value)
                                $tag_values = [];



                                foreach ($document['tag_list'] as $tag)
                                {// Processing each entry
                                    // (Getting the values)
                                    list( $value, $color ) = explode( '=', $tag );

                                    // (Appending the value)
                                    $tag_values[] = "<span class=\"tag-value ms-2 ml-2\" style=\"border-bottom-color: $color;\"><b>$value</b></span>";
                                }



                                // (Getting the value)
                                $tag_values = implode( '', $tag_values );



                                // (Getting the value)
                                $local_datetimes =
                                [
                                    'insert' => LocalizerStore::fetch()->localize_datetime( $document['datetime']['insert'] ),
                                    'update' => $document['datetime']['update'] ? LocalizerStore::fetch()->localize_datetime( $document['datetime']['update'] ) : null
                                ]
                                ;
                            @endphp

                            <tr data-id="{{ $document['id'] }}">
                                <td data-column="id" data-value="{{ $document['id'] }}">
                                    {{ $document['id'] }}
                                </td>
                                <td data-column="path" data-value="{{ $document['path'] }}">
                                    {{ $document['path'] }}
                                </td>
                                <td data-column="title" data-value="{{ $document['title'] }}">
                                    {{ $document['title'] }}
                                </td>
                                <td data-column="description" data-value="{{ $document['description'] }}">
                                    {{ $document['description'] }}
                                </td>
                                <td data-column="tag_list" data-value="{{ implode( ';', $document['tag_list'] ) }}">
                                    {!! $tag_values !!}
                                </td>
                                <td data-column="datetime.insert" data-value="{{ strtotime( $local_datetimes['insert'] ) }}" data-real-value="{{ $local_datetimes['insert'] }}">
                                    <span class="local-datetime" title="{{ $document['datetime']['insert'] }} UTC">
                                        {{ $local_datetimes['insert'] }}
                                    </span>
                                </td>
                                <td data-column="datetime.update" data-value="{{ strtotime( $local_datetimes['update'] ) }}" data-real-value="{{ $local_datetimes['update'] }}">
                                    <span class="local-datetime" title="{{ $document['datetime']['update'] }} UTC">
                                        {{ $local_datetimes['update'] }}
                                    </span>
                                </td>
                                <td data-column="owner" data-value="{{ $document['owner']['username'] }}">
                                    <span title="{{ $document['owner']['name'] }} {{ $document['owner']['surname'] }}">
                                        {{ $document['owner']['username'] }}
                                    </span>
                                </td>

                                <td data-column="sitemap" data-value="{{ $document['datetime']['option']['sitemap'] ? 'checked' : '' }}">
                                    <input type="checkbox" class="m-auto d-table document-option" data-id="{{ $document['id'] }}" data-option="sitemap" {{ $document['datetime']['option']['sitemap'] ? 'checked' : '' }}>
                                </td>

                                <td data-column="active" data-value="{{ $document['datetime']['option']['active'] ? 'checked' : '' }}">
                                    <input type="checkbox" class="m-auto d-table document-option" data-id="{{ $document['id'] }}" data-option="active" {{ $document['datetime']['option']['active'] ? 'checked' : '' }}>
                                </td>

                                <td data-controls>
                                    <div class="table-row-controls">
                                        <a class="btn btn-success" href="/docs{{ $document['path'] }}" target="_blank" title="View">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                            <span class="btn-label ms-1">
                                                View
                                            </span>
                                        </a>
                                        <button class="btn btn-warning ms-2" value="edit" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                            <span class="btn-label ms-1">
                                                Edit
                                            </span>
                                        </button>
                                        <button class="btn btn-danger ms-2" value="delete" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                            <span class="btn-label ms-1">
                                                Delete
                                            </span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <div class="modal fade theme-dark" id="register_resource_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 98%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        Document
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="swg swg-form" id="register_resource_form">
                        <input type="hidden" class="input" name="id" value="">

                        <div class="row">
                            <div class="col">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control input" name="path" placeholder="path" data-required>
                                    <label>Path</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <div class="document-editor jtexteditor mt-3"></div>
                            </div>
                            <div class="col">
                                <iframe class="document-preview" srcdoc=""></iframe>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 60px;">
                            <div class="col d-flex justify-content-center">
                                <span class="polymorph-block" data-id="register" hidden>
                                    <button type="submit" class="btn btn-primary m-auto">
                                        Add
                                    </button>
                                </span>
                                <span class="polymorph-block" data-id="change" hidden>
                                    <button type="submit" class="btn btn-primary m-auto">
                                        Save
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('view.script')

    <script name="button">
    
        // (Click-Event on the element)
        $('#register_resource_button').on('click', function () {
            // (Resetting the form)
            $('#register_resource_form')[0].swg.reset();



            // (Setting the attributes)
            $('#register_resource_modal .polymorph-block').attr('hidden', true);
            $('#register_resource_modal .polymorph-block[data-id="register"]').attr('hidden', false);



            // (Showing the modal)
            $('#register_resource_modal').modal('show');
        });

    </script>

    <script name="form">
    
        // (Setting the value)
        const resourceType = 'document';



        // (Creating an instance of Form)
        const form = new Solenoid.SWG.Form( $('#register_resource_form')[0] );

        // (Listening for the events)
        form.addEventListener('submit', async function (event) {
            if ( Object.keys( form.validate( true ) ).length > 0 ) return;



            // (Getting the value)
            const input = await form.getInput();



            // (Getting the value)
            input['content'] = btoa( $('.document-editor').swg().getValue() );



            // (Sending an http request)
            const response = await Solenoid.HTTP.sendRequest
            (
                '',
                'RPC',
                {
                    'Action': `${ resourceType }::${ input['id'] ? 'change' : 'register' }`
                },
                JSON.stringify( input ),
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

    <script name="table">

        // (Creating a Table)
        const table = new Solenoid.SWG.Table.Table( $('#resource_table')[0], $('#resource_table').prev()[0] );

        // (Entry-Action-Event on the element)
        table.addEventListener('entry.action', async function (event) {
            // (Getting the values)
            const id     = event.entry.getId();
            const action = event.action;



            // (Setting the value)
            let response = null;

            switch ( action )
            {
                case 'edit':
                    // (Sending an http request)
                    response = await Solenoid.HTTP.sendRequest
                    (
                        '',
                        'RPC',
                        {
                            'Action':  `${ resourceType }::find`
                        },
                        JSON.stringify
                        (
                            {
                                'key':   'id',
                                'value': id
                            }
                        ),
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



                    // (Resetting the form)
                    $('#register_resource_form')[0].swg.reset();

                    // (Setting the input)
                    $('#register_resource_form')[0].swg.setInput( response.body['document'] );

                    // (Setting the value)
                    $('.document-editor').swg().setValue( response.body['document']['content'] );



                    // (Setting the attributes)
                    $('#register_resource_modal .polymorph-block').attr('hidden', true);
                    $('#register_resource_modal .polymorph-block[data-id="change"]').attr('hidden', false);



                    // (Showing the modal)
                    $('#register_resource_modal').modal('show');
                break;

                case 'delete':
                    if ( !confirm( 'Are you sure to remove this entry ?' ) )
                    {// (Confirmation failed)
                        // Returning the value
                        return;
                    }



                    // (Sending an http request)
                    response = await Solenoid.HTTP.sendRequest
                    (
                        '',
                        'RPC',
                        {
                            'Action': `${ resourceType }::unregister`
                        },
                        JSON.stringify
                        (
                            {
                                'id': id
                            }
                        ),
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
                break;
            }
        });

        // (Search-Event on the element)
        table.addEventListener('global-search', function (event) {
            // (Filtering the values)
            table.filter('FILTER_GLOBAL');
        });

        // (Local-Search-Event on the element)
        table.addEventListener('local-search', function (event) {
            // (Filtering the values)
            table.filter('FILTER_LOCAL');
        });

        // (Local-Search-Event on the element)
        table.addEventListener('key-search', function (event) {
            // (Filtering the values)
            table.filter('FILTER_KEY');
        });



        // (Change-Event on the element)
        $('.document-option').on('change', async function () {
            // (Sending an http request)
            const response = await Solenoid.HTTP.sendRequest
            (
                '',
                'RPC',
                {
                    'Action': `${ resourceType }::set_option`
                },
                JSON.stringify
                (
                    {
                        'id':     $(this).attr('data-id'),

                        'option': $(this).attr('data-option'),
                        'value':  $(this).prop('checked')
                    }
                ),
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
        });

    </script>



    <script name="document-editor">
    
        // (Getting the value)
        iframeContent = atob( iframeContent );



        // (Getting the value)
        const iframeElement = $('.document-preview');



        // (Setting the attributes)
        iframeElement.attr( 'srcdoc', iframeContent );



        // (Creating an instance of jTextEditor)
        $('.document-editor').jtexteditor
        (
            {
                'language': 'html',
                'value': iframeContent,

                'eventListener':
                {
                    'change': function (event)
                    {
                        // (Getting the value)
                        const value = event.value;

                        // (Setting the attribute)
                        iframeElement.attr( 'srcdoc', value );
                    }
                }
            }
        )
        ;
    
    </script>

@endsection