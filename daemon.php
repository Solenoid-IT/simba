<?php



namespace App;



class Daemon implements \Solenoid\Core\Daemon
{
    private static self $instance;



    public int $startup_ts;



    # Returns [self]
    private function __construct () {}



    # Returns [self]
    public static function init ()
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Creating an instance)
            self::$instance = new self();
        }



        // Returning the value
        return self::$instance;
    }



    # Returns [void]
    public function startup ()
    {
        // Printing the value
        echo "[EVENT] -> Startup ( " . date( 'c', $this->startup_ts ) . " )\n";
    }

    # Returns [void]
    public function tick ()
    {
        // Printing the value
        echo "[EVENT] -> Tick ( Uptime = " . ( time() - $this->startup_ts ) . "s )\n";
    }

    # Returns [void]
    public function stop (int $signal)
    {
        // Printing the value
        echo "[EVENT] -> Stop ( $signal )\n";
    }
}



?>