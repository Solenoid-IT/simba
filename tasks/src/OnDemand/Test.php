<?php



namespace App\Tasks\OnDemand;



use \Solenoid\Core\Task\Task;

use \Solenoid\Core\Storage;



class Test extends Task
{
    public static array $tags = [ 'test' ];



    # Returns [void]
    public function run (string $name, string $surname)
    {
        // (Writing to the storage)
        Storage::select('local')->write( '/a/b/c/d/e/file.txt', date('c') );

        // (Writing to the storage)
        Storage::select('local')->write( '/../a/b/c/d/e/file-ext.txt', 'Hello World !!!' );



        // Printing the value
        echo "\n\nWelcome $name $surname !\n\n\n";
    }

    # Returns [void]
    public function print ()
    {
        // Printing the value
        echo date('c') . "\n";
    }
}



?>