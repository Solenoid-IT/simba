<?php



// (Including the file)
include_once( __DIR__ . '/lib/composer/autoload.php' );



// (Registering the function)
spl_autoload_register
(
    function ($name)
    {
        // (Getting the values)
        $parts                      = explode( "\\",  $name );

        $root                       = $parts[0];
        $object                     = $parts[1];



        if ( $root !== 'App' ) return;



        // (Getting the value)
        $local_namespace_components = array_slice( $parts, 2 );



        switch ($object)
        {
            case 'Libraries':
                // (Getting the value)
                $local_namespace_components = array_splice( $local_namespace_components, count( $local_namespace_components ) - 1, 0, 'src' );

                // (Setting the value)
                $file_path = __DIR__ . '/lib/src/' . implode( '/', $local_namespace_components ) . '.php';
            break;

            case 'Stores':
                // (Setting the value)
                $file_path = __DIR__ . '/stores/' . implode( '/', $local_namespace_components ) . '.php';
            break;

            case 'Models':
                // (Setting the value)
                $file_path = __DIR__ . '/models/' . implode( '/', $local_namespace_components ) . '.php';
            break;

            case 'Services':
                // (Setting the value)
                $file_path = __DIR__ . '/services/' . implode( '/', $local_namespace_components ) . '.php';
            break;

            case 'Middlewares':
                // (Setting the value)
                $file_path = __DIR__ . '/middlewares/src/' . implode( '/', $local_namespace_components ) . '.php';
            break;

            case 'Tasks':
                // (Setting the value)
                $file_path = __DIR__ . '/tasks/src/' . implode( '/', $local_namespace_components ) . '.php';
            break;

            case 'Controllers':
                // (Setting the value)
                $file_path = __DIR__ . '/controllers/' . implode( '/', $local_namespace_components ) . '.php';
            break;

            default:
                // Returning the value
                return;
        }



        // (Including the file)
        include_once( $file_path );
    }
)
;



?>