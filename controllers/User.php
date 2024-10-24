<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;

use \App\Middlewares\User as UserMiddleware;
use \App\Middlewares\RPC\Parser as RPC;



class User extends Controller
{
    # Returns [void]
    public function rpc ()
    {
        switch ( RPC::$subject )
        {
            case 'session':
                switch ( RPC::$verb )
                {
                    case 'validate':
                        if ( UserMiddleware::run() === false ) return;



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;
                }
            break;
        }
    }
}



?>