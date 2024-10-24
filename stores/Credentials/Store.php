<?php



namespace App\Stores\Credentials;



use \Solenoid\Core\App\App;



class Store
{
    private static self $instance;



    public array $credentials;



    # Returns [self]
    private function __construct ()
    {
        // (Getting the value)
        $app = App::get();



        // (Getting the value)
        $this->credentials = $app->fetch_credentials();
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