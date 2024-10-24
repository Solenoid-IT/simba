<!doctype html>
<html>
<head>
    <script src="https://dev.simba.solenoid.it/assets/lib/solenoid/solenoid.http.js"></script>
    <script src="https://www.solenoid.it/cdn/lib/js/solenoid/solenoid.file.js"></script>
</head>
<body>
    <form id="form">
        <input type="text" class="input" name="name" value="John">
        <input type="text" class="input" name="surname" value="Doe">
        <input type="text" class="input" name="email" value="john-doe@gmail.com">
        <input type="file" class="input" name="pictures" multiple>

        <button type="submit">Submit</button>
    </form>

    <script>
    
        // (Listening for the event)
        document.querySelector('#form').addEventListener('submit', async function (event) {
            // (Preventing the default)
            event.preventDefault();



            // (Creating a FormData)
            const fd = new FormData();

            // (Appending the values)
            fd.append( 'name', 'John' );
            fd.append( 'surname', 'Doe' );
            fd.append( 'email', 'john-doe@gmail.com' );

            for ( const file of document.querySelector('#form .input[name="pictures"]').files )
            {// Processing each entry
                // (Appending the value)
                fd.append( 'pictures', file, file.name );
            }



            // (Sending the request)
            const response = await Solenoid.HTTP.sendRequest
            (
                '',
                'RPC',
                [
                    'Action: transfer',
                    'Content-Type: multipart/form-data'
                ],
                fd,
                'blob'
            )
            ;

            // (Downloading the file)
            Solenoid.File.download( response.body.type, 'mfd', response.body );
        });

    </script>
</body>
</html>