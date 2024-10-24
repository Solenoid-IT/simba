<?php



namespace App\Stores\Cookies;



use \Solenoid\Core\App\App;

use \Solenoid\HTTP\Cookie;



class Store
{
    private static self $instance;



    public array $cookies;



    # Returns [self]
    private function __construct ()
    {
        // (Getting the value)
        $this->cookies =
        [
            'user'      => new Cookie( 'user', '.' . App::$id, '/', true, true),
            'fwd_route' => new Cookie( 'fwd_route', '.' . App::$id, '/', true, true)
        ]
        ;
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