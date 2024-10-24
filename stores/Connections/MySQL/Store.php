<?php



namespace App\Stores\Connections\MySQL;



use \Solenoid\Core\App\App;
use \Solenoid\Core\Storage;
use \Solenoid\Core\Credentials;

use \Solenoid\MySQL\Connection;



class Store
{
    private static self $instance;



    public array $connections;



    # Returns [self]
    private function __construct ()
    {
        // (Getting the value)
        $profiles = Credentials::fetch( '/mysql/data.json' );



        // (Setting the value)
        $this->connections = [];

        foreach ( $profiles as $profile => $v )
        {// Processing each entry
            foreach ( $v as $db_name => $credentials )
            {// Processing each entry
                // (Getting the value)
                $this->connections[ "$profile/$db_name" ] = new Connection( $credentials['host'], $credentials['port'], $credentials['username'], $credentials['password'] );
            }
        }



        if ( App::$env->type === 'dev' )
        {// Match OK
            // (Listening for the events)
            $this->connections['local/simba_db']->add_event_listener
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

            $this->connections['local/simba_db']->add_event_listener
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
    public static function fetch ()
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self();
        }



        // Returning the value
        return self::$instance;
    }
}



?>