<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \App\Models\local\simba_db\Session as SessionModel;



class Test extends Controller
{
    # Returns [void]
    public function get ()
    {
        // Returning the value
        return SessionModel::fetch()->filter( [ [ 'id' => 'ahcid' ] ] )->count();
    }
}



?>