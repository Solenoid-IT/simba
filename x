#!/usr/bin/php
<?php



// (Setting the value)
error_reporting( E_ERROR );



// (Setting the values)
$helper =
    <<<EOD


    AVAILABLE COMMANDS


    x install

    x init

    x make:lib <id>

    x make:store <id>
    x make:service <id>

    x make:middleware <id>

    x make:task <id>

    x make:controller <id>

    x mysql build <root-username>
    x mysql extract-models

    x daemon register ?<name>
    x daemon start
    x daemon stop
    x daemon restart

    x scheduler start
    x scheduler stop
    x scheduler restart
    x scheduler enable
    x scheduler disable
    x scheduler task <id> <action=enable|disable>

    x set-envs
    x dev
    x build
    x release
    x task <path> <method> ...<args>



    EOD
;



// Returns [assoc]
function get_envs ()
{
    // (Getting the value)
    $app_config = json_decode( file_get_contents( __DIR__ . '/app.json' ), true );



    // (Getting the value)
    $envs =
    [
        'APP_ID'         => $app_config['id'],
        'APP_NAME'       => $app_config['name'],
        'APP_URL'        => 'https://' . $app_config['id'],

        'BE_HOST'        => $app_config['id']
    ]
    ;



    // Returning the value
    return $envs;
}



