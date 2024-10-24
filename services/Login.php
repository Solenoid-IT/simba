<?php



namespace App\Services;



use \Solenoid\Core\Service;

use \Solenoid\HTTP\Request;



class Login extends Service
{
    # Returns [string]
    public static function extract_location ()
    {
        // Returning the value
        return Request::fetch()->cookies['fwd_route'] ?? '/admin/dashboard';
    }
}



?>