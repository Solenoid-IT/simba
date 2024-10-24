<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;
use \Solenoid\Core\MVC\View;



class SPA extends Controller
{
    # Returns [void]
    public function get ()
    {
        // (Printing the value)
        View::build_html( '../web/build/index.blade.php' )->render();
    }
}



?>