<?php



namespace App\Tasks\OnDemand;



use \Solenoid\Core\Task\Task;

use \Solenoid\Core\App\App;

use \Solenoid\HTTP\Client\Client;



class User extends Task
{
    public static array $tags = [];



    # Returns [void]
    public function create (string $tenant, string $user, string $email, int $hierarchy = 1)
    {
        // (Sending an http request)
        $response = Client::send
        (
            'https://' . App::$id . '/api',
            'RPC',
            [
                'Action: User.register',
                'Content-Type: application/json',

                'User-Agent: Simba'
            ],
            json_encode
            (
                [
                    'tenant'        =>
                    [
                        'name'      => $tenant
                    ],

                    'user'          =>
                    [
                        'name'      => $user,
                        'email'     => $email,
                        'hierarchy' => $hierarchy
                    ]
                ]
            )
        )
        ;

        if ( $response->fetch_tail()->status->code !== 200 )
        {// (Request failed)
            // (Setting the value)
            $message = "Request failed :: " . $response->body['error']['message'];

            // Throwing an exception
            throw new \Exception($message);

            // (Closing the process
            exit;
        }



        // Printing the value
        echo "\n\nConfirm operation by email \"$email\" ...\n\n\n";
    }

    # Returns [void]
    public function create_test (string $email)
    {
        // (Creating the user)
        $this->create( 'simba', 'admin', $email );
    }
}



?>