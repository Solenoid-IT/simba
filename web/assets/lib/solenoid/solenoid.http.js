// © Solenoid Team



if (typeof Solenoid === 'undefined') Solenoid = {};

Solenoid.HTTP = {};



Solenoid.HTTP.eventListeners =
{
    'start':                       [],
    'upload.progress':             [],
    'download.progress':           [],
    'end':                         [],

    'network-error':               [],
    'enqueued-request':            [],
    'unprocessable-request-queue': [],
    'request-queue-empty':         []
}
;

// Returns [void]
Solenoid.HTTP.addEventListener = function (type, callback)
{
    // (Appending the value)
    Solenoid.HTTP.eventListeners[type].push(callback);
}

// Returns [void]
Solenoid.HTTP.triggerEvent = function (event)
{
    // (Iterating each entry)
    Solenoid.HTTP.eventListeners[ event.type ].forEach( function (callback) { callback( event ); } );
}



Solenoid.HTTP.Event = function (type, data)
{
    const private = {};
    const public  = this;



    // Returns [void]
    private.__construct = function (type, data)
    {
        // (Getting the values)
        public.type = type;
        public.data = data;
    }



    private.__construct( type, data );
}



Solenoid.HTTP.Status = function (code, message)
{
    const private = {};
    const public  = this;



    // Returns [void]
    private.__construct = function (code, message)
    {
        // (Getting the values)
        public.code    = code;
        public.message = message;
    }



    private.__construct( code, message );
}



Solenoid.HTTP.defaultHeaders = {};



Solenoid.HTTP.Request = function (headers, body)
{
    const private = {};
    const public  = this;



    // Returns [self]
    private.__construct = function (headers, body)
    {
        // (Getting the values)
        public.headers = headers;
        public.body    = body;
    }



    // Returns [string|false]
    public.get = function (key)
    {
        for ( const header of public.headers )
        {// Processing each entry
            // (Getting the values)
            [ k, v ] = header.split( ': ', 2 );

            if ( key.toLowerCase() === k.toLowerCase() ) return v;
        }



        // Returning the value
        return false;
    }

    // Returns [Array<string>]
    public.getAll = function (key)
    {
        // (Setting the value)
        let results = [];

        for ( const header of public.headers )
        {// Processing each entry
            // (Getting the values)
            [ k, v ] = header.split( ': ', 2 );

            if ( key.toLowerCase() === k.toLowerCase() ) results.push( v );
        }



        // Returning the value
        return results;
    }



    private.__construct( headers, body );
}

Solenoid.HTTP.Response = function (status, headers, body)
{
    const private = {};
    const public  = this;



    // Returns [self]
    private.__construct = function (status, headers, body)
    {
        // (Getting the values)
        public.status  = status;
        public.headers = headers;
        public.body    = body;
    }



    // Returns [string|false]
    public.get = function (key)
    {
        for ( const header of public.headers )
        {// Processing each entry
            // (Getting the values)
            [ k, v ] = header.split( ': ', 2 );

            if ( key === k ) return v;
        }



        // Returning the value
        return false;
    }

    // Returns [Array<string>]
    public.getAll = function (key)
    {
        // (Setting the value)
        let results = [];

        for ( const header of public.headers )
        {// Processing each entry
            // (Getting the values)
            [ k, v ] = header.split( ': ', 2 );

            if ( key === k ) results.push( v );
        }



        // Returning the value
        return results;
    }



    private.__construct( status, headers, body );
}



Solenoid.HTTP.URL_PREFIX = '';



Solenoid.HTTP.CapturedRequest = function (request, xhr)
{
    const private = {};
    const public  = this;



    // Returns [void]
    private.__construct = function (request, xhr)
    {
        // (Getting the values)
        public.request = request;
        private.xhr    = xhr;
    }



    // Returns [void]
    public.abort = function ()
    {
        // (Aborting the request)
        private.xhr.abort();
    }



    private.__construct( request, xhr );
}

