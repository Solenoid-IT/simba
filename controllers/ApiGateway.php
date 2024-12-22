<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\RPC\Action;



class ApiGateway extends Controller
{
    # Returns [void]
    public function process_action ()
    {
        // Returning the value
        return Action::run( '\\App\\Controllers\\API\\' );
    }
}



?>