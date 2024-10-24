<?php



namespace App\Middlewares;



use \Solenoid\Core\Middleware;

use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Status;
use \Solenoid\HTTP\Response;

use \App\Services\User as UserService;



class User extends Middleware
{
    # Returns [bool] | Throws [Exception]
    public static function run ()
    {
        // (Verifying the user)
        $response = UserService::verify();

        if ( $response->status->code === 401 )
        {// (Session is not valid)
            if ( Request::fetch()->method === 'GET' )
            {// Match OK
                // (Sending the response)
                Server::send( new Response( new Status(303), [ 'Location: /admin/login' ] ) );
            }
            else
            {// Match failed
                // (Sending the response)
                Server::send( $response );
            }



            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }
}



?>