Solenoid.HTTP.Endpoint = function (url)
{
    const private = {};
    const public  = this;



    // Returns [void]
    private.__construct = function (url)
    {
        // (Getting the value)
        public.url = `${ Solenoid.HTTP.URL_PREFIX }${ url }`;

        // (Setting the value)
        private.eventListeners =
        {
            'start':                       [],
            'upload.progress':             [],
            'download.progress':           [],
            'end':                         [],

            'network-error':               [],
            'enqueued-request':            [],
            'unprocessable-request-queue': [],
            'request-queue-empty':         []
        }
        ;



        // (Setting the values)
        private.captureRequests = false;
        public.capturedRequests = [];



        // (Setting the value)
        private.credentials = false;
    }



    // Returns [void]
    public.addEventListener = function (type, callback)
    {
        // (Appending the value)
        private.eventListeners[type].push(callback);
    }
    
    // Returns [void]
    public.triggerEvent = function (event, global)
    {
        if (typeof global === 'undefined') global = false;



        // (Iterating each entry)
        private.eventListeners[ event.type ].forEach( function (callback) { callback( event ); } );



        if (global)
        {// Value is true
            // (Calling the function)
            Solenoid.HTTP.triggerEvent(event);
        }
    }



    // Returns [void]
    public.captureRequests = function (value)
    {
        if ( typeof value === 'undefined' ) value = true;



        // (Setting the value)
        private.captureRequests = value;
    }



    // Returns [Promise:Response|false]
    public.sendRequest = async function (method, request, responseType, offline)
    {
        if (typeof method === 'undefined') method = 'GET';
        if (typeof responseType === 'undefined') responseType = '';
        if (typeof offline === 'undefined') offline = false;



        // (Creating an XMLHttpRequest)
        const xhr = new XMLHttpRequest();



        // (Getting the values)
        xhr.responseType    = responseType;
        xhr.withCredentials = private.credentials;



        // (Opening the request)
        xhr.open( method, public.url, true );



        for ( const k in Solenoid.HTTP.defaultHeaders )
        {// Processing each entry
            // (Setting the request header)
            xhr.setRequestHeader( k, Solenoid.HTTP.defaultHeaders[ k ] );
        }

        for ( const header of request.headers )
        {// Processing each entry
            // (Getting the values)
            [ k, v ] = header.split( ': ', 2 );

            // (Setting the request header)
            xhr.setRequestHeader( k, v );
        }



        if ( private.captureRequests )
        {// Value is true
            // (Appending the value)
            public.capturedRequests.push( new Solenoid.HTTP.CapturedRequest( request, xhr ) );
        }



        // Returning the value
        return new Promise
        (
            function (resolve, reject)
            {
                // (Listening for the events)
                xhr.onreadystatechange = function ()
                {
                    if ( xhr.readyState === XMLHttpRequest.DONE )
                    {// (Response has been obtained)
                        if ( xhr.status === 0 )
                        {// (There is a network error)
                            // (Calling the function)
                            resolve(false);



                            // (Getting the value)
                            const unprocessableRequest =
                            {
                                'url':          url,
                                'method':       method,
                                'headers':      request.headers,
                                'body':         request.body,
                                'responseType': responseType,
                                'credentials':  private.credentials,

                                'mode':         'async'
                            }
                            ;

                            // (Triggering the event)
                            public.triggerEvent
                            (
                                new Solenoid.HTTP.Event
                                (
                                    'network-error',
                                    unprocessableRequest
                                ),

                                true
                            )
                            ;

                            if ( ( Solenoid.HTTP.enqueueRequestsOnOffline || offline ) && Solenoid.HTTP.offline )
                            {// Match OK
                                // (Appending the value)
                                Solenoid.HTTP.requestQueue.push(unprocessableRequest);

                                // (Triggering the event)
                                public.triggerEvent
                                (
                                    new Solenoid.HTTP.Event
                                    (
                                        'enqueued-request',
                                        unprocessableRequest
                                    ),

                                    true
                                )
                                ;
                            }



                            // Returning the value
                            return;
                        }



                        // (Getting the value)
                        const status = new Solenoid.HTTP.Status
                        (
                            xhr.status,
                            xhr.statusText
                        )
                        ;



                        // (Getting the value)
                        const headers = xhr.getAllResponseHeaders().split("\r\n").filter( function (entry) { return entry.length > 0; } );



                        // (Getting the value)
                        const body = xhr.response;



                        // (Creating a Response)
                        const response = new Solenoid.HTTP.Response( status, headers, body );



                        if ( responseType === '' )
                        {// Match OK
                            // (Getting the value)
                            const contentType = response.get( 'content-type' );

                            if ( contentType )
                            {// Value found
                                switch ( contentType )
                                {
                                    case 'application/json':
                                        // (Getting the value)
                                        response.body = JSON.parse( response.body );
                                    break;
    
                                    default:
                                        // (Doing nothing)
                                }
                            }
                        }
                        /*
                        else
                        if ( responseType === 'arraybuffer' )
                        {// Match OK
                            // (Getting the value)
                            const contentType = response.get( 'content-type' );

                            if ( contentType )
                            {// Value found
                                switch ( contentType )
                                {
                                    case 'multipart/form-data':
                                        // (Getting the value)
                                        //response.body = '';
                                    break;
    
                                    default:
                                        // (Doing nothing)
                                }
                            }
                        }
                        */
                        


                        // debug
                        //console.debug(response);



                        // (Triggering the event)
                        public.triggerEvent
                        (
                            new Solenoid.HTTP.Event
                            (
                                'end',
                                {
                                    'endpointURL': url,
                                    'method':      method,
                                    'request':     request,
                                    'response':    response
                                }
                            ),
                            true
                        )
                        ;



                        // (Calling the function)
                        resolve(response);
                    }
                }

                xhr.upload.onprogress = function (event)
                {
                    if (event.lengthComputable)
                    {// Value is true
                        // (Triggering the event)
                        public.triggerEvent
                        (
                            new Solenoid.HTTP.Event
                            (
                                'upload.progress',
                                {
                                    'originalEvent': event,
                                    'request':       request
                                }
                            ),
                            true
                        )
                        ;
                    }
                }

                xhr.onprogress = function (event)
                {
                    if (event.lengthComputable)
                    {// Value is true
                        // (Triggering the event)
                        public.triggerEvent
                        (
                            new Solenoid.HTTP.Event
                            (
                                'download.progress',
                                {
                                    'originalEvent': event,
                                    'request':       request
                                }
                            ),
                            true
                        )
                        ;
                    }
                }



                // (Sending the http request)
                xhr.send( request.body );
                


                // (Triggering the event)
                public.triggerEvent
                (
                    new Solenoid.HTTP.Event
                    (
                        'start',
                        {
                            'endpointURL': url,
                            'method':      method,
                            'request':     request
                        }
                    ),
                    true
                )
                ;



                if ( Solenoid.HTTP.debug === 'RPC' )
                {// Value is true
                    if ( method === 'RPC' )
                    {// Match OK
                        // (Logging the message)
                        console.debug( `${ method } ${ url } -> ${ request.get( 'Action' ) ?? '<action>?' }` );
                    }
                }
            }
        )
        ;
    }

    // Returns [Response|false]
    public.sendSyncRequest = async function (method, request, responseType, offline)
    {
        if (typeof method === 'undefined') method = 'GET';
        if (typeof responseType === 'undefined') responseType = '';
        if (typeof offline === 'undefined') offline = false;



        // (Creating an XMLHttpRequest)
        const xhr = new XMLHttpRequest();



        // (Getting the values)
        xhr.responseType    = responseType;
        xhr.withCredentials = private.credentials;



        // (Opening the request)
        xhr.open( method, public.url, false );



        for ( const k in Solenoid.HTTP.defaultHeaders )
        {// Processing each entry
            // (Setting the request header)
            xhr.setRequestHeader( k, Solenoid.HTTP.defaultHeaders[ k ] );
        }
    
        for ( const header of request.headers )
        {// Processing each entry
            // (Getting the values)
            [ k, v ] = header.split( ': ', 2 );

            // (Setting the request header)
            xhr.setRequestHeader( k, v );
        }



        // (Listening for the events)
        xhr.upload.onprogress = function (event)
        {
            if (event.lengthComputable)
            {// Value is true
                // (Triggering the event)
                public.triggerEvent
                (
                    new Solenoid.HTTP.Event
                    (
                        'upload.progress',
                        {
                            'originalEvent': event,
                            'request':       request
                        }
                    ),
                    true
                )
                ;
            }
        }

        xhr.onprogress = function (event)
        {
            if (event.lengthComputable)
            {// Value is true
                // (Triggering the event)
                public.triggerEvent
                (
                    new Solenoid.HTTP.Event
                    (
                        'download.progress',
                        {
                            'originalEvent': event,
                            'request':       request
                        }
                    ),
                    true
                )
                ;
            }
        }



        // (Triggering the event)
        public.triggerEvent
        (
            new Solenoid.HTTP.Event
            (
                'start',
                {
                    'endpointURL': url,
                    'method':      method,
                    'request':     request
                }
            ),
            true
        )
        ;



        // (Sending the http request)
        xhr.send( request.body );



        if ( xhr.status === 0 )
        {// (There is a network error)
            // (Getting the value)
            const unprocessableRequest =
            {
                'url':          url,
                'method':       method,
                'headers':      request.headers,
                'body':         request.body,
                'responseType': responseType,
                'credentials':  private.credentials,

                'mode':         'async'
            }
            ;

            // (Triggering the event)
            public.triggerEvent
            (
                new Solenoid.HTTP.Event
                (
                    'network-error',
                    unprocessableRequest
                ),

                true
            )
            ;

            if ( ( Solenoid.HTTP.enqueueRequestsOnOffline || offline ) && Solenoid.HTTP.offline )
            {// Match OK
                // (Appending the value)
                Solenoid.HTTP.requestQueue.push(unprocessableRequest);

                // (Triggering the event)
                public.triggerEvent
                (
                    new Solenoid.HTTP.Event
                    (
                        'enqueued-request',
                        unprocessableRequest
                    ),

                    true
                )
                ;
            }



            // Returning the value
            return false;
        }



        // (Getting the value)
        const status = new Solenoid.HTTP.Status
        (
            xhr.status,
            xhr.statusText
        )
        ;



        // (Getting the value)
        const headers = xhr.getAllResponseHeaders().split("\r\n").filter( function (entry) { return entry.length > 0; } );



        // (Getting the value)
        const body = xhr.response;



        // (Creating a Response)
        const response = new Solenoid.HTTP.Response( status, headers, body );



        if ( responseType === '' )
        {// Match OK
            if ( response.get( 'content-type' ) )
            {// Value found
                switch ( response.get( 'content-type' ) )
                {
                    case 'application/json':
                        // (Getting the value)
                        response.body = JSON.parse( response.body );
                    break;

                    default:
                        // (Doing nothing)
                }
            }
        }



        // debug
        //console.debug(response);



        // (Triggering the event)
        public.triggerEvent
        (
            new Solenoid.HTTP.Event
            (
                'end',
                {
                    'endpointURL': url,
                    'method':      method,
                    'request':     request,
                    'response':    response
                }
            ),
            true
        )
        ;



        // Returning the value
        return response;
    }



    // Returns [void]
    public.setCredentials = function (value)
    {
        if ( typeof value === 'undefined' ) value = false;



        // (Getting the value)
        private.credentials = value;
    }



    private.__construct(url);
}



