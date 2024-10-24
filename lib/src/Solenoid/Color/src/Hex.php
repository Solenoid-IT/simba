<?php



namespace App\Libraries\Solenoid\Color;



class Hex
{
    public string $r;
    public string $g;
    public string $b;

    public string $a;



    # Returns [self]
    public function __construct (string $r, string $g, string $b, string $a = 'ff')
    {
        // (Getting the values)
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;

        $this->a = $a;
    }

    # Returns [Hex]
    public static function create (string $r, string $g, string $b, string $a = 'ff')
    {
        // Returning the value
        return new Hex( $r, $g, $b, $a );
    }



    # Returns [Hex]
    public static function parse (string $hex)
    {
        // Returning the value
        return Hex::create( substr( $hex, 1, 2 ), substr( $hex, 3, 2 ), substr( $hex, 5, 2 ) );
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return '#' . $this->r . $this->g . $this->b;
    }
}



?>