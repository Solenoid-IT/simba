<?php



namespace App\Stores;



use \Solenoid\HTTP\Cookie;
use \Solenoid\MySQL\DateTime as MySQLDateTime;
use \Solenoid\DateTime\DateTime;



class Localizer
{
    private static self $instance;



    # Returns [self]
    private function __construct () {}



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



    # Returns [string]
    public function localize_datetime (string $datetime)
    {
        // (Getting the value)
        $timezone = Cookie::fetch_value( 'timezone' );



        // Returning the value
        return
            $timezone
                ?
            DateTime::create( MySQLDateTime::create( $datetime )->to_iso() )->convert( $timezone, 'Y-m-d H:i:s' )
                :
            $datetime . ' UTC'
        ;
    }
}



?>