<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;
use \Solenoid\Core\Credentials;
use \Solenoid\Core\App\App;

use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Status;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Client\Client as HttpClient;

use \Solenoid\MySQL\DateTime;

use \Solenoid\Core\App\WebApp;

use \Solenoid\Encryption\KeyPair;
use \Solenoid\Encryption\RSA;
use \Solenoid\IDK\IDK;

use \Solenoid\RPC\Request as RPCRequest;

use \App\Middlewares\RPC\Parser as RPCParser;
use \App\Models\local\simba_db\User as UserModel;
use \App\Models\local\simba_db\Tenant as TenantModel;
use \App\Models\local\simba_db\Activity as ActivityModel;
use \App\Models\local\simba_db\ActivityView as ActivityViewModel;
use \App\Models\local\simba_db\Hierarchy as HierarchyModel;
use \App\Models\local\simba_db\Session as SessionModel;
use \App\Models\local\simba_db\Document as DocumentModel;
use \App\Models\local\simba_db\Tag as TagModel;
use \App\Models\local\simba_db\DocumentTag as DocumentTagModel;
use \App\Models\local\simba_db\DocumentTagView as DocumentTagViewModel;
use \App\Services\Authorization as AuthorizationService;
use \App\Services\User as UserService;
use \App\Services\Client as ClientService;
use \App\Services\Login as LoginService;
use \App\Stores\Sessions\Store as SessionsStore;
use \App\Stores\Cookies\Store as CookiesStore;



