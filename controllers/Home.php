<?php



namespace App\Controllers;



use \Solenoid\Core\MVC\Controller;

use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;

use \Solenoid\MySQL\Condition;
use \Solenoid\MySQL\Record;

use \App\Models\local\simba_db\Document as DocumentModel;
use \App\Middlewares\RPC\Parser as RPC;



class Home extends Controller
{
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

                        DocumentModel::fetch()->query()
                            ->condition_start()
                                ->where_field( null, 'datetime.option.active' )->is_not(null)
                                    ->and()
                                ->where_field( null, 'path' )->like( '', $dir )
                            ->condition_end()
                            
                            ->order_by( null, 'id', 'DESC' )
                            
                            ->select_all()
                        
                            ->run()

                            ->list()
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
                            switch ( $record['type'] )
                            {
                                case 'file':
                                    if ( substr_count( $record['path'], '/' ) > substr_count( $dir, '/' ) + 1 )
                                    {// Match failed
                                        // Returning the value
                                        return false;
                                    }
                                break;

                                case 'dir':
                                    if ( $record['path'] . '/' === "/docs$dir" )
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

                        DocumentModel::fetch()->view( ( new Condition() )->where_field( null, 'datetime.option.active' )->is_not(null) )->list()
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