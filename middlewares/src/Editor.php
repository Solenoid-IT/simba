<?php



namespace App\Middlewares;



use \Solenoid\Core\Middleware;

use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;

use \Solenoid\MySQL\Condition;

use \App\Stores\Sessions\Store as SessionsStore;
use \App\Models\local\simba_db\User as UserModel;



class Editor extends Middleware
{
    # Returns [bool] | Throws [Exception]
    public static function run ()
    {
        // (Getting the value)
        $user_id = SessionsStore::fetch()->sessions['user']->data['user'];



        // (Getting the value)
        $user = UserModel::fetch()->filter( [ [ 'id' => $user_id ] ] )->find();

        if ( $user === false )
        {// (User not found)
            // (Sending the response)
            Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'User not found' ] ] ) );



            // Returning the value
            return false;
        }



        if ( !in_array( $user->hierarchy, [ 1, 2 ] ) )
        {// (Match failed)
            // (Sending the response)
            Server::send( new Response( new Status(403), [], [ 'error' => [ 'message' => 'Operation not permitted' ] ] ) );



            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }
}



?>