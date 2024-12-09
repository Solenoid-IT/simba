<?php



namespace App\Middlewares\RPC;



use \Solenoid\Core\Middleware;
use \Solenoid\Core\Credentials;

use \Solenoid\RPC\Request;



class Authenticator extends Middleware
{
    # Returns [bool] | Throws [Exception]
    public static function run ()
    {
        // (Getting the value)
        $request = Request::fetch();

        if ( !$request->valid ) return false;

        if ( !$request->verify( Credentials::fetch( 'system/data.json' )['rpc']['token'] ) ) return false;
    }
}



?>