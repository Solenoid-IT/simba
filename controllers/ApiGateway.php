<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\HTTP\Request;

use \Solenoid\RPC\Action;



class ApiGateway extends Controller
{
    # Returns [void]
    public function process_action ()
    {
        // (Getting the value)
        $request = Request::fetch();



        // Returning the value
        return Action::run( '\\App\\Controllers\\API\\', $request->url->fetch_params()['m'] ?? $request->headers['Action'] );
    }
}



?>