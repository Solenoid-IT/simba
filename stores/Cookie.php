<?php



namespace App\Stores;



use \Solenoid\Core\App\App;

use \Solenoid\HTTP\Cookie as HttpCookie;



class Cookie
{
    private static self $instance;

    private static array $values = [];



    # Returns [HttpCookie|false]
    public static function get (string $id)
    {
        // Returning the value
        return self::$values[ $id ] ?? false;
    }

    # Returns [void]
    public static function set (string $id, HttpCookie &$value)
    {
        // (Getting the value)
        self::$values[ $id ] = &$value;
    }



    # Returns [self]
    private function __construct ()
    {
        // (Setting the values)
        self::set( 'user', new HttpCookie( 'user', '.' . App::$id, '/', true, true ) );
        self::set( 'fwd_route', new HttpCookie( 'fwd_route', '.' . App::$id, '/', true, true ) );
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