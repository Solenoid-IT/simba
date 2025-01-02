<?php



namespace App\Stores\Connection;



use \Solenoid\Core\Credentials;
use \Solenoid\Core\App\App;
use \Solenoid\Core\Storage;

use \Solenoid\MySQL\Connection;



class MySQL
{
    private static self $instance;

    private static array $values = [];



    # Returns [Connection|false]
    public static function get (string $id)
    {
        // Returning the value
        return self::$values[ $id ] ?? false;
    }

    # Returns [void]
    public static function set (string $id, Connection &$value)
    {
        // (Getting the value)
        self::$values[ $id ] = &$value;
    }



    # Returns [self]
    private function __construct ()
    {
        foreach ( Credentials::fetch( '/mysql/data.json' ) as $profile => $credentials )
        {// Processing each entry
            // (Setting the value)
            self::set( $profile, new Connection( $credentials['host'], $credentials['port'], $credentials['username'], $credentials['password'] ) );
        }



        if ( App::$env->type === 'dev' )
        {// Match OK
            // (Listening for the events)
            self::get( 'local/simba_db' )->add_event_listener
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

            self::get( 'local/simba_db' )->add_event_listener
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
    }

    # Returns [self]
    public static function preset ()
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // Returning the value
            self::$instance = new self();
        }



        // Returning the value
        return self::$instance;
    }
}



?>