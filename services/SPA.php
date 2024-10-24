<?php



namespace App\Services;



use \Solenoid\Core\Service;

use \Solenoid\Core\App\WebApp;



class SPA extends Service
{
    # Returns [string]
    public static function fetch_be_host ()
    {
        // (Getting the value)
        $app = WebApp::fetch();



        // Returning the value
        return preg_replace( '/^front\-/', '', $app->request->url->host );
    }
}



?>