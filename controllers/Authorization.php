<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\Core\App\WebApp;

use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;
use \Solenoid\HTTP\Client\Client;

use \Solenoid\SSE\Server as SSEServer;
use \Solenoid\SSE\Event as SSEEvent;

use \Solenoid\MySQL\Condition;

use \App\Stores\Sessions\Store as SessionsStore;
use \App\Models\local\simba_db\Authorization as AuthorizationModel;
use \App\Models\local\simba_db\User as UserModel;
use \App\Services\Authorization as AuthorizationService;



class Authorization extends Controller
{
    # Returns [void]
    public function get (string $token, string $action)
    {
        switch ( $action )
        {
            case 'decline':
                // (Removing the authorization)
                $response = AuthorizationService::remove( $token );

                if ( $response->status->code !== 200 )
                {// (Unable to remove the authorization)
                    // Returning the value
                    return
                        Server::send( $response )
                    ;
                }
            break;

            case 'accept':
                // (Getting the value)
                $response = AuthorizationService::fetch( $token );

                if ( $response->status->code !== 200 )
                {// (Authorization is not valid)
                    // Returning the value
                    return
                        Server::send( new Response( new Status(200), [], [ 'message' => 'Request has been processed' ] ) )
                    ;
                }



                // (Getting the value)
                $authorization = $response->body;



                if ( $authorization->data['request'] )
                {// (Authorization contains a request to make)
                    // (Getting the value)
                    $request = Request::fetch();



                    // (Sending an http request)
                    $res = Client::send
                    (
                        $request->url->fetch_base() . $authorization->data['request']['endpoint_path'],
                        'RPC',
                        [
                            'Action: ' . $authorization->data['request']['action'],
                            'Content-Type: application/json',

                            "Auth-Token: $token",

                            'Session-Id: ' . $request->cookies['user']
                        ]
                    )
                    ;

                    if ( $res->fetch_tail()->status->code !== 200 )
                    {// (Request failed)
                        // Returning the value
                        return
                            Server::send( new Response( new Status( $res->fetch_tail()->status->code ), [], $res->body ) )
                        ;
                    }



                    // (Getting the value)
                    $user_id = $res->body;
                }



                if ( $authorization->data['display'] )
                {// (Authorization contains a message to display)
                    // (Removing the authorization)
                    $resp = AuthorizationService::remove( $token );

                    if ( $resp->status->code !== 200 )
                    {// (Unable to remove the authorization)
                        // Returning the value
                        return
                            Server::send( $resp )
                        ;
                    }



                    // Returning the value
                    return
                        Server::send( new Response( new Status(200), [ 'Content-Type: text/html' ], $authorization->data['display'] ) )
                    ;
                }



                if ( $authorization->data['login'] )
                {// (Authorization contains a login to do)
                    // (Getting the value)
                    $user = UserModel::fetch()->where( 'id', $user_id )->find();

                    if ( $user === false )
                    {// (Record not found)
                        // Returning the value
                        return
                            Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                        ;
                    }



                    // (Getting the value)
                    $session = SessionsStore::fetch()->sessions['user'];



                    if ( !$session->start() )
                    {// (Unable to start the session)
                        // Returning the value
                        return
                            Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => [ 'Unable to start the session' ] ] ] ) )
                        ;
                    }

                    if ( !$session->regenerate_id() )
                    {// (Unable to regenerate the session id)
                        // Returning the value
                        return
                            Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => [ 'Unable to regenerate the session id' ] ] ] ) )
                        ;
                    }

                    if ( !$session->set_duration() )
                    {// (Unable to set the session duration)
                        // Returning the value
                        return
                            Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => [ 'Unable to set the session duration' ] ] ] ) )
                        ;
                    }



                    // (Setting the value)
                    $session->data = [];



                    // (Getting the value)
                    $session->data['user'] = $user->id;
                }



                // (Getting the value)
                $callback_url = $authorization->callback_url;



                // (Removing the authorization)
                $response = AuthorizationService::remove( $token );

                if ( $response->status->code !== 200 )
                {// (Unable to remove the authorization)
                    // Returning the value
                    return
                        Server::send( $response )
                    ;
                }



                if ( $callback_url )
                {// Value found
                    // Returning the value
                    return
                        Server::send( new Response( new Status(303), [ "Location: $callback_url" ] ) )
                    ;
                }
            break;

            default:
                // (Removing the authorization)
                $response = AuthorizationService::remove( $token );

                if ( $response->status->code !== 200 )
                {// (Unable to remove the authorization)
                    // Returning the value
                    return
                        Server::send( $response )
                    ;
                }
        }



        // Returning the value
        return
            Server::send( new Response( new Status(200), [], [ 'message' => 'Request has been processed' ] ) )
        ;
    }

    # Returns [void]
    public function sse ()
    {
        // (Getting the value)
        $session = SessionsStore::fetch()->sessions['user'];



        // (Starting the session)
        $session->start();



        // (Getting the value)
        $token = $session->data['authorization'];

        if ( !isset( $token ) )
        {// Value not found
            // (Destroying the session)
            $session->destroy();



            // Returning the value
            return
                Server::send( new Response( new Status(401), [ 'Content-Type: application/json' ], [ 'error' => [ 'message' => 'Authorization not found' ] ] ) )
            ;
        }



        // (Starting the server)
        SSEServer::create()->start
        (
            function () use (&$session, $token)
            {
                // (Waiting for the seconds)
                sleep( 1 );



                // (Getting the value)
                $authorization = AuthorizationModel::fetch()->filter( [ [ 'token' => $token ] ] )->get();

                if ( $authorization === false )
                {// (Authorization not found)
                    // (Closing the session)
                    $session->close();



                    // Returning the value
                    return
                        SSEEvent::create( 'close', json_encode( [ 'time' => time(), 'location' => '/admin' ] ) )
                    ;
                }
                else
                {// (Authorization found)
                    if ( strtotime( $authorization->datetime->expiration ) <= time() )
                    {// (Authorization is not valid)
                        // (Deleting the record)
                        AuthorizationModel::fetch()->delete( ( new Condition() )->filter( [ [ 'token' => $authorization->token ] ] ) );
                    }
                }
            }
        )
        ;
    }
}



?>