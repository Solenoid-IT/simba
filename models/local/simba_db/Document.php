<?php



namespace App\Models\local\simba_db;



use \Solenoid\MySQL\Model;

use \App\Stores\Connection\MySQL as ConnectionStore;



class Document extends Model
{
    private static self $instance;



    public string $conn_id  = 'local/simba_db';
    public string $database = 'simba_db';
    public string $table    = 'document';



    # Returns [self]
    private function __construct ()
    {
        // (Getting the value)
        $connection = ConnectionStore::get( $this->conn_id );

        if ( !$connection )
        {// Value not found
            // (Getting the value)
            $message = "Connection '" . $this->conn_id . "' not found";

            // Throwing an exception
            throw new \Exception($message);

            // Returning the value
            return;
        }



        // Calling the function
        parent::__construct( $connection, $this->database, $this->table );
    }

    # Returns [self]
    public static function fetch ()
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self();
        }



        // (Resetting the model)
        self::$instance->reset();



        // Returning the value
        return self::$instance;
    }
}



?>