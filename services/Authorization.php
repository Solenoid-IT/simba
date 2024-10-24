<?php



namespace App\Services;



use \Solenoid\Core\Service;

use \Solenoid\Core\App\WebApp;

use \Solenoid\KeyGen\Generator;
use \Solenoid\KeyGen\Token;
use \Solenoid\MySQL\DateTime;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;
use \Solenoid\SMTP\Mail;
use \Solenoid\SMTP\MailBox;
use \Solenoid\SMTP\MailBody;
use \Solenoid\SMTP\Retry;

use \App\Stores\Connections\SMTP\Store as SMTPConnectionsStore;
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
        $app = WebApp::fetch();



        // (Getting the value)
        $connection = SMTPConnectionsStore::fetch()->connections['service'];



        // (Getting the value)
        $response = ClientService::detect( $ip, $ua );

        if ( $response->status->code !== 200 )
        {// (Request failed)
            // Returning the value
            return
                new Response( new Status(500), [], [ 'error' => [ 'message' => 'Unable to detect the client' ] ] )
            ;
        }



        // (Creating a Mail)
        $mail = new Mail
        (
            new MailBox( $app->fetch_credentials()['smtp']['profiles']['service']['username'], $app->name ),

            [
                new MailBox( $receiver )
            ],

            [],
            [],
            [],

            $app->name . ' - Authorization Required',
            new MailBody
            (
                '',

                $app->blade->build
                (
                    'components/mail/authorization.blade.php',
                    [
                        'app_name'     => $app->name,
                        'type'         => $type,
                        'client'       => $response->body,
                        'endpoint_url' => $app->request->url->fetch_base() . "/admin/authorization/$token"
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