<?php



namespace App\Services;



use \Solenoid\Core\Service;

use \Solenoid\Core\Credentials;

use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Status;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Client\Client as HttpClient;

use \App\Models\local\simba_db\Session as SessionModel;



class Client extends Service
{
    # Returns [Response] | Throws [Exception]
    public static function detect (?string $ip = null, ?string $ua = null)
    {
        // (Getting the value)
        $request = Request::fetch();



        // (Sending a request)
        $response = HttpClient::send
        (
            'https://api.solenoid.it/ip-info@1.0.0',
            'RPC',
            [
                'Action: analyze_ip',
                'Content-Type: application/json',
                'License: ' . Credentials::fetch( '/system/data.json' )['api']['ip-info']
            ],
            [
                'ip' => $ip ?? $request->client_ip
            ]
        )
        ;

        if ( $response->fetch_tail()->status->code !== 200 )
        {// (Request failed)
            // Returning the value
            return
                new Response( new Status( $response->fetch_tail()->status->code ), [], $response->body )
            ;
        }



        // (Getting the value)
        $ip_info =
        [
            'address'     => $response->body['ip']['address'],
            'country'     =>
            [
                'code'    => $response->body['geolocation']['country']['code'],
                'name'    => $response->body['geolocation']['country']['name']
            ],
            'isp'         => $response->body['connection']['isp']
        ]
        ;



        // (Sending a request)
        $response = HttpClient::send
        (
            'https://api.solenoid.it/ua-info@1.0.0',
            'RPC',
            [
                'Action: analyze_ua',
                'Content-Type: application/json',
                'License: ' . Credentials::fetch( '/system/data.json' )['api']['ua-info']
            ],
            [
                'ua' => $ua ?? $request->headers['User-Agent']
            ]
        )
        ;

        if ( $response->fetch_tail()->status->code !== 200 )
        {// (Request failed)
            // Returning the value
            return
                new Response( new Status( $response->fetch_tail()->status->code ), [], $response->body )
            ;
        }



        // (Getting the value)
        $ua_info =
        [
            'browser' => $response->body['browser']['summary'],
            'os'      => $response->body['os']['summary'],
            'hw'      => $response->body['hw']['summary']
        ]
        ;



        // (Getting the value)
        $data =
        [
            'ip' => $ip_info,
            'ua' => $ua_info
        ]
        ;



        // Returning the value
        return
            new Response( new Status(200), [], $data )
        ;
    }

    # Returns [Response] | Throws [Exception]
    public static function fetch_real_session_id (string $user, ?string $session = null)
    {
        // (Setting the value)
        $session_id = null;



        if ( $session === null )
        {// Value not found
            // (Setting the value)
            $session_found = false;
        }
        else
        {// Value found
            // (Getting the value)
            $session_found = SessionModel::fetch()->where( 'id', $session )->exists();
        }



        if ( $session_found )
        {// Value is true
            // (Getting the value)
            $session_id = $session;
        }
        else
        {// Value is false
            // (Getting the value)
            $session = SessionModel::fetch()->where( 'id', Request::fetch()->headers['Session-Id'] )->find();

            if ( $session )
            {// Value found
                // (Getting the value)
                $current_user_id = json_decode( $session->data, true )['user'];

                if ( $current_user_id === $user )
                {// Match OK
                    // (Getting the value)
                    $session_id = $session->id;
                }
            }
        }



        // Returning the value
        return
            new Response( new Status(200), [], [ 'session_id' => $session_id ] )
        ;
    }
}



?>