switch ( $argv[1] )
{
    case 'install':
        // (Setting the cwd)
        chdir( __DIR__ );



        // (Executing the cmds)
        system('composer update');
        system('composer install');
        system('composer dump-autoload');
    break;

    case 'init':
        // (Setting the cwd)
        chdir( __DIR__ );



        // (Executing the cmd)
        system('php x install');



        // (Executing the command)
        #echo shell_exec("sudo php x perms set");



        // (Including the files)
        include_once( __DIR__ . '/autoload.php' );



        // (Getting the value)
        $app_config = json_decode( file_get_contents( __DIR__ . '/app.json' ), true );



        # Returns [string]
        function explicit_path (string $base, string $path)
        {
            // Returning the value
            return preg_replace( '/^\./', $base, $path );
        }



        // (Creating a Storage)
        $credentials_storage = new \Solenoid\Core\Storage( explicit_path( __DIR__, $app_config['credentials']['folder_path'] ), true );



        // (Getting the value)
        $mysql_credentials_file_path = '/mysql/data.json';

        if ( !file_exists( $credentials_storage . $mysql_credentials_file_path ) )
        {// (File not found)
            // (Setting the value)
            $credentials_content =
                <<<EOD
                {
                    "local":
                    {
                        "simba_db":
                        {
                            "host":     "localhost",
                            "port":     null,
                            "username": "simba_db",
                            "password": ""
                        }
                    }
                }
                EOD
            ;

            if ( !$credentials_storage->write( $mysql_credentials_file_path, $credentials_content ) )
            {// (Unable to write to the file)
                // (Setting the value)
                $message = "Unable to write to the file '" . $credentials_storage . $mysql_credentials_file_path . "'";

                // Throwing an exception
                throw new \Exception($message);

                // Closing the process
                exit;
            }



            // (Executing the cmd)
            system("sudo chmod 664 \"" . $credentials_storage . $mysql_credentials_file_path .  "\"");
        }



        // (Getting the value)
        $smtp_credentials_file_path = '/smtp/data.json';

        if ( !file_exists( $credentials_storage . $smtp_credentials_file_path ) )
        {// (File not found)
            // (Setting the value)
            $credentials_content =
                <<<EOD
                {
                    "service":
                    {
                        "host":            "",
                        "port":            null,
                        "username":        "",
                        "password":        "",
                        "encryption_type": "ssl"
                    }
                }
                EOD
            ;

            if ( !$credentials_storage->write( $smtp_credentials_file_path, $credentials_content ) )
            {// (Unable to write to the file)
                // (Setting the value)
                $message = "Unable to write to the file '" . $credentials_storage . $smtp_credentials_file_path . "'";

                // Throwing an exception
                throw new \Exception($message);

                // Closing the process
                exit;
            }



            // (Executing the cmd)
            system("sudo chmod 664 \"" . $credentials_storage . $smtp_credentials_file_path .  "\"");
        }



        // (Getting the value)
        $system_credentials_file_path = '/system/data.json';

        if ( !file_exists( $credentials_storage . $system_credentials_file_path ) )
        {// (File not found)
            // (Setting the value)
            $credentials_content =
                <<<EOD
                {
                    "mysql":
                    {
                        "rpu_username": "root"
                    },

                    "idk":
                    {
                        "passphrase": ""
                    },

                    "rpc":
                    {
                        "token": ""
                    },

                    "api":
                    {
                        "ip-info": "",
                        "ua-info": ""
                    }
                }
                EOD
            ;

            if ( $credentials_storage->write( $system_credentials_file_path, $credentials_content ) === false )
            {// (Unable to write to the file)
                // (Setting the value)
                $message = "Unable to write to the file '" . $credentials_storage . $system_credentials_file_path . "'";

                // Throwing an exception
                throw new \Exception($message);

                // Closing the process
                exit;
            }



            // (Executing the cmd)
            system("sudo chmod 664 \"" . $credentials_storage . $system_credentials_file_path .  "\"");
        }



        foreach ( $app_config['storages'] as $storage )
        {// Processing each entry
            // (Getting the value)
            $folder_path = explicit_path( __DIR__, $storage['path'] );

            if ( !( new \Solenoid\Core\Storage( $folder_path, true ) )->make_dir( '/' ) )
            {// (Unable to make the directory)
                // (Setting the value)
                $message = "Unable to make the directory '$folder_path'";

                // Throwing an exception
                throw new \Exception($message);

                // Closing the process
                exit;
            }



            // (Executing the cmd)
            system("sudo chmod 2775 \"$folder_path\"");
        }



        foreach ( $app_config['views'] as $k => $folder_path )
        {// Processing each entry
            // (Getting the value)
            $folder_path = explicit_path( __DIR__, $folder_path );

            if ( !( new \Solenoid\Core\Storage( $folder_path, true ) )->make_dir( '/' ) )
            {// (Unable to make the directory)
                // (Setting the value)
                $message = "Unable to make the directory '$folder_path'";

                // Throwing an exception
                throw new \Exception($message);

                // Closing the process
                exit;
            }



            // (Executing the cmd)
            system("sudo chmod 2775 \"$folder_path\"");
        }



        // (Getting the value)
        $views_cache_folder_path = explicit_path( __DIR__, $app_config['views']['cache_folder_path'] );

        if ( is_dir( $views_cache_folder_path ) )
        {// (Directory found)
            // (Executing the cmd)
            system("sudo rm -rf \"$views_cache_folder_path\"/*");
        }



        // (Getting the value)
        $core_folder_path = realpath( __DIR__ . '/..' );



        // (Getting the values)
        $core_user  = posix_getpwuid( fileowner( $core_folder_path ) )['name'];
        $core_group = posix_getpwuid( filegroup( $core_folder_path ) )['name'];



        // (Executing the cmd)
        system("sudo chown -R $core_user:$core_group \"$core_folder_path\"");



        /*

        // (Executing the cmd)
        system("sudo rm -rf ./svelte/node_modules");

        // (Setting the cwd)
        chdir( "$core_folder_path/svelte" );

        // (Executing the cmd)
        system("npm install");

        */



        // Printing the value
        echo "\n\nApp has been initialized. Visit 'https://kb.solenoid.it/simba@1.0.0' for configuring the app\n\n\n";
    break;



    case 'make:lib':
        // (Including the file)
        include_once( __DIR__ . '/autoload.php' );



        // (Creating a Storage)
        $storage = new \Solenoid\Core\Storage( __DIR__ . '/lib/src', true );



        // (Getting the value)
        $id = $argv[2];



        // (Getting the value)
        $folder_path = "/$id";

        if ( $storage->dir_exists( $folder_path ) )
        {// (Directory found)
            // Printing the value
            echo "\n\nCannot create the library :: Library '$id' already exists !\n\n\n";
        }
        else
        {// (Directory not found)
            // (Getting the values)
            $sample_file_path = "$folder_path/src/Sample.php";
            $sample_ns        = str_replace( '/', '\\', $id );
            $sample_content   =
                <<<EOD
                <?php



                namespace App\Libraries\\$sample_ns;
                
                
                
                class Sample
                {
                    # Returns [string]
                    public static function build (string \$key, array \$value = [])
                    {
                        // Returning the value
                        return \$key . ' -> ' . json_encode( \$value );
                    }
                }
                
                
                
                ?>
                EOD
            ;



            if ( !$storage->write( $sample_file_path, $sample_content ) )
            {// (Unable to write to the file)
                // (Setting the value)
                $message = "Unable to write to the file '{$storage}{$sample_file_path}'";

                // Throwing an exception
                throw new \Exception($message);

                // Closing the process
                exit;
            }



            // Printing the value
            echo "\n\nFile `{$storage}{$sample_file_path}` has been created !\n\n\n";
        }
    break;

    case 'make:store':
        // (Including the file)
        include_once( __DIR__ . '/autoload.php' );



        // (Creating a Storage)
        $storage = new \Solenoid\Core\Storage( __DIR__ . '/stores', true );



        // (Getting the value)
        $id = $argv[2];



        // (Getting the value)
        $file_path = "/$id.php";

        if ( $storage->file_exists( $file_path ) )
        {// (File found)
            // Printing the value
            echo "\n\nCannot create the store :: File '{$storage}{$file_path}' already exists !\n\n\n";

            // Closing the process
            exit;
        }



        // (Getting the values)
        $parts        = explode( '/', $id );
        $class_name   = $parts[ count( $parts ) - 1 ];
        $class_path   = count( $parts ) === 1 ? '' : '\\' . implode( '\\', array_slice( $parts, 0, count( $parts ) - 1 ) );
        $file_content =
            <<<EOD
            <?php



            namespace App\Stores$class_path;



            use \Solenoid\Core\Store;



            class $class_name extends Store
            {
                private static self \$instance;



                # Returns [self]
                private function __construct ()
                {
                    
                }



                # Returns [self]
                public static function fetch ()
                {
                    if ( !isset( self::\$instance ) )
                    {// Value not found
                        // (Getting the value)
                        self::\$instance = new self();
                    }



                    // Returning the value
                    return self::\$instance;
                }



                # Returns [int]
                public function fetch_time ()
                {
                    // Returning the value
                    return time();
                }
            }



            ?>
            EOD
        ;



        if ( !$storage->write( $file_path, $file_content ) )
        {// (Unable to write to the file)
            // (Setting the value)
            $message = "Unable to write to the file '{$storage}{$file_path}'";

            // Throwing an exception
            throw new \Exception($message);

            // Closing the process
            exit;
        }



        // Printing the value
        echo "\n\nFile `{$storage}{$file_path}` has been created !\n\n\n";
    break;

    case 'make:service':
        // (Including the file)
        include_once( __DIR__ . '/autoload.php' );



        // (Creating a Storage)
        $storage = new \Solenoid\Core\Storage( __DIR__ . '/services', true );



        // (Getting the value)
        $id = $argv[2];



        // (Getting the value)
        $file_path = "/$id.php";

        if ( $storage->file_exists( $file_path ) )
        {// (File found)
            // Printing the value
            echo "\n\nCannot create the service :: File '{$storage}{$file_path}' already exists !\n\n\n";

            // Closing the process
            exit;
        }



        // (Getting the values)
        $parts        = explode( '/', $id );
        $class_name   = $parts[ count( $parts ) - 1 ];
        $class_path   = count( $parts ) === 1 ? '' : '\\' . implode( '\\', array_slice( $parts, 0, count( $parts ) - 1 ) );
        $file_content =
            <<<EOD
            <?php



            namespace App\Services$class_path;



            use \Solenoid\Core\Service;

            use \Solenoid\HTTP\Request;



            class $class_name extends Service
            {
                # Returns [assoc] | Throws [Exception]
                public static function fetch_url ()
                {
                    // Returning the value
                    return Request::fetch()->url;
                }
            }



            ?>
            EOD
        ;



        if ( !$storage->write( $file_path, $file_content ) )
        {// (Unable to write to the file)
            // (Setting the value)
            $message = "Unable to write to the file '{$storage}{$file_path}'";

            // Throwing an exception
            throw new \Exception($message);

            // Closing the process
            exit;
        }



        // Printing the value
        echo "\n\nFile `{$storage}{$file_path}` has been created !\n\n\n";
    break;

    case 'make:middleware':
        // (Including the file)
        include_once( __DIR__ . '/autoload.php' );



        // (Creating a Storage)
        $storage = new \Solenoid\Core\Storage( __DIR__ . '/middlewares/src', true );



        // (Getting the value)
        $id = $argv[2];



        // (Getting the value)
        $file_path = "/$id.php";

        if ( $storage->file_exists( $file_path ) )
        {// (File found)
            // Printing the value
            echo "\n\nCannot create the middleware :: File '{$storage}{$file_path}' already exists !\n\n\n";

            // Closing the process
            exit;
        }



        // (Getting the values)
        $parts        = explode( '/', $id );
        $class_name   = $parts[ count( $parts ) - 1 ];
        $class_path   = count( $parts ) === 1 ? '' : '\\' . implode( '\\', array_slice( $parts, 0, count( $parts ) - 1 ) );
        $file_content =
            <<<EOD
            <?php



            namespace App\Middlewares$class_path;



            use \Solenoid\Core\Middleware;

            use \Solenoid\HTTP\Request;



            class $class_name extends Middleware
            {
                
                # Returns [bool] | Throws [Exception]
                public static function run ()
                {
                    // Returning the value
                    return Request::fetch()->headers['Auth-Token'] === 'test';
                }
            }



            ?>
            EOD
        ;



        if ( !$storage->write( $file_path, $file_content ) )
        {// (Unable to write to the file)
            // (Setting the value)
            $message = "Unable to write to the file '{$storage}{$file_path}'";

            // Throwing an exception
            throw new \Exception($message);

            // Closing the process
            exit;
        }



        // Printing the value
        echo "\n\nFile `{$storage}{$file_path}` has been created !\n\n\n";
    break;

    case 'make:task':
        // (Including the file)
        include_once( __DIR__ . '/autoload.php' );



        // (Creating a Storage)
        $storage = new \Solenoid\Core\Storage( __DIR__ . '/tasks/src', true );



        // (Getting the value)
        $id = $argv[2];



        // (Getting the value)
        $file_path = "/$id.php";

        if ( $storage->file_exists( $file_path ) )
        {// (File found)
            // Printing the value
            echo "\n\nCannot create the task :: File '{$storage}{$file_path}' already exists !\n\n\n";

            // Closing the process
            exit;
        }



        // (Getting the values)
        $parts        = explode( '/', $id );
        $class_name   = $parts[ count( $parts ) - 1 ];
        $class_path   = count( $parts ) === 1 ? '' : '\\' . implode( '\\', array_slice( $parts, 0, count( $parts ) - 1 ) );
        $file_content =
            <<<EOD
            <?php



            namespace App\Tasks$class_path;



            use \Solenoid\Core\Task\Task;

            use \Solenoid\Core\Storage;



            class $class_name extends Task
            {
                public static array \$tags = [ 'test' ];



                # Returns [void]
                public function run (string \$file_ext = 'txt')
                {
                    // (Writing to the storage)
                    Storage::select('local')->write( "/a/b/c/d/e/file.\$file_ext", date('c') );

                    // (Writing to the storage)
                    Storage::select('local')->write( "/../a/b/c/d/e/file-ext.\$file_ext", 'Cannot write here !!!' );



                    # php x task $id "txt"
                }
            }



            ?>
            EOD
        ;



        if ( !$storage->write( $file_path, $file_content ) )
        {// (Unable to write to the file)
            // (Setting the value)
            $message = "Unable to write to the file '{$storage}{$file_path}'";

            // Throwing an exception
            throw new \Exception($message);

            // Closing the process
            exit;
        }



        // Printing the value
        echo "\n\nFile `{$storage}{$file_path}` has been created !\n\n\n";
    break;

    case 'make:controller':
        // (Including the file)
        include_once( __DIR__ . '/autoload.php' );



        // (Creating a Storage)
        $storage = new \Solenoid\Core\Storage( __DIR__ . '/controllers', true );



        // (Getting the value)
        $id = $argv[2];



        // (Getting the value)
        $file_path = "/$id.php";

        if ( $storage->file_exists( $file_path ) )
        {// (File found)
            // Printing the value
            echo "\n\nCannot create the controller :: File '{$storage}{$file_path}' already exists !\n\n\n";

            // Closing the process
            exit;
        }



        // (Getting the values)
        $parts        = explode( '/', $id );
        $class_name   = $parts[ count( $parts ) - 1 ];
        $class_path   = count( $parts ) === 1 ? '' : '\\' . implode( '\\', array_slice( $parts, 0, count( $parts ) - 1 ) );
        $file_content =
            <<<EOD
            <?php



            namespace App\Controllers$class_path;



            use \Solenoid\Core\MVC\Controller;

            use \Solenoid\HTTP\Request;
            use \Solenoid\HTTP\Server;
            use \Solenoid\HTTP\Status;
            use \Solenoid\HTTP\Response;

            use \App\Middlewares\RPC\Parser as RPC;



            class $class_name extends Controller
            {
                # Returns [void]
                public function get ()
                {
                    // Returning the value
                    return Request::fetch()->url;
                }

                # Returns [void]
                public function rpc ()
                {
                    switch ( RPC::\$subject )
                    {
                        case 'url':
                            switch ( RPC::\$verb )
                            {
                                case 'fetch':
                                    // Returning the value
                                    return
                                        Server::send( new Response( new Status(200), [], Request::fetch()->url ) )
                                    ;
                                break;
                            }
                        break;
                    }
                }
            }



            ?>
            EOD
        ;



        if ( !( $storage )->write( $file_path, $file_content ) )
        {// (Unable to write to the file)
            // (Setting the value)
            $message = "Unable to write to the file '{$storage}{$file_path}'";

            // Throwing an exception
            throw new \Exception($message);

            // Closing the process
            exit;
        }



        // Printing the value
        echo "\n\nFile `{$storage}{$file_path}` has been created !\n\n\n";
    break;



    case 'mysql':
        switch ( $argv[2] )
        {
            case 'reset':
                # Returns [string]
                function replace_values (string $content, array $values)
                {
                    foreach ( $values as $k => $v )
                    {// Processing each entry
                        // (Getting the value)
                        $content = str_replace( $k, $v, $content );
                    }



                    // Returning the value
                    return $content;
                }



                // (Getting the value)
                $app_config = json_decode( file_get_contents( __DIR__ . '/app.json' ) );



                // (Getting the value)
                $credentials_folder_path = preg_match( '/^\./', __DIR__, $app_config['credentials'] );



                // (Getting the value)
                $mysql_credentials = json_decode( file_get_contents( "$credentials_folder_path/mysql/data.json" ), true );



                // (Setting the value)
                $dbs = [];

                foreach ( glob( __DIR__ . '/databases/*/*' ) as $folder_path )
                {// Processing each entry
                    // (Getting the values)
                    $profile = basename( dirname( $folder_path ) );
                    $db_name = basename( $folder_path );



                    // (Getting the value)
                    $constants =
                    [
                        '%( DB_NAME )%' => $db_name,
                        '%( DB_USER )%' => $mysql_credentials[$profile][$db_name]['username'],
                        '%( DB_PASS )%' => $mysql_credentials[$profile][$db_name]['password']
                    ]
                    ;



                    // (Setting the value)
                    $sql_context_pad_length = 86;



                    // (Getting the value)
                    $merge_sql_file = "$folder_path/merge.sql";

                    // (Writing to the file)
                    file_put_contents( $merge_sql_file, '' );



                    foreach ( [ 'reset.sql' ] as $file_name )
                    {// Processing each entry
                        // (Getting the value)
                        $sql_context = str_pad( '-- ' . strtoupper( pathinfo( $file_name, PATHINFO_FILENAME ) ) . ' ', $sql_context_pad_length, '-', STR_PAD_RIGHT );



                        // (Writing to the file)
                        file_put_contents( $merge_sql_file, "$sql_context\n\n\n" . replace_values( file_get_contents( "$folder_path/$file_name" ), $constants ) . "\n\n\n", FILE_APPEND );
                    }



                    // (Getting the value)
                    $rpu_username = json_decode( file_get_contents( "$credentials_folder_path/system/data.json" ), true )['mysql']['rpu_username'];



                    // (Executing the cmd)
                    system("sudo mysql -u $rpu_username -p < $merge_sql_file");

                    // (Removing the file)
                    unlink( $merge_sql_file );



                    // (Appending the value)
                    $dbs[] = "$profile/$db_name";
                }



                if ( $dbs )
                {// Value is not empty
                    // (Getting the value)
                    $dbs_csv = '"' . implode( '";"', $dbs ) . '"';

                    // Printing the value
                    echo "\n\nDatabases $dbs_csv have been reset\n\n\n";
                }
            break;

            case 'build':
                # Returns [string]
                function replace_values (string $content, array $values)
                {
                    foreach ( $values as $k => $v )
                    {// Processing each entry
                        // (Getting the value)
                        $content = str_replace( $k, $v, $content );
                    }



                    // Returning the value
                    return $content;
                }



                // (Getting the value)
                $app_config = json_decode( file_get_contents( __DIR__ . '/app.json' ), true );



                // (Getting the value)
                $credentials_folder_path = preg_match( '/^\./', __DIR__, $app_config['credentials'] );



                // (Getting the value)
                $mysql_credentials = json_decode( file_get_contents( "$credentials_folder_path/mysql/data.json" ), true );



                // (Setting the value)
                $dbs = [];

                foreach ( glob( __DIR__ . '/databases/*/*' ) as $folder_path )
                {// Processing each entry
                    // (Getting the values)
                    $profile = basename( dirname( $folder_path ) );
                    $db_name = basename( $folder_path );



                    // (Getting the value)
                    $constants =
                    [
                        '%( DB_NAME )%' => $db_name,
                        '%( DB_USER )%' => $mysql_credentials[$profile][$db_name]['username'],
                        '%( DB_PASS )%' => $mysql_credentials[$profile][$db_name]['password']
                    ]
                    ;



                    // (Setting the value)
                    $sql_context_pad_length = 86;



                    // (Getting the value)
                    $merge_sql_file = "$folder_path/merge.sql";

                    // (Writing to the file)
                    file_put_contents( $merge_sql_file, '' );



                    foreach ( [ 'schema.sql', 'contents.sql', 'user.sql' ] as $file_name )
                    {// Processing each entry
                        // (Getting the value)
                        $sql_context = str_pad( '-- ' . strtoupper( pathinfo( $file_name, PATHINFO_FILENAME ) ) . ' ', $sql_context_pad_length, '-', STR_PAD_RIGHT );



                        // (Writing to the file)
                        file_put_contents( $merge_sql_file, "$sql_context\n\n\n" . replace_values( file_get_contents( "$folder_path/$file_name" ), $constants ) . "\n\n\n", FILE_APPEND );
                    }



                    // (Getting the value)
                    $sql_context = str_pad( '-- EVENTS ', $sql_context_pad_length, '-', STR_PAD_RIGHT );

                    // (Writing to the file)
                    file_put_contents( $merge_sql_file, "$sql_context\n\n\n", FILE_APPEND );

                    foreach ( glob( "$folder_path/events/*" ) as $file_path )
                    {// Processing each entry
                        // (Writing to the file)
                        file_put_contents( $merge_sql_file, replace_values( file_get_contents( "$file_path" ), $constants ) . "\n\n\n", FILE_APPEND );
                    }



                    // (Getting the value)
                    $sql_context = str_pad( '-- VIEWS ', $sql_context_pad_length, '-', STR_PAD_RIGHT );

                    // (Writing to the file)
                    file_put_contents( $merge_sql_file, "$sql_context\n\n\n", FILE_APPEND );

                    foreach ( glob( "$folder_path/views/*" ) as $file_path )
                    {// Processing each entry
                        // (Writing to the file)
                        file_put_contents( $merge_sql_file, replace_values( file_get_contents( "$file_path" ), $constants ) . "\n\n\n", FILE_APPEND );
                    }



                    // (Getting the value)
                    $rpu_username = json_decode( file_get_contents( "$credentials_folder_path/system/data.json" ), true )['mysql']['rpu_username'];



                    // (Executing the cmd)
                    #echo shell_exec("sudo mysql -u $username -p$password $db_name < $merge_sql_file");
                    system("sudo mysql -u $rpu_username -p < $merge_sql_file");

                    // (Removing the file)
                    unlink( $merge_sql_file );



                    // (Appending the value)
                    $dbs[] = "$profile/$db_name";
                }



                if ( $dbs )
                {// Value is not empty
                    // (Getting the value)
                    $dbs_csv = '"' . implode( '";"', $dbs ) . '"';

                    // Printing the value
                    echo "\n\nDatabases $dbs_csv have been imported\n\n\n";
                }
            break;

            case 'extract-models':
                // (Including the file)
                include_once( __DIR__ . '/autoload.php' );



                # Returns [string]
                function snake_to_camel (string $value)
                {
                    // Returning the value
                    return lcfirst( str_replace( '_', '', ucwords( str_replace( '.', '_', $value ), '_' ) ) );
                }

                # Returns [string]
                function replace_values (string $content, array $values)
                {
                    foreach ( $values as $k => $v )
                    {// Processing each entry
                        // (Getting the value)
                        $content = str_replace( $k, $v, $content );
                    }



                    // Returning the value
                    return $content;
                }



                // (Setting the value)
                $dbs = [];

                foreach ( glob( __DIR__ . '/databases/*/*/schema.sql' ) as $file_path )
                {// Processing each entry
                    // (Getting the values)
                    $profile = basename( dirname( $file_path, 2 ) );
                    $db_name = basename( dirname( $file_path ) );



                    // (Getting the value)
                    $constants =
                    [
                        '%( DB_NAME )%' => $db_name
                    ]
                    ;



                    // (Getting the value)
                    $model_db_path = __DIR__ . "/models/$profile/$db_name";

                    if ( !is_dir( $model_db_path ) )
                    {// (Directory not found)
                        if ( !mkdir( $model_db_path, 0777, true ) )
                        {// (Unable to make the directory)
                            // (Setting the value)
                            $message = "Unable to make the directory '$model_db_path'";

                            // Throwing an exception
                            throw new \Exception($message);

                            // Closing the process
                            exit;
                        }
                    }



                    // (Getting the value)
                    $database = \Solenoid\MySQL\DDL\Database::parse( replace_values( file_get_contents( $file_path ), $constants ) );

                    foreach ( $database->entities as $entity )
                    {// Processing each entry
                        // (Getting the values)
                        $model_name      = ucfirst( snake_to_camel( $entity->name ) );
                        $model_file_path = "$model_db_path/$model_name.php";

                        if ( !file_exists( $model_file_path ) )
                        {// (File not found)
                            // (Setting the value)
                            $ai_field = false;

                            foreach ( $entity->fields as $name => $descriptor )
                            {// Processing each entry
                                if ( stripos( $descriptor, 'AUTO_INCREMENT' ) !== false )
                                {// Value found
                                    // (Getting the value)
                                    $ai_field = $name;

                                    // Breaking the iteration
                                    break;
                                }
                            }



                            // (Setting the value)
                            $model_file_content =
                                <<<EOD
                                <?php



                                namespace App\Models\\$profile\\$db_name;



                                use \Solenoid\MySQL\Model;

                                use \Solenoid\MySQL\Query;

                                use \App\Stores\Connections\MySQL\Store as MySQLConnectionsStore;



                                class $model_name extends Model
                                {
                                    private static self \$instance;



                                    # Returns [self]
                                    private function __construct ()
                                    {
                                        // (Calling the function)
                                        parent::__construct( MySQLConnectionsStore::fetch()->connections['$profile/$db_name'], '$database->name', '$entity->name' );
                                    }



                                    # Returns [self]
                                    public static function fetch ()
                                    {
                                        if ( !isset( self::\$instance ) )
                                        {// Value not found
                                            // (Getting the value)
                                            self::\$instance = new self();
                                        }



                                        // (Resetting the condition)
                                        ( self::\$instance )->reset();



                                        // Returning the value
                                        return self::\$instance;
                                    }



                                    # Returns [Cursor|false]
                                    public function view ()
                                    {
                                        // Returning the value
                                        return ( new Query( \$this->connection ) )->from( \$this->database, "view::\$this->table::all" )->select_all()->run();
                                    }
                                }



                                ?>
                                EOD
                            ;

                            if ( file_put_contents( $model_file_path, $model_file_content ) === false )
                            {// (Unable to write to the file)
                                // (Setting the value)
                                $message = "Unable to write to the file '$model_file_path'";

                                // Throwing an exception
                                throw new \Exception($message);

                                // Closing the process
                                exit;
                            }
                        }
                    }



                    // (Appending the value)
                    $dbs[] = "$profile/$db_name";
                }



                if ( $dbs )
                {// Value is not empty
                    // (Getting the value)
                    $models_csv = '"' . implode( '";"', $dbs ) . '"';



                    // Printing the value
                    echo "\n\nModels $models_csv have been extracted\n\n\n";
                }
            break;
        }
    break;



    case 'daemon':
        switch ( $argv[2] )
        {
            case 'start':
                // (Setting the cwd)
                chdir( __DIR__ );



                // (Executing the cmd)
                system('php ./system/daemon.php start');
            break;

            case 'stop':
                // (Setting the cwd)
                chdir( __DIR__ );



                // (Executing the cmd)
                system('php ./system/daemon.php stop');
            break;

            case 'restart':
                // (Setting the cwd)
                chdir( __DIR__ );



                // (Executing the cmd)
                system('php ./system/daemon.php restart');
            break;



            case 'register':
                if ( isset( $argv[3] ) )
                {// Value found
                    // (Getting the value)
                    $name = $argv[3];
                }
                else
                {// Value not found
                    // (Getting the value)
                    $app_config = json_decode( file_get_contents( __DIR__ . '/app.json' ) );

                    // (Getting the value)
                    $name = "$app_config->id.simba";
                }



                // (Getting the values)
                $description     = '';
                $executable_path = "php " . __DIR__ . '/system/daemon.php';



                // (Executing the cmd)
                system("simba service create \"$name\" \"$description\" \"$executable_path\"");
            break;
        }
    break;

    case 'scheduler':
        switch ( $argv[2] )
        {
            case 'start':
                // (Setting the cwd)
                chdir( __DIR__ );



                // (Executing the cmd)
                system('php ./system/scheduler.php start');
            break;

            case 'stop':
                // (Setting the cwd)
                chdir( __DIR__ );



                // (Executing the cmd)
                system('php ./system/scheduler.php stop');
            break;

            case 'restart':
                // (Setting the cwd)
                chdir( __DIR__ );



                // (Executing the cmd)
                system('php ./system/scheduler.php restart');
            break;



            case 'enable':
                // (Getting the value)
                $file_path = __DIR__ . '/tasks/scheduler.json';



                // (Getting the value)
                $scheduler = json_decode( file_get_contents( $file_path ), true );

                // (Setting the value)
                $scheduler['enabled'] = true;

                // (Writing to the file)
                file_put_contents( $file_path, json_encode( $scheduler, JSON_PRETTY_PRINT ) );
            break;

            case 'disable':
                // (Getting the value)
                $file_path = __DIR__ . '/tasks/scheduler.json';



                // (Getting the value)
                $scheduler = json_decode( file_get_contents( $file_path ), true );

                // (Setting the value)
                $scheduler['enabled'] = false;

                // (Writing to the file)
                file_put_contents( $file_path, json_encode( $scheduler, JSON_PRETTY_PRINT ) );
            break;



            case 'task':
                // (Including the file)
                include_once( __DIR__ . '/autoload.php' );



                // (Getting the value)
                $task_id = $argv[3];

                switch ( $argv[4] )
                {
                    case 'enable':
                    case 'disable':
                        // (Getting the value)
                        $task_action = $argv[4];
                    break;

                    default:
                        // Printing the value
                        echo $helper;

                        // Closing the process
                        exit;
                }



                // (Getting the value)
                $file_path = __DIR__ . '/tasks/scheduler.json';



                // (Getting the value)
                $scheduler = json_decode( file_get_contents( $file_path ), true );

                foreach ( $scheduler['tasks'] as &$task )
                {// Processing each entry
                    if ( $task['id'] === $task_id )
                    {// Match OK
                        // (Setting the value)
                        $task['enabled'] = $task_action === 'enable';
                    }
                }

                // (Writing to the file)
                file_put_contents( $file_path, json_encode( $scheduler, JSON_PRETTY_PRINT ) );
            break;
        }
    break;



    case 'set-envs':
        // (Getting the value)
        $envs = get_envs();



        // (Setting the value)
        $envs_s = [];

        foreach ( $envs as $k => $v )
        {// Processing each entry
            // (Getting the value)
            $envs_s[] = "'$k': " . ( is_string( $v ) ? "'$v'" : $v );
        }



        // (Getting the value)
        $envs_s = implode( ",\n\t", $envs_s );



        // (Getting the values)
        $env_file_path    = __DIR__ . '/svelte/src/envs.js';
        $env_file_content =
            <<<EOD
            export const envs =
            {
                $envs_s
            }
            ;
            EOD
        ;

        if ( file_put_contents( $env_file_path, $env_file_content ) === false )
        {// (Unable to write to the file)
            // (Setting the value)
            $message = "Unable to write to the file '$env_file_path'";

            // Throwing an exception
            throw new \Exception($message);

            // Closing the process
            exit;
        }



        // (Getting the value)
        $core_file_path    = __DIR__ . '/svelte/.core';
        $core_file_content =
        [
            'dev_server'   =>
            [
                'host'     => 'front-dev.' . $envs['APP_ID'],
                'https'    =>
                [
                    'key'  => __DIR__ . '/svelte/cert/key.pem',
                    'cert' => __DIR__ . '/svelte/cert/cert.pem'
                ]
            ],

            'build_path'   => __DIR__ . '/web/build'
        ]
        ;

        if ( file_put_contents( $core_file_path, json_encode( $core_file_content, JSON_PRETTY_PRINT ) ) === false )
        {// (Unable to write to the file)
            // (Setting the value)
            $message = "Unable to write to the file '$core_file_path'";

            // Throwing an exception
            throw new \Exception($message);

            // Closing the process
            exit;
        }



        // (Including the file)
        include_once( __DIR__ . '/autoload.php' );



        // (Creating a Blade)
        $blade = new \eftec\bladeone\BladeOne( __DIR__ . '/svelte/src', __DIR__ . '/svelte/src/_cache' );



        // (Getting the value)
        $html_file_content = $blade->run( '/app.blade.php', [ 'envs' => $envs ] );



        // (Getting the value)
        $app_file_path = __DIR__ . '/svelte/src/app.html';

        if ( file_put_contents( $app_file_path, $html_file_content ) === false )
        {// (Unable to write to the file)
            // (Setting the value)
            $message = "Unable to write to the file '$app_file_path'";

            // Throwing an exception
            throw new \Exception($message);

            // Closing the process
            exit;
        }
    break;

    case 'set-ldns':
        // (Getting the value)
        $envs = get_envs();



        // (Getting the value)
        $app_id = $envs['APP_ID'];



        // (Getting the values)
        $hosts_file_path    = '/etc/hosts';
        $hosts_file_content = file_get_contents( $hosts_file_path );

        $core_config        = json_decode( file_get_contents( __DIR__ . '/svelte/.core' ), true );

        $dev_server_fqdn    = $core_config['dev_server']['host'];
        $localhost_entry    = "127.0.0.1 $dev_server_fqdn";

        if ( strpos( $hosts_file_content, $localhost_entry ) === false )
        {// Value not found
            if ( file_put_contents( $hosts_file_path, "\n\n# Simba [$app_id]\n$localhost_entry", FILE_APPEND ) === false )
            {// (Unable to write to the file)
                // (Setting the value)
                $message = "Unable to write to the file '$hosts_file_path'";

                // Throwing an exception
                throw new \Exception($message);

                // Closing the process
                exit;
            }
        }
    break;

    case 'dev':
        // (Executing the cmd)
        system('php x set-envs');



        // (Executing the cmd)
        system('sudo php x set-ldns');



        // (Getting the values)
        $core_config    = json_decode( file_get_contents( __DIR__ . '/svelte/.core' ), true );

        $key_file_path  = $core_config['dev_server']['https']['key'];
        $cert_file_path = $core_config['dev_server']['https']['cert'];



        // (Setting the cwd)
        chdir( __DIR__ . '/svelte' );



        if ( file_exists( $cert_file_path ) )
        {// (File found)
            // (Getting the value)
            $cert_info = openssl_x509_parse( file_get_contents( $cert_file_path ) );
        }
        else
        {// (File not found)
            // (Getting the value)
            $cert_info = false;
        }

        if ( $cert_info === false || time() >= $cert_info['validTo_time_t'] )
        {// (Certificate not found or is expired)
            // Printing the value
            echo "\n\n" . ( $cert_info === false ? 'Creating' : 'Renewing' ) . " the certificate ...\n\n\n";

            // (Executing the cmd)
            system( "openssl req -x509 -newkey rsa:4096 -keyout $key_file_path -out $cert_file_path -sha256 -days 3650 -nodes -subj \"/C=IT/ST=Italy/L=Turin/O=Solenoid-IT/OU=Solenoid-IT/CN=Solenoid-IT\"" );
        }



        // (Registering the shutdown function)
        register_shutdown_function
        (
            function ()
            {
                // (Setting the cwd)
                chdir( __DIR__ );

                // (Executing the cmd)
                system('php x build');
            }
        )
        ;



        // (Executing the cmd)
        #system("npm run dev -- --open --port 3000");
        #system("npm run dev -- --open");
        system("npm run dev");
    break;

    case 'build':
        // (Executing the cmd)
        system('php x set-envs');



        // (Setting the cwd)
        chdir( __DIR__ . '/svelte' );



        // (Executing the cmd)
        system("npm run build");



        // (Getting the value)
        $build_folder_path = json_decode( file_get_contents( __DIR__ . '/svelte/.core' ), true )['build_path'];



        // (Getting the value)
        $cap_dir = __DIR__ . '/capacitor';

        // (Setting the cwd)
        chdir( $cap_dir );



        // (Executing the command)
        echo shell_exec("rm -rf $cap_dir/android");
        echo shell_exec("rm -rf $cap_dir/ios");

        echo shell_exec('npx cap add android');
        echo shell_exec('npx cap add ios');



        // (Getting the value)
        $link = __DIR__ . '/capacitor/build';
        
        if ( is_link($link) ) unlink($link);

        // (Creating a symlink)
        symlink( $build_folder_path, $link );



        // (Executing the command)
        echo shell_exec('npx cap sync');



        // (Getting the value)
        $envs = get_envs();



        // (Including the file)
        include_once( __DIR__ . '/autoload.php' );



        // (Getting the value)
        $file_path_list = \Solenoid\System\Directory::select( $build_folder_path )->list( 0, '/\.html$/' );

        foreach ( $file_path_list as $file_path )
        {// Processing each entry
            // (Getting the value)
            $file_content = file_get_contents( $file_path );



            // (Replacing the text)
            $file_content = preg_replace( '/\.?\/_svelte/', 'https://' . $envs['APP_ID'] . '/build/_svelte', $file_content );



            /*
            
            // (Creating an HTMLBuilder)
            $html_builder = \Solenoid\HTML\Builder::create( $file_content );

            // (Appending the contents)
            $html_builder->append( 'head', "@include('assets/head.blade.php')" );
            $html_builder->append( 'body', "@include('assets/body.blade.php')" );

            // (Getting the value)
            $file_content = $html_builder->fetch_content();

            */



            // (Getting the value)
            $blade_file_path = preg_replace( '/\.html$/', '.blade.php', $file_path );



            // (Writing the content to the file)
            \Solenoid\System\File::select( $blade_file_path )->write( $file_content );
        }



        // (Printing the value)
        echo "\n\nApp -> https://{$envs['APP_ID']}\n\n\n";
    break;

    case 'release':
        // (Including the file)
        include_once( __DIR__ . '/release.php' );
    break;

    case 'task':
        if ( count( $argv ) === 2 )
        {// (There are no args)
            // Printing the value
            echo $helper;

            // Closing the process
            exit;
        }



        // (Setting the cwd)
        chdir( __DIR__ );



        // (Getting the value)
        $args = $argv;
        $args = array_splice( $args, 2 );
        $args = implode( ' ', $args );



        // (Executing the cmd)
        system( "php bootstrap.php $args" );
    break;



    default:
        // Printing the value
        echo $helper;
}



?>