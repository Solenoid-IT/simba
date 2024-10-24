<?php



namespace App;



use \Solenoid\Core\App\App;

use \Solenoid\Network\IPv4\IPv4;
use \Solenoid\Network\IPv4\Firewall;

use \Solenoid\HTTP\Request;

use \App\Middlewares\Cors as CorsMiddleware;



class Gate
{
    # Returns [bool]
    public static function run ()
    {
        switch ( App::$mode )
        {
            case 'cli':
                // Printing the value
                echo "Gate -> traversed\n\n";
            break;

            case 'http':
                if ( in_array( 'fw', App::$target->tags ) )
                {// (Route contains this tag)
                    // (Creating a Firewall)
                    $firewall = new Firewall
                    (
                        [],
                        [
                            '0.0.0.0/0'
                        ]
                    )
                    ;



                    // Returning the value
                    return $firewall->check( IPv4::select( Request::fetch()->client_ip ) );
                }



                if ( CorsMiddleware::run() === false ) return false;
            break;
        }
    }
}



?>