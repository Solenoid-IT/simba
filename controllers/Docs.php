<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\Core\App\App;

use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Status;
use \Solenoid\HTTP\Response;

use \Solenoid\MySQL\Record;
use \Solenoid\MySQL\DateTime;

use \App\Models\local\simba_db\Document as DocumentModel;
use \App\Models\local\simba_db\Visitor as VisitorModel;
use \App\Services\Client as ClientService;
use \App\Middlewares\RPC\Parser as RPC;
use \App\Middlewares\User as UserMiddleware;



class Docs extends Controller
{
    # Returns [void]
    public function get ()
    {
        // (Getting the value)
        $route    = App::$target->args[0];
        $path     = App::$target->args[1];
        $user_sid = Request::fetch()->cookies['user'];



        // (Getting the value)
        $document = DocumentModel::fetch()->filter( [ [ 'path' => $path ] ] )->find();

        if ( $document )
        {// (Document found)
            if ( $user_sid )
            {// (Cookie found)
                if ( UserMiddleware::run() === false ) return;
            }



            if ( !$user_sid && $document->datetime->option->active )
            {// (User is not logged-in and Document is active)
                try
                {
                    // (Getting the value)
                    $client = ClientService::detect();



                    // (Getting the value)
                    $record =
                    [
                        'route'           => $route,

                        'document'        => $document->id,

                        'ip.address'      => $client['ip']['address'],
                        'ip.country.code' => $client['ip']['country']['code'],
                        'ip.country.name' => $client['ip']['country']['name'],
                        'ip.isp'          => $client['ip']['isp'],

                        'user_agent'      => $client['user_agent'],

                        'browser'         => $client['browser'],
                        'os'              => $client['os'],
                        'hw'              => $client['hw'],

                        'datetime.insert' => DateTime::fetch()
                    ]
                    ;

                    // (Inserting the record)
                    VisitorModel::fetch()->insert( [ $record ] );
                }
                catch (\Exception $e)
                {
                    // (Doing nothing)
                }
            }



            if ( $user_sid || $document->datetime->option->active )
            {// (User is logged-in or Document is active)
                // Printing the value
                echo $document->content;

                // Returning the value
                return;
            }
        }
    }

    # Returns [void]
    public function rpc ()
    {
        switch ( RPC::$verb )
        {
            case 'list_by_dir':
                // (Getting the value)
                $dir = RPC::$input->dir;
                $dir = $dir ? $dir : '/';
                $dir = $dir[ strlen($dir) - 1 ] === '/' ? $dir : "$dir/";



                // (Getting the value)
                $files = array_values
                (
                    array_map
                    (
                        function (Record $record)
                        {
                            // (Getting the value)
                            $record =
                            [
                                'path'            => '/docs' . $record->path,
                                'basename'        => basename( $record->path ),
                                'type'            => 'file',
                                'datetime'        =>
                                [
                                    'last_update' => $record->datetime->update ?? $record->datetime->insert
                                ]
                            ]
                            ;



                            // Returning the value
                            return new Record($record);
                        },

                        array_filter
                        (
                            DocumentModel::fetch()->query()
                                ->condition_start()
                                    ->where_field( null, 'datetime.option.active' )->is_not(null)
                                ->condition_end()
                                
                                ->order_by( null, 'id', 'DESC' )

                                ->select_all()

                                ->run()

                                ->list()
                            ,
                            
                            function (Record $record) use ($dir)
                            {
                                // Returning the value
                                return
                                    $record->datetime->option->active
                                        &&
                                    stripos( $record->path, $dir ) === 0
                                ;
                            }
                        )
                    )
                )
                ;



                // (Setting the value)
                $directories = [];

                foreach ( $files as $file )
                {// Processing each entry
                    // (Getting the value)
                    $directories[ dirname( $file->path ) ] = $file;
                }



                // (Getting the value)
                $directories = array_keys( $directories );



                // (Setting the value)
                $entries = $files;

                foreach ( $directories as $directory )
                {// Processing each entry
                    // (Appending the value)
                    $entries[] =
                    [
                        'path'            => $directory,
                        'basename'        => basename( $directory ),
                        'type'            => 'dir',
                        'datetime'        =>
                        [
                            'last_update' => null
                        ]
                    ]
                    ;
                }



                // (Getting the value)
                $entries = array_values
                (
                    array_filter
                    (
                        $entries,

                        function ($record) use ($dir)
                        {
                            switch ( $record->type )
                            {
                                case 'file':
                                    if ( substr_count( $record->path, '/' ) > substr_count( $dir, '/' ) + 1 )
                                    {// Match failed
                                        // Returning the value
                                        return false;
                                    }
                                break;

                                case 'dir':
                                    if ( $record->path . '/' === "/docs$dir" )
                                    {// Match failed
                                        // Returning the value
                                        return false;
                                    }
                                break;
                            }



                            // Returning the value
                            return true;
                        }
                    )
                )
                ;



                // (Getting the value)
                $data =
                [
                    'records' => $entries
                ]
                ;



                // Returning the value
                return
                    Server::send( new Response( new Status(200), [], $data ) )
                ;
            break;

            case 'list':
                // (Getting the value)
                $files = array_values
                (
                    array_map
                    (
                        function (Record $record)
                        {
                            // (Getting the value)
                            $record =
                            [
                                'path'            => '/docs' . $record->path,
                                'title'           => $record->title,
                                'description'     => $record->description,
                                'tag_list'        => $record->tag_list,
                                'datetime'        =>
                                [
                                    'last_update' => $record->datetime->update ?? $record->datetime->insert
                                ]
                            ]
                            ;



                            // Returning the value
                            return new Record($record);
                        },

                        DocumentModel::fetch()->view
                        (
                            DocumentModel::fetch()
                                ->condition_start()
                                    ->where_field( null, 'datetime.option.active' )->is_not(null)
                                ->condition_end()
                        ),
                    )
                )
                ;



                // (Getting the value)
                $data =
                [
                    'records' => $files
                ]
                ;



                // Returning the value
                return
                    Server::send( new Response( new Status(200), [], $data ) )
                ;
            break;
        }
    }
}



?>