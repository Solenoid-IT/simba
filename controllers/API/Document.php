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
use \App\Models\local\simba_db\Document as DocumentModel;
use \App\Models\local\simba_db\Tag as TagModel;
use \App\Models\local\simba_db\DocumentTag as DocumentTagModel;
use \App\Models\local\simba_db\DocumentTagView as DocumentTagViewModel;
use \App\Models\local\simba_db\Activity as ActivityModel;

use \App\Stores\Session as SessionStore;

use \App\Services\User as UserService;
use \App\Services\Client as ClientService;



class Document extends Controller
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



        if ( DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'path', $input['path'] ] ] )->exists() )
        {// (Record found)
            // Returning the value
            return Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','path'] already exists (" . Action::$class . ")" ] ] ) );
        }



        // (Getting the value)
        $record =
        [
            'tenant'                  => $user->tenant,
            'path'                    => $input['path'],

            'owner'                   => $user->id,

            'title'                   => $input['title'],
            'description'             => $input['description'],

            'content'                 => $input['content'],

            'datetime.insert'         => DateTime::fetch(),
            'datetime.update'         => null,

            'datetime.option.active'  => null,
            'datetime.option.sitemap' => null
        ]
        ;

        if ( !DocumentModel::fetch()->insert( [ $record ] ) )
        {// (Unable to insert the record)
            // Returning the value
            return
                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (' . Action::$class . ')' ] ] ) )
            ;
        }



        // (Getting the value)
        $resource_id = DocumentModel::fetch()->fetch_ids()[0];



        if ( !DocumentTagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'document', $resource_id ] ] )->delete() )
        {// (Unable to delete the records)
            // Returning the value
            return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to delete the records (document_tag)' ] ] ) );
        }



        foreach ( $input['tags'] as $id )
        {// Processing each entry
            if ( !TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $id ] ] )->exists() )
            {// (Record not found)
                // Returning the value
                return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Record not found (tag)' ] ] ) );
            }



            // (Getting the value)
            $r =
            [
                'tenant'   => $user->tenant,

                'document' => $resource_id,
                'tag'      => $id
            ]
            ;

            if ( !DocumentTagModel::fetch()->insert( [ $r ] ) )
            {// (Unable to insert the record)
                // Returning the value
                return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (document_tag)' ] ] ) );
            }
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
            'description'          => 'Document has been created',
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
            'resource.type'        => Action::$class,
            'resource.id'          => $resource_id,
            'resource.key'         => $record['path'],
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



        if ( DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'path', $input['path'] ], [ 'owner', '<>', $user->id ] ] )->exists() )
        {// (Record found)
            // Returning the value
            return Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','path'] already exists (" . Action::$class . ")" ] ] ) );
        }



        // (Getting the value)
        $record =
        [
            'path'                    => $input['path'],

            'title'                   => $input['title'],
            'description'             => $input['description'],

            'content'                 => $input['content'],

            'datetime.update'         => DateTime::fetch()
        ]
        ;

        if ( !DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $input['id'] ] ] )->bind( $object, [ 'path' ] )->update( $record ) )
        {// (Unable to update the record)
            // Returning the value
            return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to update the record (' . Action::$class . ')' ] ] ) );
        }



        if ( !DocumentTagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'document', $input['id'] ] ] )->delete() )
        {// (Unable to delete the records)
            // Returning the value
            return
                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to delete the records (document_tag)' ] ] ) )
            ;
        }



        foreach ( $input['tags'] as $id )
        {// Processing each entry
            if ( !TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $id ] ] )->exists() )
            {// (Record not found)
                // Returning the value
                return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Record not found (tag)' ] ] ) );
            }



            // (Getting the value)
            $r =
            [
                'tenant'   => $user->tenant,

                'document' => $input['id'],
                'tag'      => $id
            ]
            ;

            if ( !DocumentTagModel::fetch()->insert( [ $r ] ) )
            {// (Unable to insert the record)
                // Returning the value
                return Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (document_tag)' ] ] ) );
            }
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
            'description'          => 'Document has been changed',
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
            'resource.key'         => $object->path,
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
            if ( !DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $id ] ] )->bind( $object, [ 'path' ] )->delete() )
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
                'description'          => 'Document has been removed',
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
                'resource.key'         => $object->path,
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
        $resource = DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $input['id'] ] ] )->find();

        if ( !$resource )
        {// (Record not found)
            // Returning the value
            return Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (' . Action::$class . ')' ] ] ) );
        }



        // (Getting the value)
        $tags = DocumentTagViewModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'document', $resource->id ] ] )->list
        (
            transform_record: function ($record)
            {
                // Returning the value
                return
                [
                    'id'    => $record->tag->id,

                    'name'  => $record->tag->name,
                    'value' => $record->tag->value
                ]
                ;
            }
        )
        ;



        // (Getting the value)
        $resource->tags = $tags;



        // Returning the value
        return Server::send( new Response( new Status(200), [], $resource ) );
    }

    # Returns [void]
    public static function set_option ()
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



        if ( !in_array( $input['option'], [ 'active', 'sitemap' ] ) )
        {// Match failed
            // Returning the value
            return Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => "Invalid value for property 'option'" ] ] ) );
        }



        // (Getting the value)
        $time = time();



        // (Getting the value)
        $record =
        [
            'datetime.option.' . $input['option'] => $input['value'] ? DateTime::fetch( $time ) : null,

            'datetime.update'                     => DateTime::fetch( $time )
        ]
        ;

        if ( !DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $input['id'] ] ] )->bind( $object, [ 'path' ] )->update( $record ) )
        {// (Unable to update the record)
            // Returning the value
            return Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => 'Unable to update the record (' . Action::$class . ')' ] ] ) );
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
            'description'          => "Document has been changed :: Option '" . $input['option'] . "' has been " . ( $input['value'] ? 'enabled' : 'disabled' ),
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
            'resource.key'         => $object->path,
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
}



?>