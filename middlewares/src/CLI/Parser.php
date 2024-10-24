<?php



namespace App\Middlewares\CLI;



use \Solenoid\Core\Middleware;

use \Solenoid\Core\App\SysApp;



class Parser extends Middleware
{
    public static array $args;



    # Returns [bool] | Throws [Exception]
    public static function run ()
    {
        // (Getting the value)
        $app = SysApp::fetch();



        // (Setting the value)
        $args = [];

        foreach ( $app->target->args as $arg )
        {// Processing each entry
            // (Getting the value)
            $parts = explode( '=', $arg );

            if ( count($parts) === 1 )
            {// (There is only a part)
                // (Appending the value)
                $args[] = $parts[0];
            }
            else
            {// (There are more parts)
                // (Getting the value)
                $args[ $parts[0] ] = $parts[1];
            }
        }



        // (Getting the value)
        self::$args = &$args;
    }
}



?>