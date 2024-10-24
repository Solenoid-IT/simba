<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\Core\MVC\View;

use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Status;
use \Solenoid\HTTP\Response;

use \App\Middlewares\RPC\Parser as RPC;



class FormData extends Controller
{
    # Returns [void]
    public function get ()
    {
        // (Printing the value)
        View::build( 'components/multipart-formdata.blade.php' )->render();
    }

    # Returns [void]
    public function rpc ()
    {
        switch ( RPC::$subject )
        {
            case '':
                switch ( RPC::$verb )
                {
                    case 'transfer':
                        // Returning the value
                        return
                            Server::send( new Response( new Status(200), [ 'Content-Type: multipart/form-data' ], Request::fetch()->body ) )
                        ;
                    break;
                }
            break;
        }
    }
}



?>