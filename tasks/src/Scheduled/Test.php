<?php



namespace App\Tasks\Scheduled;



use \Solenoid\Core\Task\Task;

use \App\Middlewares\CLI\Parser as CLIParser;



class Test extends Task
{
    public static array $tags = [ 'test' ];



    # Returns [void]
    public function run ()
    {
        // (Running the middleware)
        CLIParser::run();



        // (Getting the values)
        $name    = CLIParser::$args['name'];
        $surname = CLIParser::$args['surname'];



        // (Waiting for the time)
        sleep(20);



        // Printing the value
        echo "\nHello \"$name\" \"$surname\" !\n\n\n";
    }
}



?>