<?php



namespace App\Services;



use \Solenoid\Core\Service;

use \Solenoid\Core\App\App;
use \Solenoid\Core\Credentials;
use \Solenoid\Core\MVC\View;

use \Solenoid\KeyGen\Generator;
use \Solenoid\KeyGen\Token;
use \Solenoid\MySQL\DateTime;
use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;
use \Solenoid\SMTP\Mail;
use \Solenoid\SMTP\MailBox;
use \Solenoid\SMTP\MailBody;
use \Solenoid\SMTP\Retry;

use \App\Stores\Connection\SMTP as SmtpConnectionStore;
use \App\Models\local\simba_db\Authorization as AuthorizationModel;
use \App\Services\Client as ClientService;



class Authorization extends Service
{
    # Returns [Response]
    public static function start (?array $data = null, ?string $callback_url = null, int $duration = 60)
    {
        // (Getting the value)
        $token = Generator::start
        (
            function ( $key )
            {
                // Returning the value
                return !AuthorizationModel::fetch()->where( 'token', $key )->exists();
            },

            function ()
            {
                // Returning the value
                return Token::generate( 128 );
            }
        )
        ;

        if ( $token === false )
        {// (Unable to generate the token)
            // Returning the value
            return
                new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to generate the token' ] ])
            ;
        }



        // (Getting the values)
        $creation_timestamp   = time();
        $expiration_timestamp = $creation_timestamp + $duration;



        // (Getting the value)
        $record =
        [
            'token'               => $token,

            'data'                => $data ? json_encode( $data ) : null,

            'callback_url'        => $callback_url,

            'datetime.insert'     => DateTime::fetch( $creation_timestamp ),
            'datetime.expiration' => DateTime::fetch( $expiration_timestamp )
        ]
        ;

        if ( AuthorizationModel::fetch()->insert( [ $record ] ) === false )
        {// (Unable to insert the record)
            // Returning the value
            return
                new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to insert the record (authorization)' ] ] )
            ;
        }



        // Returning the value
        return
            new Response( new Status(200), [], [ 'token' => $token, 'exp_time' => $expiration_timestamp ] )
        ;
    }

    # Returns [Response]
    public static function send (string $token, string $receiver, string $type, ?string $ip = null, ?string $ua = null)
    {
        // (Getting the value)
        $connection = SmtpConnectionStore::get( 'service' );



        // (Getting the value)
        $response = ClientService::detect( $ip, $ua );

        if ( $response->status->code !== 200 )
        {// (Request failed)
            if ( $response->status->code !== 401 )
            {// (Client is authorized)
                // Returning the value
                return
                    new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to detect the client' ] ] )
                ;
            }
        }



        if ( $response->status->code === 401 )
        {// (Client is not authorized)
            // (Getting the value)
            $client =
            [
                'ip'          =>
                [
                    'address' => $_SERVER['REMOTE_ADDR']
                ]
            ]
            ;
        }
        else
        {// (Client is authorized)
            // (Getting the value)
            $client = $response->body;
        }



        // (Creating a Mail)
        $mail = new Mail
        (
            new MailBox( Credentials::fetch('/smtp/data.json')['service']['username'], App::$name ),

            [
                new MailBox( $receiver )
            ],

            [],
            [],
            [],

            App::$name . ' - Authorization Required',
            new MailBody
            (
                '',

                View::build
                (
                    'components/mail/authorization.blade.php',
                    [
                        'app_name'     => App::$name,
                        'type'         => $type,
                        'client'       => $client,
                        'endpoint_url' => Request::fetch()->url->fetch_base() . "/admin/authorization/$token"
                    ]
                )
            )
        )
        ;

        if ( !$connection->send( $mail, new Retry() ) )
        {// (Unable to send the mail)
            // Returning the value
            return
                new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to send the mail' ] ] )
            ;
        }



        // Returning the value
        return
            new Response( new Status(200) )
        ;
    }

    # Returns [Response]
    public static function fetch (string $token)
    {
        // (Getting the value)
        $authorization = AuthorizationModel::fetch()->where( 'token', $token )->get();

        if ( $authorization === false )
        {// (Authorization not found)
            // Returning the value
            return
                new Response( new Status(404), [], [ 'error' => [ 'message' => 'Authorization not found' ] ] )
            ;
        }

        if ( time() >= strtotime( $authorization->datetime->expiration ) )
        {// (Authorization is expired)
            // Returning the value
            return
                new Response( new Status(404), [], [ 'error' => [ 'message' => 'Authorization not found' ] ] )
            ;
        }



        // Returning the value
        return
            new Response( new Status(200), [], $authorization )
        ;
    }

    # Returns [Response]
    public static function remove (string $token)
    {
        if ( AuthorizationModel::fetch()->where( 'token', $token )->delete() === false )
        {// (Unable to delete the record)
            // Returning the value
            return
                new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to delete the record (authorization)' ] ] )
            ;
        }



        // Returning the value
        return
            new Response( new Status(200) )
        ;
    }
}



?>