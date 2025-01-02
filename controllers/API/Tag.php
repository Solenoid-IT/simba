<?php



namespace App\Controllers\API;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;

use \Solenoid\RPC\Action;

use \Solenoid\MySQL\DateTime;

use \App\Models\local\simba_db\User as UserModel;
use \App\Models\local\simba_db\Tag as TagModel;
use \App\Models\local\simba_db\Activity as ActivityModel;

use \App\Stores\Session as SessionStore;

use \App\Services\User as UserService;
use \App\Services\Client as ClientService;



class Tag extends Controller
{
    # Returns [void]
    public static function insert ()
    {
        // (Getting the value)
        $request = Request::fetch();



        // (Verifying the user)
        $response = UserService::verify( 1 );

        if ( $response->status->code !== 200 )
        {// (Verification is failed)
            // Returning the value
            return Server::send( $response );
        }



        // (Getting the value)
        $session = SessionStore::get( 'user' );



        // (Getting the value)
        $user_id = $session->data['user'];



        // (Getting the value)
        $user = UserModel::fetch()->where( 'id', $user_id )->find();

        if ( !$user )
        {// Value not found
            // Returning the value
            return Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) );
        }



        // (Getting the value)
        $input = json_decode( $request->body, true );



        if ( TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'name', $input['name'] ] ] )->exists() )
        {// (Record found)
            // Returning the value
            return Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','name'] already exists (" . Action::$class . ")" ] ] ) );
        }



        // (Getting the value)
        $record =
        [
            'tenant'                  => $user->tenant,
            'name'                    => $input['name'],

            'owner'                   => $user->id,

            'value'                   => $input['value'],
            'color'                   => $input['color'],

            'datetime.insert'         => DateTime::fetch(),
            'datetime.update'         => null
        ]
        ;

        if ( !TagModel::fetch()->insert( [ $record ] ) )
        {// (Unable to insert the record)
            // Returning the value
            return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (' . Action::$class . ')' ] ] ) );
        }



        // (Getting the value)
        $resource_id = TagModel::fetch()->fetch_ids()[0];



        // (Getting the values)
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];



        // (Getting the value)
        $response = ClientService::detect( $ip, $ua );

        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
        {// (Unable to detect the client)
            // Returning the value
            return Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) );
        }



        // (Getting the value)
        $record =
        [
            'user'                 => $user_id,
            'action'               => $request->headers['Action'],
            'description'          => 'Tag has been created',
            'session'              => $session->id,
            'ip'                   => $ip,
            'user_agent'           => $ua,
            'ip_info.country.code' => $response->body['ip']['country']['code'],
            'ip_info.country.name' => $response->body['ip']['country']['name'],
            'ip_info.isp'          => $response->body['ip']['isp'],
            'ua_info.browser'      => $response->body['ua']['browser'],
            'ua_info.os'           => $response->body['ua']['os'],
            'ua_info.hw'           => $response->body['ua']['hw'],
            'resource.action'      => 'insert',
            'resource.type'        => 'tag',
            'resource.id'          => $resource_id,
            'resource.key'         => $record['name'],
            'datetime.insert'      => DateTime::fetch()
        ]
        ;

        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
        {// (Unable to insert the record)
            // Returning the value
            return Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) );
        }



        // Returning the value
        return Server::send( new Response( new Status(200), [ 'Content-Type: application/json' ], $resource_id ) );
    }

    # Returns [void]
    public static function update ()
    {
        // (Getting the value)
        $request = Request::fetch();



        // (Verifying the user)
        $response = UserService::verify( 1 );

        if ( $response->status->code !== 200 )
        {// (Verification is failed)
            // Returning the value
            return Server::send( $response );
        }



        // (Getting the value)
        $session = SessionStore::get( 'user' );



        // (Getting the value)
        $user_id = $session->data['user'];



        // (Getting the value)
        $user = UserModel::fetch()->where( 'id', $user_id )->find();

        if ( !$user )
        {// Value not found
            // Returning the value
            return Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) );
        }



        // (Getting the value)
        $input = json_decode( $request->body, true );



        if ( TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'name', $input['name'] ], [ 'owner', '<>', $user->id ] ] )->exists() )
        {// (Record found)
            // Returning the value
            return Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','name'] already exists (" . Action::$class . ")" ] ] ) );
        }



        // (Getting the value)
        $record =
        [
            'name'            => $input['name'],

            'value'           => $input['value'],
            'color'           => $input['color'],

            'datetime.update' => DateTime::fetch()
        ]
        ;

        if ( !TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $input['id'] ] ] )->bind( $object, [ 'name' ] )->update( $record ) )
        {// (Unable to update the record)
            // Returning the value
            return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to update the record (' . Action::$class . ')' ] ] ) );
        }



        // (Getting the values)
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];



        // (Getting the value)
        $response = ClientService::detect( $ip, $ua );

        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
        {// (Unable to detect the client)
            // Returning the value
            return Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) );
        }



        // (Getting the value)
        $record =
        [
            'user'                 => $user_id,
            'action'               => $request->headers['Action'],
            'description'          => 'Tag has been changed',
            'session'              => $session->id,
            'ip'                   => $ip,
            'user_agent'           => $ua,
            'ip_info.country.code' => $response->body['ip']['country']['code'],
            'ip_info.country.name' => $response->body['ip']['country']['name'],
            'ip_info.isp'          => $response->body['ip']['isp'],
            'ua_info.browser'      => $response->body['ua']['browser'],
            'ua_info.os'           => $response->body['ua']['os'],
            'ua_info.hw'           => $response->body['ua']['hw'],
            'resource.action'      => 'update',
            'resource.type'        => Action::$class,
            'resource.id'          => $input['id'],
            'resource.key'         => $object->name,
            'datetime.insert'      => DateTime::fetch()
        ]
        ;

        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
        {// (Unable to insert the record)
            // Returning the value
            return Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) );
        }



        // Returning the value
        return Server::send( new Response( new Status(200) ) );
    }

    # Returns [void]
    public static function delete ()
    {
        // (Getting the value)
        $request = Request::fetch();



        // (Verifying the user)
        $response = UserService::verify( 1 );

        if ( $response->status->code !== 200 )
        {// (Verification is failed)
            // Returning the value
            return Server::send( $response );
        }



        // (Getting the value)
        $session = SessionStore::get( 'user' );



        // (Getting the value)
        $user_id = $session->data['user'];



        // (Getting the value)
        $user = UserModel::fetch()->where( 'id', $user_id )->find();

        if ( !$user )
        {// Value not found
            // Returning the value
            return Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) );
        }



        // (Getting the value)
        $input = json_decode( $request->body, true );

        foreach ( $input as $id )
        {// Processing each entry
            #if ( !TagModel::fetch()->where( 'id', 'IN', $input )->delete() )
            if ( !TagModel::fetch()->where( [ ['tenant', $user->tenant ], [ 'id', $id ] ] )->bind( $object, [ 'name' ] )->delete() )
            {// (Unable to delete the record)
                // Returning the value
                return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to delete the record (' . Action::$class . ')' ] ] ) );
            }



            // (Getting the values)
            $ip = $_SERVER['REMOTE_ADDR'];
            $ua = $_SERVER['HTTP_USER_AGENT'];



            // (Getting the value)
            $response = ClientService::detect( $ip, $ua );

            if ( !in_array( $response->status->code, [ 200, 401 ] ) )
            {// (Unable to detect the client)
                // Returning the value
                return Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) );
            }



            // (Getting the value)
            $record =
            [
                'user'                 => $user_id,
                'action'               => $request->headers['Action'],
                'description'          => 'Tag has been removed',
                'session'              => $session->id,
                'ip'                   => $ip,
                'user_agent'           => $ua,
                'ip_info.country.code' => $response->body['ip']['country']['code'],
                'ip_info.country.name' => $response->body['ip']['country']['name'],
                'ip_info.isp'          => $response->body['ip']['isp'],
                'ua_info.browser'      => $response->body['ua']['browser'],
                'ua_info.os'           => $response->body['ua']['os'],
                'ua_info.hw'           => $response->body['ua']['hw'],
                'resource.action'      => 'delete',
                'resource.type'        => Action::$class,
                'resource.id'          => $id,
                'resource.key'         => $object->name,
                'datetime.insert'      => DateTime::fetch()
            ]
            ;

            if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
            {// (Unable to insert the record)
                // Returning the value
                return Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) );
            }
        }



        // Returning the value
        return Server::send( new Response( new Status(200) ) );
    }

    # Returns [void]
    public static function find ()
    {
        // (Getting the value)
        $request = Request::fetch();



        // (Verifying the user)
        $response = UserService::verify( 1 );

        if ( $response->status->code !== 200 )
        {// (Verification is failed)
            // Returning the value
            return Server::send( $response );
        }



        // (Getting the value)
        $session = SessionStore::get( 'user' );



        // (Getting the value)
        $user_id = $session->data['user'];



        // (Getting the value)
        $user = UserModel::fetch()->where( 'id', $user_id )->find();

        if ( !$user )
        {// Value not found
            // Returning the value
            return Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) );
        }



        // (Getting the value)
        $input = json_decode( $request->body, true );



        // (Getting the value)
        $resource = TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $input['id'] ] ] )->find();

        if ( !$resource )
        {// (Record not found)
            // Returning the value
            return Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (' . Action::$class . ')' ] ] ) );
        }



        // Returning the value
        return Server::send( new Response( new Status(200), [], $resource ) );
    }
}



?>