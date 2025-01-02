<?php



namespace App\Models\local\simba_db;



use \Solenoid\MySQL\Model;
use \Solenoid\MySQL\Record;

use \App\Stores\Connection\MySQL as ConnectionStore;



class User extends Model
{
    private static self $instance;



    public string $conn_id  = 'local/simba_db';
    public string $database = 'simba_db';
    public string $table    = 'user';



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



    # Returns [Record|false]
    public function get ()
    {
        // (Getting the value)
        $record = $this->find
        (
            [ 'security.password' ],
            true,
            true,
            function (Record $record)
            {
                // (Getting the values)
                $record->profile->photo                = json_decode( $record->profile->photo, true );
                $record->security->mfa                 = $record->security->mfa === 1;

                $idk_auth                              = $record->security->idk->authentication === 1;

                $record->security->idk                 = new \stdClass();
                $record->security->idk->authentication = $idk_auth;



                // Returning the value
                return $record;
            }
        )
        ;



        // Returning the value
        return $record;
    }

    # Returns [array<Record>]
    public function get_list ()
    {
        // (Getting the value)
        $records = $this->list
        (
            [ 'security.password' ],
            true,
            [],
            true,
            function (Record $record)
            {
                // (Getting the values)
                $record->profile->photo                = json_decode( $record->profile->photo, true );
                $record->security->mfa                 = $record->security->mfa === 1;

                $idk_auth                              = $record->security->idk->authentication === 1;

                $record->security->idk                 = new \stdClass();
                $record->security->idk->authentication = $idk_auth;



                // Returning the value
                return $record;
            }
        )
        ;



        // Returning the value
        return $records;
    }
}



?>