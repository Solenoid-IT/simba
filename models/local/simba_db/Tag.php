<?php



namespace App\Models\local\simba_db;



use \Solenoid\MySQL\Model;

use \App\Stores\Connections\MySQL\Store as MySQLConnectionsStore;



class Tag extends Model
{
    private static self $instance;



    # Returns [self]
    private function __construct ()
    {
        // (Calling the function)
        parent::__construct( MySQLConnectionsStore::fetch()->connections['local/simba_db'], 'simba_db', 'tag' );
    }



    # Returns [self]
    public static function fetch ()
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self();
        }



        // (Resetting the condition)
        ( self::$instance )->reset();



        // Returning the value
        return self::$instance;
    }
}



?>