// Returns [Promise]
Solenoid.HTTP.sendRequest = async function (url, method, headers, body, responseType, credentials, offline)
{
    // (Creating an Endpoint)
    const endpoint = new Solenoid.HTTP.Endpoint(url);



    // (Setting the value)
    endpoint.setCredentials(credentials);



    // (Sending the request)
    const response = await endpoint.sendRequest( method, new Solenoid.HTTP.Request( headers, body ), responseType, offline );



    // Returning the value
    return response;
}

// Returns [Response]
Solenoid.HTTP.sendSyncRequest = function (url, method, headers, body, responseType, credentials, offline)
{
    // (Creating an Endpoint)
    const endpoint = new Solenoid.HTTP.Endpoint(url);



    // (Setting the value)
    endpoint.setCredentials(credentials);



    // (Sending the request)
    const response = endpoint.sendSyncRequest( method, new Solenoid.HTTP.Request( headers, body ), responseType, offline );



    // Returning the value
    return response;
}



// Returns [object]
Solenoid.HTTP.getCookies = function ()
{
    // (Setting the value)
    let cookies = {};



    // (Getting the value)
    const kvs = document.cookie.split( /\;\s*/ );

    for (const kv in kvs)
    {// Processing each entry
        // (Getting the value)
        const [ k, v ] = kvs[ kv ].split('=');

        // (Getting the value)
        cookies[ k ] = v;
    }



    // Returning the value
    return cookies;
}



