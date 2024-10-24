<?php



namespace App\Stores\Sessions;



use \Solenoid\HTTP\Session;
use \Solenoid\HTTP\SessionContent;

use \Solenoid\KeyGen\Generator;
use \Solenoid\KeyGen\Token;

use \Solenoid\MySQL\DateTime;

use \App\Models\local\simba_db\Session as SessionModel;
use \App\Stores\Cookies\Store as CookiesStore;



class Store
{
    private static self $instance;



    public array $sessions;



    # Returns [self]
    private function __construct ()
    {
        // (Getting the value)
        $this->sessions['user'] = new Session
        (
            [
                'validate_id' => function ( $id )
                {
                    // Returning the value
                    return preg_match( '/^[\w]+$/', $id ) === 1;
                },

                'generate_id' => function ()
                {
                    // Returning the value
                    return
                        Generator::start
                        (
                            function ($id)
                            {
                                // Returning the value
                                return !SessionModel::fetch()->where( 'id', $id )->exists();
                            },
                            function ()
                            {
                                // Returning the value
                                return Token::generate( 128 );
                            }
                        )
                    ;
                },

                'init' => function ( $id, $duration )
                {
                    // (Getting the values)
                    $current_timestamp    = time();
                    $expiration_timestamp = $current_timestamp + $duration;



                    // Returning the value
                    return new SessionContent( $current_timestamp, $expiration_timestamp, [] );
                },

                'read' => function ( $id, $duration )
                {
                    // (Getting the value)
                    $session = SessionModel::fetch()->where( 'id', $id )->find();

                    if ( $session === false )
                    {// (Record does not exist)
                        // (Getting the values)
                        $current_timestamp    = time();
                        $expiration_timestamp = $current_timestamp + $duration;

                        $content = [ 'creation' => $current_timestamp, 'expiration' => $expiration_timestamp, 'data' => [] ];
                    }
                    else
                    {// (Record exists)
                        // (Getting the value)
                        $content =
                        [
                            'data'       => json_decode( $session->data, true ),

                            'creation'   => strtotime( $session->datetime->insert ),
                            'expiration' => strtotime( $session->datetime->expiration )
                        ]
                        ;

                        if ( time() >= $content['expiration'] )
                        {// (Session is expired)
                            // (Setting the value)
                            $content['data'] = [];
                        }
                    }
                    
    


                    // Returning the value
                    return new SessionContent( $content['creation'], $content['expiration'], $content['data'] );
                },

                'write' => function ( $id, $content )
                {
                    if ( SessionModel::fetch()->where( 'id', $id )->exists() )
                    {// (Record found)
                        // (Getting the value)
                        $record =
                        [
                            'data'                => json_encode( $content->data ),

                            'user'                => $content->data['user'],

                            'datetime.update'     => DateTime::fetch()
                        ]
                        ;

                        if ( SessionModel::fetch()->where( 'id', $id )->update( $record ) === false )
                        {// (Unable to update the record)
                            // (Setting the value)
                            $message = "Unable to update the record (session) :: " . SessionModel::fetch()->connection->get_error_text();

                            // Throwing an exception
                            throw new \Exception($message);

                            // Returning the value
                            return;
                        }
                    }
                    else
                    {// (Record not found)
                        // (Getting the value)
                        $record =
                        [
                            'id'                  => $id,

                            'data'                => json_encode( $content->data ),

                            'user'                => $content->data['user'],

                            'datetime.insert'     => DateTime::fetch( $content->creation ),
                            'datetime.expiration' => DateTime::fetch( $content->expiration )
                        ]
                        ;

                        if ( SessionModel::fetch()->insert( [ $record ] ) === false )
                        {// (Unable to insert the record)
                            // (Setting the value)
                            $message = "Unable to insert the record (session) :: " . SessionModel::fetch()->connection->get_error_text();

                            // Throwing an exception
                            throw new \Exception($message);

                            // Returning the value
                            return;
                        }
                    }
                },

                'change_id' => function ( $old, $new )
                {
                    if ( SessionModel::fetch()->where( 'id', $old )->update( [ 'id' => $new ] ) === false )
                    {// (Unable to update the record)
                        // (Setting the value)
                        $message = "Unable to update the record (session) :: " . SessionModel::fetch()->connection->get_error_text();

                        // Throwing an exception
                        throw new \Exception($message);

                        // Returning the value
                        return;
                    }
                },

                'set_expiration' => function ( $duration )
                {
                    // Returning the value
                    return $duration === null ? null : time() + $duration;
                },

                'destroy' => function ( $id )
                {
                    if ( SessionModel::fetch()->where( 'id', $id )->delete() === false )
                    {// (Unable to delete the record)
                        // (Setting the value)
                        $message = "Unable to delete the record (session) :: " . SessionModel::fetch()->connection->get_error_text();

                        // Throwing an exception
                        throw new \Exception($message);

                        // Returning the value
                        return;
                    }
                },
            ],
            CookiesStore::fetch()->cookies['user'],
            3600,
            true
        )
        ;
    }



    # Returns [self]
    public static function fetch ()
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self();
        }



        // Returning the value
        return self::$instance;
    }
}



?>