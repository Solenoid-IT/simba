<?php



namespace App\Middlewares\RPC;



use \Solenoid\Core\Middleware;

use \Solenoid\Core\App\WebApp;

use \Solenoid\RPC\Request;



class Authenticator extends Middleware
{
    # Returns [bool] | Throws [Exception]
    public static function run ()
    {
        // (Getting the value)
        $app = WebApp::fetch();



        // (Getting the value)
        $request = Request::fetch();

        if ( !$request->valid ) return false;

        if ( !$request->verify( $app->fetch_credentials()['rpc']['token'] ) ) return false;
    }
}



?>