Solenoid.HTTP.debug = false;



Solenoid.HTTP.requestQueue = [];
Solenoid.HTTP.enqueueRequestsOnOffline = false;

Solenoid.HTTP.offline = false;



// Returns [Promise:array<object>]
Solenoid.HTTP.processQueue = async function ()
{
    // (Setting the value)
    let unprocessedRequestQueue = [];

    while ( Solenoid.HTTP.requestQueue.length > 0 )
    {// Processing each entry
        // (Getting the value)
        const request = Solenoid.HTTP.requestQueue.shift();



        // (Sending the request)
        const response = await Solenoid.HTTP.sendRequest
        (
            request.url,
            request.method,
            request.headers,
            request.body,
            request.responseType,
            request.credentials
        )
        ;

        if ( response.status.code === 0 )
        {// (There is a network error)
            // (Appending the value)
            unprocessedRequestQueue.push( request );
        }
    }



    // Returning the value
    return unprocessedRequestQueue;
}



// (Listening for the events)
window.addEventListener('offline', function () {
    // (Setting the value)
    Solenoid.HTTP.offline = true;
});

window.addEventListener('online', function () {
    // (Setting the value)
    Solenoid.HTTP.offline = false;



    // (Processing the queue)
    const unprocessedRequestQueue = Solenoid.HTTP.processQueue();

    if ( unprocessedRequestQueue.length > 0 )
    {// Value is not empty
        // (Triggering the event)
        Solenoid.HTTP.triggerEvent
        (
            new Solenoid.HTTP.Event
            (
                'unprocessable-request-queue',
                {
                    'queue': unprocessedRequestQueue
                }
            )
        )
        ;
    }
    else
    {// Value is empty
        // (Triggering the event)
        Solenoid.HTTP.triggerEvent
        (
            new Solenoid.HTTP.Event
            (
                'request-queue-empty'
            )
        )
        ;
    }
});