class API extends Controller
{
    # Returns [void]
    public function rpc ()
    {
        // (Getting the value)
        $request = Request::fetch();

        if ( false && $request->cookies['fwd_route'] )
        {// Value found
            // Returning the value
            return
                Server::send( new Response( new Status(200), [], [ 'redirect' => $request->cookies['fwd_route'] ] ) )
            ;
        }



        switch ( RPCParser::$subject )
        {
            case '':
                switch ( RPCParser::$verb )
                {
                    case 'test':
                        // Returning the value
                        return
                            Server::send( new Response( new Status(200), [], RPCRequest::fetch()->parse_body() ) )
                        ;
                    break;
                }
            break;

            case 'user':
                switch ( RPCParser::$verb )
                {
                    case 'register':
                        if ( $request->client_ip !== $request->server_ip )
                        {// (Request is not from localhost)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                            ;
                        }
            


                        if ( $request->headers['Auth-Token'] )
                        {// (Authorization has been provided)
                            // (Getting the value)
                            $response = AuthorizationService::fetch( $request->headers['Auth-Token'] );

                            if ( $response->status->code !== 200 )
                            {// (Unable to fetch the authorization)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $authorization = $response->body;



                            if ( $authorization->data['request']['input']['tenant']['id'] )
                            {// Value found
                                // (Getting the value)
                                $tenant_id = $authorization->data['request']['input']['tenant']['id'];
                            }
                            else
                            {// Value not found
                                // (Getting the value)
                                $tenant = TenantModel::fetch()->where( 'name', $authorization->data['request']['input']['tenant']['name'] )->find();

                                if ( $tenant === false )
                                {// (Record not found)
                                    // (Getting the value)
                                    $record =
                                    [
                                        'name'            => $authorization->data['request']['input']['tenant']['name'],

                                        'datetime.insert' => DateTime::fetch(),
                                        'datetime.update' => null
                                    ]
                                    ;

                                    if ( TenantModel::fetch()->insert( [ $record ] ) === false )
                                    {// (Unable to insert the record)
                                        // Returning the value
                                        return
                                            Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (tenant)' ] ] ) )
                                        ;
                                    }



                                    // (Getting the value)
                                    $tenant_id = TenantModel::fetch()->fetch_ids()[0];
                                }
                                else
                                {// (Record found)
                                    // (Getting the value)
                                    $tenant_id = $tenant->id;
                                }
                            }



                            if ( UserModel::fetch()->where( [ [ 'tenant', $tenant_id ], [ 'name', $authorization->data['request']['input']['user']['name'] ] ] )->exists() )
                            {// (Record found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','name'] already exists (user)" ] ] ) )
                                ;
                            }

                            if ( UserModel::fetch()->where( 'email', $authorization->data['request']['input']['user']['email'] )->exists() )
                            {// (Record found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['email'] already exists (user)" ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $record = $authorization->data['request']['input']['user'];
                            $record =
                            [
                                'tenant'          => $tenant_id,

                                'name'            => $record['name'],

                                'email'           => $record['email'],

                                'hierarchy'       => $record['hierarchy'],

                                'datetime.insert' => DateTime::fetch(),
                                'datetime.update' => null
                            ]
                            ;

                            if ( UserModel::fetch()->insert( [ $record ] ) === false )
                            {// (Unable to insert the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (user)' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $user_id = UserModel::fetch()->fetch_ids()[0];



                            if ( $authorization->data['request']['input']['client'] )
                            {// Value found
                                // (Getting the value)
                                $response = ClientService::fetch_real_session_id
                                (
                                    $authorization->data['request']['input']['client']['user'],
                                    $authorization->data['request']['input']['client']['session']
                                )
                                ;

                                if ( $response->status->code !== 200 )
                                {// (Unable to fetch the real session id)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(500), [], [ 'Unable to fetch the real session id' ] ) )
                                    ;
                                }



                                // (Getting the value)
                                $session_id = $response->body['session_id'];



                                // (Getting the values)
                                $ip = $authorization->data['request']['input']['client']['ip'];
                                $ua = $authorization->data['request']['input']['client']['ua'];



                                // (Getting the value)
                                $response = ClientService::detect( $ip, $ua );
        
                                if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                                {// (Unable to detect the client)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                    ;
                                }
        
        
        
                                // (Getting the value)
                                $record =
                                [
                                    'user'                 => $authorization->data['request']['input']['client']['user'],
                                    'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                    'description'          => "User has been created",
                                    'session'              => $session_id,
                                    'ip'                   => $ip,
                                    'user_agent'           => $ua,
                                    'ip_info.country.code' => $response->body['ip']['country']['code'],
                                    'ip_info.country.name' => $response->body['ip']['country']['name'],
                                    'ip_info.isp'          => $response->body['ip']['isp'],
                                    'ua_info.browser'      => $response->body['ua']['browser'],
                                    'ua_info.os'           => $response->body['ua']['os'],
                                    'ua_info.hw'           => $response->body['ua']['hw'],
                                    'resource.action'      => 'insert',
                                    'resource.type'        => 'user',
                                    'resource.id'          => $user_id,
                                    'resource.key'         => $record['name'],
                                    'datetime.insert'      => DateTime::fetch()
                                ]
                                ;
                            }
                            else
                            {// Value not found
                                // (Getting the value)
                                $record =
                                [
                                    'user'                 => $user_id,
                                    'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                    'description'          => "User has been created",
                                    'session'              => null,
                                    'ip'                   => $_SERVER['REMOTE_ADDR'],
                                    'user_agent'           => $_SERVER['HTTP_USER_AGENT'],
                                    'ip_info.country.code' => null,
                                    'ip_info.country.name' => null,
                                    'ip_info.isp'          => null,
                                    'ua_info.browser'      => null,
                                    'ua_info.os'           => null,
                                    'ua_info.hw'           => null,
                                    'resource.action'      => 'insert',
                                    'resource.type'        => 'user',
                                    'resource.id'          => $user_id,
                                    'resource.key'         => $record['name'],
                                    'datetime.insert'      => DateTime::fetch()
                                ]
                                ;
                            }



                            if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                            {// (Unable to insert the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                ;
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200), [ 'Content-Type: application/json' ], $user_id ) )
                            ;
                        }
                        else
                        {// (Authorization has not been provided)
                            // (Getting the value)
                            $input = RPCRequest::fetch()->parse_body();



                            // (Starting an authorization)
                            $response = AuthorizationService::start
                            (
                                [
                                    'request'           =>
                                    [
                                        'endpoint_path' => $request->url->path,
                                        'action'        => $request->headers['Action'],
                                        'input'         => $input
                                    ],

                                    'login'             => true
                                ],

                                $request->url->fetch_base() . '/admin/dashboard'
                            )
                            ;
                            
                            if ( $response->status->code !== 200 )
                            {// (Unable to start the authorization)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the authorization' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $token = $response->body['token'];



                            // (Sending the authorization)
                            $response = AuthorizationService::send
                            (
                                $token,
                                $input['user']['email'],
                                RPCParser::$subject . '.' . RPCParser::$verb,
                                
                                $input['client']['ip'] ?? null,
                                $input['client']['ua'] ?? null
                            )
                            ;
                            
                            if ( $response->status->code !== 200 )
                            {// (Unable to send the authorization)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to send the authorization' ] ] ) )
                                ;
                            }



                            // Returning the value
                            return Server::send( new Response( new Status(200), [], [ 'token' => $token ] ) );
                        }
                    break;

                    case 'destroy':
                        // (Getting the value)
                        $request = Request::fetch();



                        if ( $request->headers['Auth-Token'] )
                        {// (Authorization has been provided)
                            if ( $request->client_ip !== $request->server_ip )
                            {// (Request is not from localhost)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $response = AuthorizationService::fetch( $request->headers['Auth-Token'] );

                            if ( $response->status->code !== 200 )
                            {// (Unable to fetch the authorization)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $authorization = $response->body;



                            // (Getting the value)
                            $user_id = $authorization->data['request']['input']['user'];

                            if ( !UserModel::fetch()->where( 'id', $user_id )->delete() )
                            {// (Unable to delete the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to delete the record (user)' ] ] ) )
                                ;
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200) ) )
                            ;
                        }
                        else
                        {// (Authorization has not been provided)
                            // (Verifying the user)
                            $response = UserService::verify();

                            if ( $response->status->code !== 200 )
                            {// (Session is not valid)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $session = SessionsStore::fetch()->sessions['user'];
    
    
    
                            // (Getting the value)
                            $user_id = $session->data['user'];
    
    
    
                            // (Getting the value)
                            $user = UserModel::fetch()->where( 'id', $user_id )->find();
    
                            if ( !$user )
                            {// (Record not found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                ;
                            }



                            // (Starting an authorization)
                            $response = AuthorizationService::start
                            (
                                [
                                    'request'           =>
                                    [
                                        'endpoint_path' => $request->url->path,
                                        'action'        => $request->headers['Action'],
                                        'input'         =>
                                        [
                                            'user'      => $user_id
                                        ]
                                    ]
                                ]
                            )
                            ;
                            
                            if ( $response->status->code !== 200 )
                            {// (Unable to start the authorization)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the authorization' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $token = $response->body['token'];



                            // (Sending the authorization)
                            $response = AuthorizationService::send( $token, $user->email, RPCParser::$subject . '.' . RPCParser::$verb );
                            
                            if ( $response->status->code !== 200 )
                            {// (Unable to send the authorization)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to send the authorization' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $session->data['authorization'] = $token;



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200) ) )
                            ;
                        }
                    break;

                    case 'fetch_data':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Setting the value)
                        $data = [];



                        switch ( $request->headers['Route'] )
                        {
                            case '/admin/dashboard':
                            case '/admin/activity_log':
                            case '/admin/users':
                            case '/admin/documents':
                            case '/admin/tags':
                                // (Getting the value)
                                $response = UserService::fetch_data( $user_id );

                                if ( $response->status->code !== 200 )
                                {// (Unable to fetch the data)
                                    // Returning the value
                                    return
                                        Server::send( $response )
                                    ;
                                }



                                // (Getting the value)
                                $data['user'] = $response->body;



                                // (Getting the value)
                                $hierarchies = HierarchyModel::fetch()->list();

                                foreach ( $hierarchies as $hierarchy )
                                {// Processing each entry
                                    // (Getting the value)
                                    $data['hierarchies'][ $hierarchy->id ] = $hierarchy;
                                }



                                // (Getting the value)
                                $data['alerts'] = ActivityModel::fetch()->where( [ [ 'user', $user_id ], [ 'alert_severity', 'IS NOT', null ], [ 'datetime.alert.read', 'IS', null ] ] )->list();
                            break;
                        }



                        switch ( $request->headers['Route'] )
                        {
                            case '/admin/activity_log':
                                // (Getting the value)
                                $user = UserModel::fetch()->where( 'id', $user_id )->find();

                                if ( !$user )
                                {// (Record not found)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                    ;
                                }



                                if ( $user->hierarchy === 1 )
                                {// (User is an admin)
                                    // (Getting the value)
                                    $data['records'] = ActivityViewModel::fetch()->where( 'user.tenant', $user->tenant )->list();
                                }
                                else
                                {// (User is not an admin)
                                    // (Getting the value)
                                    $data['records'] = ActivityModel::fetch()->where( 'user', $user_id )->list();
                                }
                        


                                foreach ( $data['records'] as &$record )
                                {// Processing each entry
                                    // (Getting the value)
                                    $record = (array) $record;



                                    // (Removing the element)
                                    unset( $record['resource']->id );



                                    // (Getting the value)
                                    $record['current_session'] = $record['session'] && $record['session'] === $session->found_id;
                                }
                            break;

                            case '/admin/users':
                                // (Getting the value)
                                $user = UserModel::fetch()->where( 'id', $user_id )->find();

                                if ( !$user )
                                {// (Record not found)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                    ;
                                }
                            


                                // (Getting the value)
                                $data['records'] = UserModel::fetch()->where( 'tenant', $user->tenant )->list
                                (
                                    transform_record: function ($record)
                                    {
                                        // (Getting the value)
                                        $record =
                                        [
                                            'id'          => $record->id,

                                            'name'        => $record->name,
                                            'email'       => $record->email,

                                            'hierarchy'   => $record->hierarchy,

                                            'birth'       =>
                                            [
                                                'name'    => $record->birth->name,
                                                'surname' => $record->birth->surname,
                                            ],

                                            'datetime'    =>
                                            [
                                                'insert'  => $record->datetime->insert
                                            ]
                                        ]
                                        ;



                                        // Returning the value
                                        return $record;
                                    }
                                )
                                ;
                            break;

                            case '/admin/documents':
                                // (Getting the value)
                                $user = UserModel::fetch()->where( 'id', $user_id )->find();

                                if ( !$user )
                                {// (Record not found)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                    ;
                                }
                            


                                // (Getting the value)
                                $data['records'] = DocumentModel::fetch()->where( 'tenant', $user->tenant )->list
                                (
                                    transform_record: function ($record) use ($user)
                                    {
                                        // (Getting the value)
                                        $record =
                                        [
                                            'id'              => $record->id,

                                            'path'            => $record->path,

                                            'title'           => $record->title,
                                            'description'     => $record->description,

                                            'datetime'        =>
                                            [
                                                'insert'      => $record->datetime->insert,
                                                'update'      => $record->datetime->update,

                                                'option'      =>
                                                [
                                                    'active'  => $record->datetime->option->active,
                                                    'sitemap' => $record->datetime->option->sitemap,
                                                ]
                                            ],

                                            'tags'            => DocumentTagViewModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'document', $record->id ] ] )->list
                                            (
                                                transform_record: function ($record)
                                                {
                                                    // Returning the value
                                                    return
                                                        [
                                                            'id'    => $record->tag->id,

                                                            'name'  => $record->tag->name,
                                                            'value' => $record->tag->value,
                                                            'color' => $record->tag->color
                                                        ]
                                                    ;
                                                }
                                            )
                                        ]
                                        ;



                                        // Returning the value
                                        return $record;
                                    }
                                )
                                ;



                                // (Getting the value)
                                $data['tags'] = TagModel::fetch()->where( 'tenant', $user->tenant )->list();
                            break;

                            case '/admin/tags':
                                // (Getting the value)
                                $user = UserModel::fetch()->where( 'id', $user_id )->find();

                                if ( !$user )
                                {// (Record not found)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                    ;
                                }
                            


                                // (Getting the value)
                                $data['records'] = TagModel::fetch()->where( 'tenant', $user->tenant )->list();
                            break;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200), [], $data ) )
                        ;
                    break;

                    case 'change_password':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        // (Getting the value)
                        $record =
                        [
                            'security.password' => password_hash( $input['password'], PASSWORD_BCRYPT ),

                            'datetime.update'   => DateTime::fetch()
                        ]
                        ;

                        if ( UserModel::fetch()->where( 'id', $user_id )->update( $record ) === false )
                        {// (Unable to update the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to update the record (user)" ] ] ) )
                            ;
                        }



                        // (Getting the values)
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ua = $_SERVER['HTTP_USER_AGENT'];



                        // (Getting the value)
                        $response = ClientService::detect( $ip, $ua );

                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                        {// (Unable to detect the client)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                            'description'          => 'Password has been changed',
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
                            'resource.type'        => 'user',
                            'resource.id'          => $user_id,
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'change_mfa':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        // (Getting the value)
                        $record =
                        [
                            'security.mfa'    => $input['security.mfa'],

                            'datetime.update' => DateTime::fetch()
                        ]
                        ;

                        if ( UserModel::fetch()->where( 'id', $user_id )->update( $record ) === false )
                        {// (Unable to update the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to update the record (user)" ] ] ) )
                            ;
                        }



                        // (Getting the values)
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ua = $_SERVER['HTTP_USER_AGENT'];



                        // (Getting the value)
                        $response = ClientService::detect( $ip, $ua );

                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                        {// (Unable to detect the client)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                            'description'          => 'MFA has been ' . ( $input['security.mfa'] ? 'enabled ' : 'disabled' ),
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
                            'resource.type'        => 'user',
                            'resource.id'          => $user_id,
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'change_idk':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        // (Getting the value)
                        $idk_authentication = $input['security.idk.authentication'];

                        if ( $idk_authentication )
                        {// Value is true
                            // (Getting the value)
                            $key_pair = KeyPair::generate();

                            if ( $key_pair === false )
                            {// (Unable to generate a key-pair)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to generate a key-pair' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $idk = ( new IDK( $user_id, $key_pair->private_key ) )->build( Credentials::fetch( '/system/data.json' )['idk']['passphrase'], true );

                            if ( $idk === false )
                            {// (Unable to build the IDK)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to build the IDK' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $record =
                            [
                                'security.idk.authentication' => $idk_authentication,
                                'security.idk.public_key'     => $key_pair->public_key,
                                'security.idk.signature'      => base64_encode( RSA::select( 'idk' )->encrypt( $key_pair->public_key ) ),

                                'datetime.update'             => DateTime::fetch()
                            ]
                            ;
                        }
                        else
                        {// Value is false
                            // (Setting the value)
                            $idk = '';



                            // (Getting the value)
                            $record =
                            [
                                'security.idk.authentication' => $idk_authentication,
                                'security.idk.public_key'     => null,
                                'security.idk.signature'      => null,

                                'datetime.update'             => DateTime::fetch()
                            ]
                            ;
                        }



                        if ( UserModel::fetch()->where( 'id', $user_id )->update( $record ) === false )
                        {// (Unable to update the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to update the record (user)" ] ] ) )
                            ;
                        }



                        // (Getting the values)
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ua = $_SERVER['HTTP_USER_AGENT'];



                        // (Getting the value)
                        $response = ClientService::detect( $ip, $ua );

                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                        {// (Unable to detect the client)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                            'description'          => 'IDK has been ' . ( $input['security.idk.authentication'] ? 'enabled' : 'disabled' ),
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
                            'resource.type'        => 'user',
                            'resource.id'          => $user_id,
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200), [ 'Content-Type: text/plain' ], $idk ) )
                        ;
                    break;

                    case 'login':
                        // (Getting the value)
                        $request = Request::fetch();



                        if ( $request->headers['Auth-Token'] )
                        {// Value found
                            if ( $request->client_ip !== $request->server_ip )
                            {// (Request is not from localhost)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $res = AuthorizationService::fetch( $request->headers['Auth-Token'] );

                            if ( $res->status->code !== 200 )
                            {// (Unable to fetch the authorization)
                                // Returning the value
                                return
                                    Server::send( $res )
                                ;
                            }



                            // (Getting the value)
                            $authorization = $res->body;



                            // (Getting the value)
                            $record =
                            [
                                'data'            => json_encode
                                (
                                    [
                                        'user'    => $authorization->data['request']['input']['client']['user']
                                    ]
                                ),

                                'datetime.update' => DateTime::fetch()
                            ]
                            ;

                            if ( SessionModel::fetch()->where( 'id', $authorization->data['request']['input']['client']['session'] )->update( $record ) === false )
                            {// (Unable to update the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to update the record (session)' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $response = ClientService::fetch_real_session_id
                            (
                                $authorization->data['request']['input']['client']['user'],
                                $authorization->data['request']['input']['client']['session']
                            )
                            ;

                            if ( $response->status->code !== 200 )
                            {// (Unable to fetch the real session id)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'Unable to fetch the real session id' ] ) )
                                ;
                            }



                            // (Getting the value)
                            $session_id = $response->body['session_id'];



                            // (Getting the values)
                            $ip = $authorization->data['request']['input']['client']['ip'];
                            $ua = $authorization->data['request']['input']['client']['user_agent'];



                            // (Getting the value)
                            $response = ClientService::detect( $ip, $ua );

                            if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                            {// (Unable to detect the client)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $record =
                            [
                                'user'                 => $authorization->data['request']['input']['client']['user'],
                                'action'               => str_replace( '::', '.', $authorization->data['request']['action'] ),
                                'description'          => 'Login via MFA',
                                'session'              => $session_id,
                                'ip'                   => $ip,
                                'user_agent'           => $ua,
                                'ip_info.country.code' => $response->body['ip']['country']['code'],
                                'ip_info.country.name' => $response->body['ip']['country']['name'],
                                'ip_info.isp'          => $response->body['ip']['isp'],
                                'ua_info.browser'      => $response->body['ua']['browser'],
                                'ua_info.os'           => $response->body['ua']['os'],
                                'ua_info.hw'           => $response->body['ua']['hw'],
                                'datetime.insert'      => DateTime::fetch()
                            ]
                            ;

                            if ( !ActivityModel::fetch()->insert( [ $record ] ) )
                            {// (Unable to insert the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                ;
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200) ) )
                            ;
                        }
                        else
                        {// Value not found
                            // (Getting the value)
                            $input = RPCRequest::fetch()->parse_body();



                            // (Getting the values)
                            [ $user, $tenant ] = explode( '@', $input['login'] );
                            $password         = $input['password'];



                            // (Getting the value)
                            $tenant = TenantModel::fetch()->where( 'name', $tenant )->find();

                            if ( !$tenant )
                            {// (Record not found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $user = UserModel::fetch()->where( [ [ 'tenant', $tenant->id ], [ 'name', $user ] ] )->find();

                            if ( !$user )
                            {// (Record not found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            if ( $user->security->idk->authentication )
                            {// Value not found
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            if ( $user->security->password === null )
                            {// Value not found
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }

                            if ( !password_verify( $password, $user->security->password ) )
                            {// Match failed
                                // (Getting the value)
                                $response = ClientService::detect();

                                if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                                {// (Unable to detect the client)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                    ;
                                }



                                // (Getting the value)
                                $record =
                                [
                                    'user'                 => $user->id,
                                    'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                    'description'          => 'Wrong password',
                                    'session'              => null,
                                    'ip'                   => $_SERVER['REMOTE_ADDR'],
                                    'user_agent'           => $_SERVER['HTTP_USER_AGENT'],
                                    'ip_info.country.code' => $response->body['ip']['country']['code'],
                                    'ip_info.country.name' => $response->body['ip']['country']['name'],
                                    'ip_info.isp'          => $response->body['ip']['isp'],
                                    'ua_info.browser'      => $response->body['ua']['browser'],
                                    'ua_info.os'           => $response->body['ua']['os'],
                                    'ua_info.hw'           => $response->body['ua']['hw'],
                                    'alert_severity'       => 0,
                                    'datetime.insert'      => DateTime::fetch()
                                ]
                                ;

                                if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                                {// (Unable to insert the record)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                    ;
                                }



                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $session = SessionsStore::fetch()->sessions['user'];



                            if ( !$session->start() )
                            {// (Unable to start the session)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the session' ] ] ) )
                                ;
                            }

                            if ( !$session->regenerate_id() )
                            {// (Unable to regenerate the session id)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to regenerate the session id' ] ] ) )
                                ;
                            }

                            if ( !$session->set_duration() )
                            {// (Unable to set the session duration)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to set the session duration' ] ] ) )
                                ;
                            }



                            // (Setting the value)
                            $session->data = [];



                            if ( $user->security->mfa === 1 )
                            {// (Login method is MFA)
                                // (Getting the value)
                                $data =
                                [
                                    'request'                =>
                                    [
                                        'endpoint_path'      => $request->url->path,
                                        'action'             => $request->headers['Action'],
                                        'input'              =>
                                        [
                                            'client'         =>
                                            [
                                                'ip'         => $_SERVER['REMOTE_ADDR'],
                                                'user_agent' => $_SERVER['HTTP_USER_AGENT'],

                                                'user'       => $user->id,
                                                'session'    => $session->id
                                            ]
                                        ]
                                    ]
                                ]
                                ;

                                // (Starting the authorization)
                                $response = AuthorizationService::start( $data );

                                if ( $response->status->code !== 200 )
                                {// (Unable to start the authorization)
                                    // Returning the value
                                    return
                                        Server::send( $response )
                                    ;
                                }



                                // (Getting the value)
                                $token = $response->body['token'];



                                // (Sending the authorization)
                                $response = AuthorizationService::send( $token, $user->email, implode( '.', [ RPCParser::$subject, RPCParser::$verb ] ) );

                                if ( $response->status->code !== 200 )
                                {// (Unable to send the authorization)
                                    // Returning the value
                                    return
                                        Server::send( $response )
                                    ;
                                }



                                // (Getting the value)
                                $session->data['authorization'] = $token;



                                // Returning the value
                                return
                                    Server::send( new Response( new Status(200) ) )
                                ;
                            }
                            else
                            {// (Login method is BASIC)
                                // (Getting the value)
                                $session->data['user'] = $user->id;



                                // (Listening for the event)
                                $session->add_event_listener
                                (
                                    'save',
                                    function () use ($user, &$session)
                                    {
                                        // (Getting the values)
                                        $ip = $_SERVER['REMOTE_ADDR'];
                                        $ua = $_SERVER['HTTP_USER_AGENT'];



                                        // (Getting the value)
                                        $response = ClientService::detect( $ip, $ua );

                                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                                        {// (Unable to detect the client)
                                            // Returning the value
                                            return
                                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                            ;
                                        }



                                        // (Getting the value)
                                        $record =
                                        [
                                            'user'                 => $user->id,
                                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                            'description'          => 'Login via BASIC',
                                            'session'              => $session->id,
                                            'ip'                   => $ip,
                                            'user_agent'           => $ua,
                                            'ip_info.country.code' => $response->body['ip']['country']['code'],
                                            'ip_info.country.name' => $response->body['ip']['country']['name'],
                                            'ip_info.isp'          => $response->body['ip']['isp'],
                                            'ua_info.browser'      => $response->body['ua']['browser'],
                                            'ua_info.os'           => $response->body['ua']['os'],
                                            'ua_info.hw'           => $response->body['ua']['hw'],
                                            'datetime.insert'      => DateTime::fetch()
                                        ]
                                        ;

                                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                                        {// (Unable to insert the record)
                                            // Returning the value
                                            return
                                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                            ;
                                        }
                                    }
                                )
                                ;



                                // Returning the value
                                return
                                    Server::send( new Response( new Status(200), [], [ 'location' => LoginService::extract_location() ] ) )
                                ;
                            }
                        }
                    break;

                    case 'login_wait':
                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];

                        if ( !$session->start() )
                        {// (Unable to start the session)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the session' ] ] ) )
                            ;
                        }

                        
 
                        // (Getting the value)
                        $token = $session->data['authorization'];

                        if ( !$token )
                        {// Value not found
                            if ( !$session->destroy() )
                            {// (Unable to destroy the session)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to destroy the session' ] ] ) )
                                ;
                            }


                            // Returning the value
                            return
                                Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                            ;
                        }



                        // (Setting the time limit)
                        set_time_limit(60);



                        while (true)
                        {// Processing each clock
                            // (Getting the value)
                            $response = AuthorizationService::fetch( $token );

                            if ( $response->status->code === 404 )
                            {// (Authorization not found)
                                // (Closing the session)
                                $session->close();



                                // (Getting the value)
                                $location = LoginService::extract_location();



                                // (Removing the cookie)
                                CookiesStore::fetch()->cookies['fwd_route']->set( '', -1 );



                                // Returning the value
                                return
                                    Server::send( new Response( new Status(200), [], [ 'location' => $location ] ) )
                                ;
                            }



                            // (Waiting for the time)
                            sleep(2);
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(408) ) )
                        ;
                    break;

                    case 'login_with_idk':
                        // (Getting the value)
                        $idk = IDK::read( $request->body, Credentials::fetch( '/system/data.json' )['idk']['passphrase'], true );



                        if ( $idk->user )
                        {// Value found
                            // (Getting the value)
                            $user = UserModel::fetch()->where( 'id', $idk->user )->find();
                        }
                        else
                        if ( $idk->data['username'] )
                        {// Value found
                            // (Getting the value)
                            $user = UserModel::fetch()->where( 'username', $idk->data['username'] )->find();
                        }
                        else
                        {// Match failed
                            // Returning the value
                            return
                                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'IDK is not valid' ] ] ) )
                            ;
                        }



                        if ( $user === false )
                        {// (User not found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                            ;
                        }



                        if ( !$user->security->idk->authentication === 1 )
                        {// Value is false
                            // Returning the value
                            return
                                Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                            ;
                        }



                        if ( RSA::select( base64_decode( $user->security->idk->signature ) )->decrypt( $idk->key )->value !== 'idk' )
                        {// (Key is not valid)
                            // (Getting the value)
                            $response = ClientService::detect();

                            if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                            {// (Unable to detect the client)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $record =
                            [
                                'user'                 => $user->id,
                                'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                'description'          => 'Wrong key',
                                'session'              => null,
                                'ip'                   => $_SERVER['REMOTE_ADDR'],
                                'user_agent'           => $_SERVER['HTTP_USER_AGENT'],
                                'ip_info.country.code' => $response->body['ip']['country']['code'],
                                'ip_info.country.name' => $response->body['ip']['country']['name'],
                                'ip_info.isp'          => $response->body['ip']['isp'],
                                'ua_info.browser'      => $response->body['ua']['browser'],
                                'ua_info.os'           => $response->body['ua']['os'],
                                'ua_info.hw'           => $response->body['ua']['hw'],
                                'alert_severity'       => 0,
                                'datetime.insert'      => DateTime::fetch()
                            ]
                            ;

                            if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                            {// (Unable to insert the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                ;
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
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



                        // (Listening for the event)
                        $session->add_event_listener
                        (
                            'save',
                            function () use ($user, &$session)
                            {
                                // (Getting the values)
                                $ip = $_SERVER['REMOTE_ADDR'];
                                $ua = $_SERVER['HTTP_USER_AGENT'];



                                // (Getting the value)
                                $response = ClientService::detect( $ip, $ua );

                                if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                                {// (Unable to detect the client)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                    ;
                                }



                                // (Getting the value)
                                $record =
                                [
                                    'user'                 => $user->id,
                                    'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                    'description'          => 'Login via IDK',
                                    'session'              => $session->id,
                                    'ip'                   => $ip,
                                    'user_agent'           => $ua,
                                    'ip_info.country.code' => $response->body['ip']['country']['code'],
                                    'ip_info.country.name' => $response->body['ip']['country']['name'],
                                    'ip_info.isp'          => $response->body['ip']['isp'],
                                    'ua_info.browser'      => $response->body['ua']['browser'],
                                    'ua_info.os'           => $response->body['ua']['os'],
                                    'ua_info.hw'           => $response->body['ua']['hw'],
                                    'datetime.insert'      => DateTime::fetch()
                                ]
                                ;

                                if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                                {// (Unable to insert the record)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                    ;
                                }
                            }
                        )
                        ;



                        // (Removing the cookie)
                        CookiesStore::fetch()->cookies['fwd_route']->set( '', -1 );



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200), [], [ 'location' => LoginService::extract_location() ] ) )
                        ;
                    break;

                    case 'logout':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Setting the value)
                        $session->data = [];



                        if ( !$session->destroy() )
                        {// (Unable to destroy the session)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to destroy the session' ] ] ) )
                            ;
                        }



                        // (Getting the values)
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ua = $_SERVER['HTTP_USER_AGENT'];



                        // (Getting the value)
                        $response = ClientService::detect( $ip, $ua );

                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                        {// (Unable to detect the client)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                            'description'          => 'Logout',
                            'session'              => null,
                            'ip'                   => $ip,
                            'user_agent'           => $ua,
                            'ip_info.country.code' => $response->body['ip']['country']['code'],
                            'ip_info.country.name' => $response->body['ip']['country']['name'],
                            'ip_info.isp'          => $response->body['ip']['isp'],
                            'ua_info.browser'      => $response->body['ua']['browser'],
                            'ua_info.os'           => $response->body['ua']['os'],
                            'ua_info.hw'           => $response->body['ua']['hw'],
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'change_name':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// (Record not found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        if ( UserModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'name', $input['name'] ] ] )->exists() )
                        {// (Record found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','name'] already exists (user)" ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'name'            => $input['name'],

                            'datetime.update' => DateTime::fetch()
                        ]
                        ;

                        if ( UserModel::fetch()->where( 'id', $user_id )->update( $record ) === false )
                        {// (Unable to update the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to update the record (user)" ] ] ) )
                            ;
                        }



                        // (Getting the values)
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ua = $_SERVER['HTTP_USER_AGENT'];



                        // (Getting the value)
                        $response = ClientService::detect( $ip, $ua );

                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                        {// (Unable to detect the client)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                            'description'          => "Name has been changed to '" . $input['name'] . "'",
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
                            'resource.type'        => 'user',
                            'resource.id'          => $user_id,
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'change_email':
                        if ( $request->headers['Auth-Token'] )
                        {// (Authorization has been provided)
                            if ( $request->client_ip !== $request->server_ip )
                            {// (Request is not from localhost)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $response = AuthorizationService::fetch( $request->headers['Auth-Token'] );

                            if ( $response->status->code !== 200 )
                            {// (Unable to fetch the authorization)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $authorization = $response->body;



                            // (Starting an authorization)
                            $response = AuthorizationService::start
                            (
                                [
                                    'request'           =>
                                    [
                                        'endpoint_path' => $authorization->data['request']['endpoint_path'],
                                        'action'        => 'user::confirm_new_email',
                                        'input'         => $authorization->data['request']['input']
                                    ]
                                ]
                            )
                            ;
                            
                            if ( $response->status->code !== 200 )
                            {// (Unable to start the authorization)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the authorization' ] ] ) )
                                ;
                            }



                            // (Sending the authorization)
                            $response = AuthorizationService::send
                            (
                                $response->body['token'],
                                $authorization->data['request']['input']['new_value'],
                                'user.confirm_new_email',
                                
                                $authorization->data['request']['input']['client']['ip'],
                                $authorization->data['request']['input']['client']['ua']
                            )
                            ;
                            
                            if ( $response->status->code !== 200 )
                            {// (Unable to send the authorization)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to send the authorization' ] ] ) )
                                ;
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200) ) )
                            ;
                        }
                        else
                        {// (Authorization has not been provided)
                            // (Verifying the user)
                            $response = UserService::verify();

                            if ( $response->status->code !== 200 )
                            {// (Session is not valid)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $session = SessionsStore::fetch()->sessions['user'];
    
    
    
                            // (Getting the value)
                            $user_id = $session->data['user'];
    
    
    
                            // (Getting the value)
                            $user = UserModel::fetch()->where( 'id', $user_id )->find();
    
                            if ( !$user )
                            {// (Record not found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $input = RPCRequest::fetch()->parse_body();



                            if ( UserModel::fetch()->where( 'email', $input['email'] )->exists() )
                            {// (Record found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['email'] already exists (user)" ] ] ) )
                                ;
                            }



                            // (Starting an authorization)
                            $response = AuthorizationService::start
                            (
                                [
                                    'request'             =>
                                    [
                                        'endpoint_path'   => $request->url->path,
                                        'action'          => $request->headers['Action'],
                                        'input'           =>
                                        [
                                            'new_value'   => $input['email'],

                                            'client'      =>
                                            [
                                                'ip'      => $_SERVER['REMOTE_ADDR'],
                                                'ua'      => $_SERVER['HTTP_USER_AGENT'],

                                                'user'    => $user_id,
                                                'session' => $session->id
                                            ]
                                        ]
                                    ],

                                    'display'           => 'Confirm operation by email <b>' . $input['email'] . '</b> ...'
                                ]
                            )
                            ;
                            
                            if ( $response->status->code !== 200 )
                            {// (Unable to start the authorization)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the authorization' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $token = $response->body['token'];



                            // (Sending the authorization)
                            $response = AuthorizationService::send( $token, $user->email, RPCParser::$subject . '.' . RPCParser::$verb );
                            
                            if ( $response->status->code !== 200 )
                            {// (Unable to send the authorization)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to send the authorization' ] ] ) )
                                ;
                            }



                            // Returning the value
                            return Server::send( new Response( new Status(200) ) );
                        }
                    break;

                    case 'confirm_new_email':
                        if ( $request->headers['Auth-Token'] )
                        {// (Authorization has been provided)
                            if ( $request->client_ip !== $request->server_ip )
                            {// (Request is not from localhost)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $response = AuthorizationService::fetch( $request->headers['Auth-Token'] );

                            if ( $response->status->code !== 200 )
                            {// (Unable to fetch the authorization)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $authorization = $response->body;



                            // (Getting the value)
                            $record =
                            [
                                'email' => $authorization->data['request']['input']['new_value']
                            ]
                            ;

                            if ( !UserModel::fetch()->where( 'id', $authorization->data['request']['input']['client']['user'] )->update( $record ) )
                            {// (Unable to update the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to update the record (user)' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $response = ClientService::fetch_real_session_id
                            (
                                $authorization->data['request']['input']['client']['user'],
                                $authorization->data['request']['input']['client']['session']
                            )
                            ;

                            if ( $response->status->code !== 200 )
                            {// (Unable to fetch the real session id)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'Unable to fetch the real session id' ] ) )
                                ;
                            }



                            // (Getting the value)
                            $session_id = $response->body['session_id'];



                            // (Getting the values)
                            $ip = $authorization->data['request']['input']['client']['ip'];
                            $ua = $authorization->data['request']['input']['client']['ua'];



                            // (Getting the value)
                            $response = ClientService::detect( $ip, $ua );

                            if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                            {// (Unable to detect the client)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $record =
                            [
                                'user'                 => $authorization->data['request']['input']['client']['user'],
                                'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                'description'          => "Email has been changed to '" . $record['email'] . "'",
                                'session'              => $session_id,
                                'ip'                   => $ip,
                                'user_agent'           => $ua,
                                'ip_info.country.code' => $response->body['ip']['country']['code'],
                                'ip_info.country.name' => $response->body['ip']['country']['name'],
                                'ip_info.isp'          => $response->body['ip']['isp'],
                                'ua_info.browser'      => $response->body['ua']['browser'],
                                'ua_info.os'           => $response->body['ua']['os'],
                                'ua_info.hw'           => $response->body['ua']['hw'],
                                'resource.action'      => 'update',
                                'resource.type'        => 'user',
                                'resource.id'          => $authorization->data['request']['input']['client']['user'],
                                'datetime.insert'      => DateTime::fetch()
                            ]
                            ;

                            if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                            {// (Unable to insert the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                ;
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200) ) )
                            ;
                        }
                    break;

                    case 'wait_authorization':
                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];

                        if ( !$session->start() )
                        {// (Unable to start the session)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the session' ] ] ) )
                            ;
                        }

                        
 
                        // (Getting the value)
                        $token = $session->data['authorization'];

                        if ( !$token )
                        {// Value not found
                            if ( !$session->destroy() )
                            {// (Unable to destroy the session)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to destroy the session' ] ] ) )
                                ;
                            }


                            // Returning the value
                            return
                                Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                            ;
                        }



                        // (Setting the time limit)
                        set_time_limit(60);



                        while (true)
                        {// Processing each clock
                            // (Getting the value)
                            $response = AuthorizationService::fetch( $token );

                            if ( $response->status->code === 404 )
                            {// (Authorization not found)
                                // (Closing the session)
                                #$session->close();



                                // (Removing the element)
                                unset( $session->data['authorization'] );



                                // Returning the value
                                return
                                    Server::send( new Response( new Status(200) ) )
                                ;
                            }



                            // (Waiting for the time)
                            sleep(2);
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(408), [], [ 'error' => [ 'message' => 'Too much time for response' ] ] ) )
                        ;
                    break;

                    case 'change_birth_data':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// (Record not found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        // (Getting the value)
                        $record =
                        [
                            'birth.name'      => $input['birth.name'],
                            'birth.surname'   => $input['birth.surname'],

                            'datetime.update' => DateTime::fetch()
                        ]
                        ;

                        if ( UserModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $user_id ] ] )->update( $record ) === false )
                        {// (Unable to update the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to update the record (user)" ] ] ) )
                            ;
                        }



                        // (Getting the values)
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ua = $_SERVER['HTTP_USER_AGENT'];



                        // (Getting the value)
                        $response = ClientService::detect( $ip, $ua );

                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                        {// (Unable to detect the client)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                            'description'          => 'Birth-Data have been changed',
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
                            'resource.type'        => 'user',
                            'resource.id'          => $user_id,
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'recover':
                        // (Getting the value)
                        $request = Request::fetch();



                        if ( $request->headers['Auth-Token'] )
                        {// Value found
                            if ( $request->client_ip !== $request->server_ip )
                            {// (Request is not from localhost)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $res = AuthorizationService::fetch( $request->headers['Auth-Token'] );

                            if ( $res->status->code !== 200 )
                            {// (Unable to fetch the authorization)
                                // Returning the value
                                return
                                    Server::send( $res )
                                ;
                            }



                            // (Getting the value)
                            $authorization = $res->body;



                            // (Getting the value)
                            $response = ClientService::fetch_real_session_id( $authorization->data['request']['input']['client']['user'] );

                            if ( $response->status->code !== 200 )
                            {// (Unable to fetch the real session id)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'Unable to fetch the real session id' ] ) )
                                ;
                            }



                            // (Getting the value)
                            $session_id = $response->body['session_id'];



                            // (Getting the values)
                            $ip = $authorization->data['request']['input']['client']['ip'];
                            $ua = $authorization->data['request']['input']['client']['ua'];



                            // (Getting the value)
                            $response = ClientService::detect( $ip, $ua );

                            if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                            {// (Unable to detect the client)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $record =
                            [
                                'user'                 => $authorization->data['request']['input']['client']['user'],
                                'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                'description'          => 'User has been recovered',
                                'session'              => $session_id,
                                'ip'                   => $ip,
                                'user_agent'           => $ua,
                                'ip_info.country.code' => $response->body['ip']['country']['code'],
                                'ip_info.country.name' => $response->body['ip']['country']['name'],
                                'ip_info.isp'          => $response->body['ip']['isp'],
                                'ua_info.browser'      => $response->body['ua']['browser'],
                                'ua_info.os'           => $response->body['ua']['os'],
                                'ua_info.hw'           => $response->body['ua']['hw'],
                                'datetime.insert'      => DateTime::fetch()
                            ]
                            ;

                            if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                            {// (Unable to insert the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                ;
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200), [ 'Content-Type: application/json' ], $authorization->data['request']['input']['client']['user'] ) )
                            ;
                        }
                        else
                        {// Value not found
                            // (Getting the value)
                            $input = RPCRequest::fetch()->parse_body();



                            // (Getting the value)
                            $user = UserModel::fetch()->where( 'email', $input['email'] )->find();

                            if ( !$user )
                            {// (Record not found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(200) ) )
                                ;
                            }

                        

                            // (Getting the value)
                            $data =
                            [
                                'request'                =>
                                [
                                    'endpoint_path'      => $request->url->path,
                                    'action'             => $request->headers['Action'],
                                    'input'              =>
                                    [
                                        'client'         =>
                                        [
                                            'ip'         => $_SERVER['REMOTE_ADDR'],
                                            'user_agent' => $_SERVER['HTTP_USER_AGENT'],

                                            'user'       => $user->id
                                        ]
                                    ]
                                ],

                                'login'                  => true
                            ]
                            ;

                            // (Starting the authorization)
                            $response = AuthorizationService::start( $data, $request->url->fetch_base() . '/admin/dashboard' );

                            if ( $response->status->code !== 200 )
                            {// (Unable to start the authorization)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $token = $response->body['token'];



                            // (Sending the authorization)
                            $response = AuthorizationService::send( $token, $user->email, str_replace( '::', '.', $request->headers['Action'] ) );

                            if ( $response->status->code !== 200 )
                            {// (Unable to send the authorization)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200) ) )
                            ;
                        }
                    break;

                    case 'terminate_session':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();

                        foreach ( $input as $id )
                        {// Processing each entry
                            // (Getting the value)
                            $activity = ActivityModel::fetch()->where( [ [ 'user', $user_id ], [ 'id', $id ] ] )->find();

                            if ( !$activity )
                            {// (Record not found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (activity)' ] ] ) )
                                ;
                            }



                            if ( !SessionModel::fetch()->where( 'id', $activity->session )->delete() )
                            {// (Unable to delete the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to delete the record (session)" ] ] ) )
                                ;
                            }
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'add':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        if ( UserModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'name', $input['name'] ] ] )->exists() )
                        {// (Record found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['name'] already exists (user)" ] ] ) )
                            ;
                        }

                        if ( UserModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'email', $input['email'] ] ] )->exists() )
                        {// (Record found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['email'] already exists (user)" ] ] ) )
                            ;
                        }



                        // (Sending an http request)
                        $response = HttpClient::send
                        (
                            'https://' . App::$id . '/api',
                            'RPC',
                            [
                                'Action: user::register',
                                'Content-Type: application/json',

                                'User-Agent: Simba'
                            ],
                            json_encode
                            (
                                [
                                    'tenant'        =>
                                    [
                                        'id'        => $user->tenant
                                    ],

                                    'user'          =>
                                    [
                                        'name'      => $input['name'],
                                        'email'     => $input['email'],
                                        'hierarchy' => $input['hierarchy']
                                    ],

                                    'client'        =>
                                    [
                                        'ip'        => $_SERVER['REMOTE_ADDR'],
                                        'ua'        => $_SERVER['HTTP_USER_AGENT'],

                                        'user'      => $user_id,
                                        'session'   => $session->id
                                    ]
                                ]
                            )
                        )
                        ;



                        // (Setting the value)
                        $session->data['authorization'] = $response->body['token'];



                        // (Removing the element)
                        unset( $response->body['token'] );



                        // Returning the value
                        return
                            Server::send( new Response( new Status( $response->fetch_tail()->status->code ), [], $response->body ) )
                        ;
                    break;

                    case 'remove':
                        // (Getting the value)
                        $request = Request::fetch();



                        if ( $request->headers['Auth-Token'] )
                        {// (Authorization has been provided)
                            if ( $request->client_ip !== $request->server_ip )
                            {// (Request is not from localhost)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $response = AuthorizationService::fetch( $request->headers['Auth-Token'] );

                            if ( $response->status->code !== 200 )
                            {// (Unable to fetch the authorization)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $authorization = $response->body;



                            // (Getting the value)
                            $user_id = $authorization->data['request']['input']['user'];

                            


                            // (Getting the value)
                            $user = UserModel::fetch()->where( 'id', $user_id )->find();

                            if ( !$user )
                            {// (Record not found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                ;
                            }



                            if ( !UserModel::fetch()->where( 'id', $user_id )->delete() )
                            {// (Unable to delete the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to delete the record (user)' ] ] ) )
                                ;
                            }


                            if ( $authorization->data['request']['input']['client']['user'] !== $user_id )
                            {// (User is removed by another user)
                                if ( $authorization->data['request']['input']['client'] )
                                {// Value found
                                    // (Getting the value)
                                    $response = ClientService::fetch_real_session_id
                                    (
                                        $authorization->data['request']['input']['client']['user'],
                                        $authorization->data['request']['input']['client']['session']
                                    )
                                    ;

                                    if ( $response->status->code !== 200 )
                                    {// (Unable to fetch the real session id)
                                        // Returning the value
                                        return
                                            Server::send( new Response( new Status(500), [], [ 'Unable to fetch the real session id' ] ) )
                                        ;
                                    }



                                    // (Getting the value)
                                    $session_id = $response->body['session_id'];



                                    // (Getting the values)
                                    $ip = $authorization->data['request']['input']['client']['ip'];
                                    $ua = $authorization->data['request']['input']['client']['ua'];



                                    // (Getting the value)
                                    $response = ClientService::detect( $ip, $ua );
            
                                    if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                                    {// (Unable to detect the client)
                                        // Returning the value
                                        return
                                            Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to detect the client" ] ] ) )
                                        ;
                                    }



                                    // (Getting the value)
                                    $record =
                                    [
                                        'user'                 => $authorization->data['request']['input']['client']['user'],
                                        'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
                                        'description'          => "User has been removed",
                                        'session'              => $session_id,
                                        'ip'                   => $ip,
                                        'user_agent'           => $ua,
                                        'ip_info.country.code' => $response->body['ip']['country']['code'],
                                        'ip_info.country.name' => $response->body['ip']['country']['name'],
                                        'ip_info.isp'          => $response->body['ip']['isp'],
                                        'ua_info.browser'      => $response->body['ua']['browser'],
                                        'ua_info.os'           => $response->body['ua']['os'],
                                        'ua_info.hw'           => $response->body['ua']['hw'],
                                        'resource.action'      => 'delete',
                                        'resource.type'        => 'user',
                                        'resource.id'          => $user_id,
                                        'resource.key'         => $user->name,
                                        'datetime.insert'      => DateTime::fetch()
                                    ]
                                    ;

                                    if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                                    {// (Unable to insert the record)
                                        // Returning the value
                                        return
                                            Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => "Unable to insert the record (activity)" ] ] ) )
                                        ;
                                    }
                                }
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200) ) )
                            ;
                        }
                        else
                        {// (Authorization has not been provided)
                            // (Verifying the user)
                            $response = UserService::verify( 1 );

                            if ( $response->status->code !== 200 )
                            {// (Session is not valid)
                                // Returning the value
                                return
                                    Server::send( $response )
                                ;
                            }



                            // (Getting the value)
                            $session = SessionsStore::fetch()->sessions['user'];



                            // (Getting the value)
                            $user_id = $session->data['user'];



                            // (Getting the value)
                            $current_user = UserModel::fetch()->where( 'id', $user_id )->find();
        
                            if ( !$current_user )
                            {// (Record not found)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $input = RPCRequest::fetch()->parse_body();



                            foreach ( $input as $id )
                            {// Processing each entry
                                // (Getting the value)
                                $user = UserModel::fetch()->where( [ [ 'tenant', $current_user->tenant ], [ 'id', $id ] ] )->find();

                                if ( !$user )
                                {// (Record not found)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                                    ;
                                }



                                // (Starting an authorization)
                                $response = AuthorizationService::start
                                (
                                    [
                                        'request'               =>
                                        [
                                            'endpoint_path'     => $request->url->path,
                                            'action'            => $request->headers['Action'],
                                            'input'             =>
                                            [
                                                'user'          => $user->id,

                                                'client'        =>
                                                [
                                                    'ip'        => $_SERVER['REMOTE_ADDR'],
                                                    'ua'        => $_SERVER['HTTP_USER_AGENT'],
            
                                                    'user'      => $current_user->id,
                                                    'session'   => $session->id
                                                ]
                                            ]
                                        ]
                                    ]
                                )
                                ;
                                
                                if ( $response->status->code !== 200 )
                                {// (Unable to start the authorization)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the authorization' ] ] ) )
                                    ;
                                }



                                // (Sending the authorization)
                                $response = AuthorizationService::send( $response->body['token'], $user->email, RPCParser::$subject . '.' . RPCParser::$verb );
                                
                                if ( $response->status->code !== 200 )
                                {// (Unable to send the authorization)
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to send the authorization' ] ] ) )
                                    ;
                                }
                            }



                            // Returning the value
                            return
                                Server::send( new Response( new Status(200) ) )
                            ;
                        }
                    break;

                    case 'mark_alert_as_read':
                        // (Verifying the user)
                        $response = UserService::verify();

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        // (Getting the value)
                        $record =
                        [
                            'datetime.alert.read' => DateTime::fetch()
                        ]
                        ;

                        if ( !ActivityModel::fetch()->where( [ [ 'user', $user_id ], [ 'id', $input['id'] ] ] )->update( $record ) )
                        {// (Unable to update the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => "Unable to update the record (activity)" ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;
                }
            break;

            case 'document':
                switch ( RPCParser::$verb )
                {
                    case 'insert':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        if ( DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'path', $input['path'] ] ] )->exists() )
                        {// (Record found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','path'] already exists (" . RPCParser::$subject . ")" ] ] ) )
                            ;
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
                                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (' . RPCParser::$subject . ')' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $resource_id = DocumentModel::fetch()->fetch_ids()[0];



                        if ( !DocumentTagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'document', $resource_id ] ] )->delete() )
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
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Record not found (tag)' ] ] ) )
                                ;
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
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (document_tag)' ] ] ) )
                                ;
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
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
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
                            'resource.type'        => RPCParser::$subject,
                            'resource.id'          => $resource_id,
                            'resource.key'         => $record['path'],
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200), [ 'Content-Type: application/json' ], $resource_id ) )
                        ;
                    break;

                    case 'update':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        if ( DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'path', $input['path'] ], [ 'owner', '<>', $user->id ] ] )->exists() )
                        {// (Record found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','path'] already exists (" . RPCParser::$subject . ")" ] ] ) )
                            ;
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
                            return
                                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to update the record (' . RPCParser::$subject . ')' ] ] ) )
                            ;
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
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Record not found (tag)' ] ] ) )
                                ;
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
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (document_tag)' ] ] ) )
                                ;
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
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
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
                            'resource.type'        => RPCParser::$subject,
                            'resource.id'          => $input['id'],
                            'resource.key'         => $object->path,
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'delete':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();

                        foreach ( $input as $id )
                        {// Processing each entry
                            if ( !DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $id ] ] )->bind( $object, [ 'path' ] )->delete() )
                            {// (Unable to delete the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to delete the record (' . RPCParser::$subject . ')' ] ] ) )
                                ;
                            }



                            // (Getting the values)
                            $ip = $_SERVER['REMOTE_ADDR'];
                            $ua = $_SERVER['HTTP_USER_AGENT'];



                            // (Getting the value)
                            $response = ClientService::detect( $ip, $ua );

                            if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                            {// (Unable to detect the client)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $record =
                            [
                                'user'                 => $user_id,
                                'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
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
                                'resource.type'        => RPCParser::$subject,
                                'resource.id'          => $id,
                                'resource.key'         => $object->path,
                                'datetime.insert'      => DateTime::fetch()
                            ]
                            ;

                            if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                            {// (Unable to insert the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) )
                                ;
                            }
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'find':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        // (Getting the value)
                        $resource = DocumentModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $input['id'] ] ] )->find();

                        if ( !$resource )
                        {// (Record not found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (' . RPCParser::$subject . ')' ] ] ) )
                            ;
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
                        return
                            Server::send( new Response( new Status(200), [], $resource ) )
                        ;
                    break;



                    case 'set_option':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        if ( !in_array( $input['option'], [ 'active', 'sitemap' ] ) )
                        {// Match failed
                            // Returning the value
                            return
                                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => "Invalid value for property 'option'" ] ] ) )
                            ;
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
                            return
                                Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => 'Unable to update the record (' . RPCParser::$subject . ')' ] ] ) )
                            ;
                        }



                        // (Getting the values)
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ua = $_SERVER['HTTP_USER_AGENT'];



                        // (Getting the value)
                        $response = ClientService::detect( $ip, $ua );

                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                        {// (Unable to detect the client)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
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
                            'resource.type'        => RPCParser::$subject,
                            'resource.id'          => $input['id'],
                            'resource.key'         => $object->path,
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;
                }
            break;

            case 'tag':
                switch ( RPCParser::$verb )
                {
                    case 'insert':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        if ( TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'name', $input['name'] ] ] )->exists() )
                        {// (Record found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','name'] already exists (" . RPCParser::$subject . ")" ] ] ) )
                            ;
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
                            return
                                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (' . RPCParser::$subject . ')' ] ] ) )
                            ;
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
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
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
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200), [ 'Content-Type: application/json' ], $resource_id ) )
                        ;
                    break;

                    case 'update':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        if ( TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'name', $input['name'] ], [ 'owner', '<>', $user->id ] ] )->exists() )
                        {// (Record found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(409), [], [ 'error' => [ 'message' => "['tenant','name'] already exists (" . RPCParser::$subject . ")" ] ] ) )
                            ;
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
                            return
                                Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to update the record (' . RPCParser::$subject . ')' ] ] ) )
                            ;
                        }



                        // (Getting the values)
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $ua = $_SERVER['HTTP_USER_AGENT'];



                        // (Getting the value)
                        $response = ClientService::detect( $ip, $ua );

                        if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                        {// (Unable to detect the client)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $record =
                        [
                            'user'                 => $user_id,
                            'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
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
                            'resource.type'        => RPCParser::$subject,
                            'resource.id'          => $input['id'],
                            'resource.key'         => $object->name,
                            'datetime.insert'      => DateTime::fetch()
                        ]
                        ;

                        if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'delete':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();

                        foreach ( $input as $id )
                        {// Processing each entry
                            #if ( !TagModel::fetch()->where( 'id', 'IN', $input )->delete() )
                            if ( !TagModel::fetch()->where( [ ['tenant', $user->tenant ], [ 'id', $id ] ] )->bind( $object, [ 'name' ] )->delete() )
                            {// (Unable to delete the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to delete the record (' . RPCParser::$subject . ')' ] ] ) )
                                ;
                            }



                            // (Getting the values)
                            $ip = $_SERVER['REMOTE_ADDR'];
                            $ua = $_SERVER['HTTP_USER_AGENT'];



                            // (Getting the value)
                            $response = ClientService::detect( $ip, $ua );

                            if ( !in_array( $response->status->code, [ 200, 401 ] ) )
                            {// (Unable to detect the client)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to detect the client' ] ] ) )
                                ;
                            }



                            // (Getting the value)
                            $record =
                            [
                                'user'                 => $user_id,
                                'action'               => RPCParser::$subject . '.' . RPCParser::$verb,
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
                                'resource.type'        => RPCParser::$subject,
                                'resource.id'          => $id,
                                'resource.key'         => $object->name,
                                'datetime.insert'      => DateTime::fetch()
                            ]
                            ;

                            if ( ActivityModel::fetch()->insert( [ $record ] ) === false )
                            {// (Unable to insert the record)
                                // Returning the value
                                return
                                    Server::send( new Response( new Status(500), [], [  'error' => [ 'message' => 'Unable to insert the record (activity)' ] ] ) )
                                ;
                            }
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200) ) )
                        ;
                    break;

                    case 'find':
                        // (Verifying the user)
                        $response = UserService::verify( 1 );

                        if ( $response->status->code !== 200 )
                        {// (Verification is failed)
                            // Returning the value
                            return
                                Server::send( $response )
                            ;
                        }



                        // (Getting the value)
                        $session = SessionsStore::fetch()->sessions['user'];



                        // (Getting the value)
                        $user_id = $session->data['user'];



                        // (Getting the value)
                        $user = UserModel::fetch()->where( 'id', $user_id )->find();

                        if ( !$user )
                        {// Value not found
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] ) )
                            ;
                        }



                        // (Getting the value)
                        $input = RPCRequest::fetch()->parse_body();



                        // (Getting the value)
                        $resource = TagModel::fetch()->where( [ [ 'tenant', $user->tenant ], [ 'id', $input['id'] ] ] )->find();

                        if ( !$resource )
                        {// (Record not found)
                            // Returning the value
                            return
                                Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (' . RPCParser::$subject . ')' ] ] ) )
                            ;
                        }



                        // Returning the value
                        return
                            Server::send( new Response( new Status(200), [], $resource ) )
                        ;
                    break;
                }
            break;
        }



        switch ( App::$env->type )
        {
            case 'dev':
                // Returning the value
                return
                    Server::send( new Response( new Status(404), [], [ 'error' => [ 'message' => 'RPC :: Action not found' ] ] ) )
                ;
            break;

            default:
                // Returning the value
                return
                    Server::send( new Response( new Status(200) ) )
                ;
        }
    }
}



?>