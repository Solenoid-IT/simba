<?php



namespace App\Models\local\simba_db;



use \Solenoid\MySQL\Model;

use \Solenoid\MySQL\Query;

use \App\Stores\Connections\MySQL\Store as MySQLConnectionsStore;



class Session extends Model
{
    private static self $instance;



    # Returns [self]
    private function __construct ()
    {
        // (Calling the function)
        parent::__construct( MySQLConnectionsStore::fetch()->connections['local/simba_db'], 'simba_db', 'session' );
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



    # Returns [Cursor|false]
    public function view ()
    {
        // Returning the value
        return ( new Query( $this->connection ) )->from( $this->database, "view::$this->table::all" )->select_all()->run();
    }
}



?>