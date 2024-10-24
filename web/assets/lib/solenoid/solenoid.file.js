// © Solenoid Team



if (typeof Solenoid === 'undefined') Solenoid = {};

Solenoid.File = {};



// Returns [void]
Solenoid.File.download = function (type, fileName, content)
{
    // (Getting the values)
    const blob = new Blob( [ content ], { 'type': type } );
    const url  = window.URL.createObjectURL( blob );



    // (Creating an element)
    const a = document.createElement('a');

    // (Getting the values)
    a.href     = url;
    a.download = fileName;



    // (Appending the child)
    document.body.appendChild(a);



    // (Triggering the event)
    a.click();

    // (Removing the element)
    a.remove();



    // (Revoking the url object)
    window.URL.revokeObjectURL(url);
}

// Returns [Promise]
Solenoid.File.read = async function (file)
{
    // Returning the value
    return new Promise
    (
        function (resolve, reject)
        {
            // (Creating a FileReader)
            const fileReader = new FileReader();

            // (Listening for the event)
            fileReader.addEventListener('load', function () {
                // (Calling the function)
                resolve( fileReader.result );
            });

            // (Reading the file as text)
            fileReader.readAsText( file );
        }
    )
    ;
}

// Returns [Promise:void]
Solenoid.File.select = async function (accept, multiple)
{
    // Returning the value
    return new Promise( function (resolve, reject) {
        // (Getting the value)
        const id = 'solenoid_file_select_tmp_' + new Date().valueOf();



        // (Creating an element)
        const element = document.createElement('input');

        // (Setting the values)
        element.type     = 'file';
        element.accept   = typeof accept === 'undefined' ? '*' : accept;
        element.multiple = typeof multiple === 'undefined' ? false : multiple;
        element.id       = id;



        // (Appending the value)
        document.body.append( element );



        // (Triggering the event)
        element.click();

        element.addEventListener('change', function (event) {
            // Calling the function
            resolve( event.target.files );



            // (Removing the element)
            element.remove();
        });
    });
}