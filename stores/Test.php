<?php



namespace App\Stores;



class Test
{
    private static self $instance;

    private static array $values = [];



    # Returns [StdClass|false]
    public static function get (string $id)
    {
        // Returning the value
        return self::$values[ $id ] ?? false;
    }

    # Returns [void]
    public static function set (string $id, \StdClass $value)
    {
        // (Getting the value)
        self::$values[ $id ] = $value;
    }



    # Returns [self]
    private function __construct ()
    {
        // (Setting the values)
        self::set( '1', ( (object) [ 'name' => 'John', 'surname' => 'Smith' ] ) );
        self::set( '2', ( (object) [ 'name' => 'Sandra', 'surname' => 'Cameron' ] ) );
    }

    # Returns [self]
    public static function preset ()
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // Returning the value
            self::$instance = new self();
        }



        // Returning the value
        return self::$instance;
    }
}



?>