<?php



namespace App\Services;



use \Solenoid\Core\Service;

use \Solenoid\Core\App\WebApp;

use \Solenoid\HTTP\Status;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\URL;

use \App\Stores\Sessions\Store as SessionsStore;
use \App\Stores\Cookies\Store as CookiesStore;
use \App\Models\local\simba_db\User as UserModel;
use \App\Models\local\simba_db\Tenant as TenantModel;



class User extends Service
{
    # Returns [Response]
    public static function verify (?int $hierarchy = null)
    {
        // (Getting the value)
        $session = SessionsStore::fetch()->sessions['user'];



        if ( !$session->start() )
        {// (Unable to start the session)
            // Returning the value
            return
                new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to start the session' ] ] )
            ;
        }

        if ( !$session->regenerate_id() )
        {// (Unable to regenerate the session id)
            // Returning the value
            return
                new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to regenerate the session id' ] ] )
            ;
        }



        if ( $session->data['user'] )
        {// Value found
            if ( $session->data['idk_reset'] )
            {// Value found
                // Returning the value
                return
                    new Response( new Status(303), [ 'Location: /admin/user-activation' ] )
                ;
            }
            else
            {// Value not found
                // (Doing nothing)
            }
        }
        else
        {// Value not found
            if ( !$session->destroy() )
            {// (Unable to destroy the session)
                // Returning the value
                return
                    new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to destroy the session' ] ] )
                ;
            }



            // (Setting the cookie)
            CookiesStore::fetch()->cookies['fwd_route']->set( $_SERVER['HTTP_REFERER'] );



            // Returning the value
            return
                new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] )
            ;
        }



        if ( $hierarchy )
        {// Value found
            if ( !UserModel::fetch()->where( [ [ 'id', $session->data['user'] ], [ 'hierarchy', $hierarchy ] ] )->exists() )
            {// (Record not found)
                // Returning the value
                return
                    new Response( new Status(401), [], [ 'error' => [ 'message' => 'Client not authorized' ] ] )
                ;
            }
        }



        // Returning the value
        return
            new Response( new Status(200) )
        ;
    }

    # Returns [Response]
    public static function fetch_data (int $user)
    {
        // (Getting the value)
        $user = UserModel::fetch()->where( 'id', $user )->find();

        if ( $user === false )
        {// (Record not found)
            // Returning the value
            return
                new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (user)' ] ] )
            ;
        }



        // (Getting the value)
        $tenant = TenantModel::fetch()->where( 'id', $user->tenant )->find();

        if ( $tenant === false )
        {// (Record not found)
            // Returning the value
            return
                new Response( new Status(404), [], [ 'error' => [ 'message' => 'Record not found (tenant)' ] ] )
            ;
        }



        // (Getting the value)
        $data =
        [
            'user'              =>
            [
                'name'          => $user->name,

                'email'         => $user->email,

                'hierarchy'     => $user->hierarchy,

                'birth'         =>
                [
                    'name'      => $user->birth->name,
                    'surname'   => $user->birth->surname
                ]
            ],

            'tenant'            =>
            [
                'name'          => $tenant->name
            ],

            'password_set'      => $user->security->password !== null,

            'mfa'               => $user->security->mfa === 1,

            'idk'               => $user->security->idk->authentication === 1
        ]
        ;



        // Returning the value
        return
            new Response( new Status(200), [], $data )
        ;
    }
}



?>