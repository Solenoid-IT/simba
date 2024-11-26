<?php



use \Solenoid\Core\Credentials;
use \Solenoid\Core\App\App;
use \Solenoid\Core\Storage;

use \Solenoid\MySQL\Connection;
use \Solenoid\MySQL\ConnectionStore;



foreach ( Credentials::fetch( '/mysql/data.json' ) as $profile => $v )
{// Processing each entry
    foreach ( $v as $db_name => $credentials )
    {// Processing each entry
        // (Setting the value)
        ConnectionStore::set( "$profile/$db_name", new Connection( $credentials['host'], $credentials['port'], $credentials['username'], $credentials['password'] ) );
    }
}



if ( App::$env->type === 'dev' )
{// Match OK
    // (Listening for the events)
    ConnectionStore::get( 'local/simba_db' )->add_event_listener
    (
        'error',
        function ($event)
        {
            // (Getting the values)
            $connection = $event['connection'];
            $query      = $event['query'];



            // (Setting the value)
            $message = "Unable to execute the query '$query' :: " . $connection->get_error_text();

            // Throwing an exception
            throw new \Exception($message);
        }
    )
    ;

    ConnectionStore::get( 'local/simba_db' )->add_event_listener
    (
        'before-execute',
        function ($event)
        {
            // (Writing to the file)
            Storage::select( 'local' )->write( '/debug/query.sql', $event['query'] . "\n\n\n", 'append' );
        }
    )
    ;
}



?>