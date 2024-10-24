<?php



// (Getting the value)
$dir = realpath( __DIR__ . '/..' );



// (Including the files)
include_once( $dir . '/autoload.php' );
include_once( $dir . '/config.php' );
include_once( $dir . '/gate.php' );



use \Solenoid\Core\App\App;
use \Solenoid\Core\Scheduler;



// (Setting the value)
error_reporting(E_ERROR);



if ( App::fetch_context() !== 'cli' )
{// (Context is not 'CLI')
    // Printing the value
    echo "\n\nScheduler can be executed only from CLI context\n\n\n";

    // (Closing the process)
    exit;
}



// (Creating a Scheduler)
$scheduler = new Scheduler( $dir );



// (Setting the directory)
chdir( __DIR__ );



// (Getting the value)
$action = $argv[1];

switch ($action)
{
    case 'start':
        // (Running the scheduler)
        $scheduler->run();
    break;

    case 'stop':
        // (Killing the scheduler)
        $scheduler->kill();
    break;

    case 'restart':
        // (Executing the commands)
        system('php scheduler.php stop');
        system('php scheduler.php start');
    break;

    default:
        // Printing the value
        echo
            <<<EOD

            php $argv[0] start
            php $argv[0] stop
            php $argv[0] restart


            EOD
        ;
}



?>