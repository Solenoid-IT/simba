<?php



namespace App\Middlewares;



use \Solenoid\Core\Middleware;

use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Server;



class Cors extends Middleware
{
    # Returns [bool] | Throws [Exception]
    public static function run ()
    {
        /*

        // (Getting the value)
        $app = \Solenoid\Core\App\WebApp::fetch();



        if ( $app->env->type !== 'dev' ) return true;



        if ( $app->request->client_ip === $app->request->server_ip )
        {// Match OK
            // Returning the value
            return true;
        }

        if ( !$app->request->headers['Origin'] )
        {// Match failed
            // Returning the value
            return true;
        }



        // (Setting the cors)
        Server::set_cors( [ $app->request->headers['Origin'] ], [ 'GET', 'RPC', 'SSE' ], [ 'Dev-Sid', 'Auth-Token', 'Action', 'Content-Type', 'Route' ], true );



        if ( $app->request->method === 'OPTIONS' )
        {// Match OK
            // (Sending the response)
            Server::send( new \Solenoid\HTTP\Response( new \Solenoid\HTTP\Status(200) ) );



            // Returning the value
            return false;
        }



        // Returning the value
        return true;

        */



        // (Getting the value)
        $request = Request::fetch();

        if ( $request->headers['Origin'] )
        {// Match OK
            // (Setting the cors)
            Server::set_cors( [ $request->headers['Origin'] ], [ 'GET', 'RPC', 'SSE' ], [ 'Action', 'Content-Type', 'Auth-Token', 'Route' ], true );
        }

        if ( $request->method === 'OPTIONS' )
        {// Match OK
            // Returning the value
            return false;
        }
    }
}



?>