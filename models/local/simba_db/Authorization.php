<?php



namespace App\Models\local\simba_db;



use \Solenoid\MySQL\Model;

use \Solenoid\MySQL\Query;

use \App\Stores\Connections\MySQL\Store as MySQLConnectionsStore;



class Authorization extends Model
{
    private static self $instance;



    # Returns [self]
    private function __construct ()
    {
        // (Calling the function)
        parent::__construct( MySQLConnectionsStore::fetch()->connections['local/simba_db'], 'simba_db', 'authorization' );
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



    # Returns [Record|false]
    public function get ()
    {
        // (Getting the value)
        $record = self::fetch()->find();

        if ( $record === false )
        {// (Record not found)
            // Returning the value
            return false;
        }



        // (Getting the value)
        $record->data = json_decode( $record->data, true );



        // Returning the value
        return $record;
    }
}



?>