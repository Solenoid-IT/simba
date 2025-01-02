<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \App\Models\local\simba_db\Session as SessionModel;

use \App\Stores\Test as TestStore;



class Test extends Controller
{
    # Returns [void]
    public function get ()
    {
        // Returning the value
        return SessionModel::fetch()->filter( [ [ 'id' => 'ahcid' ] ] )->count();
    }

    # Returns [void]
    public function run ()
    {
        # debug
        #return \App\Stores\Connection\MySQL::get( 'local/simba_db' )->execute('SELECT CURRENT_TIMESTAMP AS `timestamp`;')->fetch_cursor()->fetch_head();



        // Returning the value
        return [ TestStore::get( '0' ), TestStore::get( '1' ), TestStore::get( '2' ) ];
    }
}



?>