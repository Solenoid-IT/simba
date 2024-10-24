<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;



class TestArgs extends Controller
{
    # Returns [void]
    public function get (string $str, int $int)
    {
        // Returning the value
        return "$str->$int";
    }
}



?>