<?php



namespace App\Libraries\Solenoid\JWT;



class Token
{
    public array  $header;
    public array  $payload;

    public string $signature;



    # Returns [self]
    public function __construct (array $header = [ 'typ' => 'JWT', 'alg' => 'HS256' ], array $payload)
    {
        // (Getting the values)
        $this->header    = $header;
        $this->payload   = $payload;



        // (Getting the value)
        $this->signature = hash_hmac
        (
            'sha256',
            implode( '.', [ self::encode_component( json_encode( $header ) ), self::encode_component( json_encode( $payload ) ) ] ),
            '',
            true
        )
        ;
    }

    # Returns [Token]
    public static function create (array $header, array $payload)
    {
        // Returning the value
        return new Token( $header, $payload );
    }



    # Returns [string]
    public static function encode_component (string $component)
    {
        // Returning the value
        return str_replace( [ '+', '/', '=' ], [ '-', '_', '~' ], base64_encode( $component ) );
    }



    # Returns [string]
    public function encode ()
    {
        // Returning the value
        return
            implode
            (
                '.',
                [
                    self::encode_component( json_encode( $this->header ) ),
                    self::encode_component( json_encode( $this->payload ) ),
                    self::encode_component( $this->signature )
                ]
            )
        ;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return $this->encode();
    }
}



?>