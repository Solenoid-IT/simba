<?php



// (Getting the value)
$dir = realpath( __DIR__ . '/..' );



// (Including the files)
include_once( $dir . '/autoload.php' );
include_once( $dir . '/daemon.php' );



use \Solenoid\Core\App\App;

use \Solenoid\System\JDB;
use \Solenoid\System\Process;
use \Solenoid\System\SystemService;



// (Setting the value)
error_reporting(E_ERROR);



if ( App::fetch_context() !== 'cli' )
{// (Context is not 'CLI')
    // Printing the value
    echo "\n\nScheduler can be executed only from CLI context\n\n\n";

    // (Closing the process)
    exit;
}



// (Getting the value)
$db_file_path = "$dir/jdb/daemon.json";



// (Setting the directory)
chdir( __DIR__ );



// (Initializing the daemon)
$d = \App\Daemon::init();



// (Getting the value)
$action = $argv[1];

switch ($action)
{
    case 'start':
        // (Getting the value)
        $db = JDB::load( $db_file_path );

        if ( $db === false )
        {// (JDB not found)
            // (Creating a JDB)
            $db = new JDB( $db_file_path );

            // (Initializing the JDB)
            $db->init();
        }



        if ( $db->data['pid'] )
        {// Value found
            if ( Process::fetch_pid_info( $db->data['pid'] ) !== false )
            {// (Process is running)
                // Printing the value
                echo "\n\n" . "Daemon -> " . $db->data['pid'] . ' (already started)' . "\n\n\n";

                // Closing the process
                exit;
            }
        }
        else
        {// Value not found
            // (Getting the value)
            $db->data['pid'] = getmypid();

            // (Saving the JDB)
            $db->save();
        }



        // (Creating a SystemService)
        $daemon = new SystemService();



        // (Handling the signal)
        $daemon->handle_signal
        (
            function ($signal) use ($db, $d)
            {
                // (Removing the JDB)
                $db->remove();



                // (Running the process)
                Process::run( "php scheduler.php stop" );



                // (Calling the function)
                $d->stop($signal);
            }
        )
        ;

        // (Running the daemon)
        $daemon->run
        (
            function () use ($db, $d, $daemon)
            {// (StartUp)
                // Printing the value
                echo "\n\n" . "Daemon -> " . $db->data['pid'] . "\n\n\n";



                // (Starting the process)
                $process = Process::start( "php scheduler.php start" );

                if ( !$process )
                {// (Unable to start the process)
                    // Printing the value
                    echo "\n\nUnable to start the scheduler\n\n\n";

                    // Closing the process
                    exit;
                }



                // (Getting the value)
                $d->startup_ts = $daemon->startup_ts;



                // (Calling the function)
                $d->startup();
            },

            function () use ($d)
            {// (Tick)
                // (Calling the function)
                $d->tick();
            }
        )
        ;
    break;

    case 'stop':
        // (Getting the value)
        $db = JDB::load( $db_file_path );

        if ( $db->data['pid'] )
        {// Value found
            // (Running the process)
            Process::run( "php scheduler.php stop" );

            // (Killing the process)
            Process::kill( $db->data['pid'] );
        }



        // (Calling the function)
        $d->stop(SIGTERM);
    break;

    case 'restart':
        // (Getting the values)
        $executor = "php " . $argv[0];



        // (Executing the commands)
        system("$executor stop");
        system("$executor start");
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