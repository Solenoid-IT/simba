<?php



// (Including the files)
include_once( __DIR__ . '/autoload.php' );
include_once( __DIR__ . '/config.php' );
include_once( __DIR__ . '/gate.php' );
include_once( __DIR__ . '/middlewares/groups.php' );



use \Solenoid\Core\App\App;
use \Solenoid\Core\App\SysApp;
use \Solenoid\Core\App\WebApp;

use \Solenoid\Core\Env;
use \Solenoid\Core\Storage;
use \Solenoid\Core\Logger;
use \Solenoid\Core\Credentials;
use \Solenoid\Core\MVC\View;
use \Solenoid\Core\Routing\Target;
use \Solenoid\Perf\Analyzer;
use \Solenoid\HTTP\Request;



// (Detecting the mode)
App::detect_mode();

switch ( App::$mode )
{
    case 'cli':
        // (Adding the env)
        Env::add( 'dev', new Env( Env::TYPE_DEV, [ 'vps' ] ) );



        // (Creating a SysApp)
        $app = new SysApp( $app_config );
    break;

    case 'http':
        // (Adding the envs)
        Env::add( 'dev', new Env( Env::TYPE_DEV, [ $app_config['id'] ] ) );
        Env::add( 'prod', new Env( Env::TYPE_PROD, [ preg_replace( '/^dev\./', '', $app_config['id'] ) ] ) );



        // (Including the file)
        include_once( __DIR__ . '/routes.php' );

        // (Creating a WebApp)
        $app = new WebApp( $app_config );
    break;
}



foreach ( $app_config['storages'] as $storage )
{// Processing each entry
    // (Adding the storage)
    Storage::add( $storage['id'], new Storage( preg_replace( '/^\./', __DIR__, $storage['path'] ), true ) );
}



foreach ( $app_config['loggers'] as $logger )
{// Processing each entry
    // (Adding the logger)
    Logger::add( $logger['id'], new Logger( preg_replace( '/^\./', __DIR__, $logger['path'] ) ) );
}



// (Configuring the credentials)
Credentials::config( preg_replace( '/^\./', __DIR__, $app_config['credentials']['folder_path'] ) );



// (Configuring the views)
View::config( preg_replace( '/^\./', __DIR__, $app_config['views']['views_folder_path'] ), preg_replace( '/^\./', __DIR__, $app_config['views']['cache_folder_path'] ) );



// (Creating an Analyzer)
$performance_analyzer = new Analyzer();



// (Listening for the events)
Target::on
(
    'before-gate',
    function () use ($performance_analyzer)
    {
        // (Opening the analyzer)
        $performance_analyzer->open();
    }
)
;
Target::on
(
    'after-gate',
    function () use ($performance_analyzer, $app)
    {
        // (Closing the analyzer)
        $performance_analyzer->close();



        // (Pushing the message)
        Logger::select( App::$mode . '/activity' )->push( ( App::$mode === 'cli' ? $app->task : Request::fetch() ) . ' -> ' . $performance_analyzer );
    }
)
;
Target::on
(
    'error',
    function ($data) use ($app)
    {
        if ( App::$mode === 'http' )
        {// Match OK
            // (Setting the status)
            http_response_code( 500 );
        }



        // (Pushing the message)
        Logger::select( App::$mode . '/error' )->push( ( App::$mode === 'cli' ? $app->task : Request::fetch() ) . ' -> ' . $data['message'] );
    }
)
;



// (Running the app)
$app->run();



?>