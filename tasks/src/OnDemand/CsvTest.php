<?php



namespace App\Tasks\OnDemand;



use \Solenoid\Core\Task\Task;
use \Solenoid\Core\Storage;

use \Solenoid\CSV\CSV;



class CsvTest extends Task
{
    public static array $tags = [ 'test' ];



    # Returns [void]
    public function build ()
    {
        // (Getting the value)
        $ts = time();



        // (Setting the value)
        $records =
        [
            [
                'sender'   => 'john.doe@gmail.com',
                'receiver' => 'livia.johnson@hotmail.ca',

                'message'  => "Welcome \"Livia\" to our portal !\n\nThis week we talk about our company policies;",
                'datetime' => date( 'c', $ts )
            ],

            [
                'sender'   => 'livia.johnson@hotmail.ca',
                'receiver' => 'john.doe@gmail.com',

                'message'  => "Thanks John for the hiring.\nYesterday I meet Niccolò",
                'datetime' => date( 'c', $ts + 2 * 3600 )
            ]
        ]
        ;



        // (Getting the value)
        $file_content = ( new CSV( array_keys( $records[0] ), $records ) )->build();



        // (Writing to the file)
        Storage::select( 'local' )->write( '/csv/file-hd.csv', $file_content );
    }

    # Returns [void]
    public function parse ()
    {
        // (Getting the value)
        $file_content = Storage::select( 'local' )->read( '/csv/file-hd.csv' );



        // (Getting the value)
        $eol = CSV::detect_eol( $file_content );

        if ( $eol === false )
        {// (Unable to detect the EOL)
            // Printing the value
            echo "Unable to detect the EOL\n";

            // Closing the process
            exit;
        }



        // (Getting the value)
        $separator = CSV::detect_separator( $file_content, $eol );

        if ( $separator === false )
        {// (Unable to detect the column separator)
            // Printing the value
            echo "Unable to detect the column separator\n";

            // Closing the process
            exit;
        }



        // (Parsing the CSV)
        $csv = CSV::parse( $file_content, CSV::TYPE_HD, $eol, $separator );



        // Printing the value
        print_r( $csv );
    }
}



?>