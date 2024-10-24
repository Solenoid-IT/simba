<?php



namespace App\Models\local\simba_db;



use \Solenoid\MySQL\Model;

use \Solenoid\MySQL\Record;

use \App\Stores\Connections\MySQL\Store as MySQLConnectionsStore;



class User extends Model
{
    private static self $instance;



    # Returns [self]
    private function __construct ()
    {
        // (Calling the function)
        parent::__construct( MySQLConnectionsStore::fetch()->connections['local/simba_db'], 'simba_db', 'user' );
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