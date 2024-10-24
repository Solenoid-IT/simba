<?php



namespace App\Middlewares\RPC;



use \Solenoid\Core\Middleware;

use \Solenoid\RPC\Request;



class Parser extends Middleware
{
    public static string $subject;
    public static string $verb;



    # Returns [bool] | Throws [Exception]
    public static function run ()
    {
        // (Getting the value)
        $request = Request::fetch();

        if ( !$request->valid ) return false;



        // (Getting the values)
        self::$subject = $request->subject;
        self::$verb    = $request->verb;
    }
}



?>