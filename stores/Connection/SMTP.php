<?php



namespace App\Stores\Connection;



use \Solenoid\Core\Credentials;

use \Solenoid\SMTP\Connection;



class SMTP
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
        foreach ( Credentials::fetch( '/smtp/data.json' ) as $profile => $credentials )
        {// Processing each entry
            // (Setting the value)
            self::set( $profile, new Connection( $credentials['host'], $credentials['port'], $credentials['username'], $credentials['password'], $credentials['encryption_type'] ) );
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