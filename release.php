<?php



// (Getting the value)
$src_folder_path = realpath( __DIR__ . '/..' );



// (Getting the value)
$app_config = json_decode( file_get_contents( "$src_folder_path/app.json" ) );



// (Getting the value)
$app_id = preg_replace( '/^dev\./', '', $app_config->id );



// (Getting the value)
$dst_folder_path = "/var/www/$app_id/core";

if ( is_dir( $dst_folder_path ) )
{// (Directory found)
    if ( readline( "Directory '$dst_folder_path' already exists. Are you sure to overwrite the contents ? [Y/n]\n" ) !== 'Y' )
    {// (Confirmation is failed)
        // Printing the value
        echo "\n\nRelease has been interrupted\n\n\n";

        // Closing the process
        exit;
    }
}



// (Executing the cmds)
system("sudo rm -rf \"$dst_folder_path\"");
system("sudo mkdir -p \"$dst_folder_path\"");
system("sudo cp -a \"$src_folder_path/.\" \"$dst_folder_path\"");



// (Setting the cwd)
chdir( $dst_folder_path );



// (Getting the values)
$app_config_file_path = "$dst_folder_path/app.json";
$app_config           = json_decode( file_get_contents( $app_config_file_path ) );
$app_config->id       = $app_id;



if ( file_put_contents( $app_config_file_path, json_encode( $app_config ) ) === false )
{// (Unable to write to the file)
    // (Setting the value)
    $message = "Unable to write to the file '$app_config_file_path'";

    // Throwing an exception
    throw new \Exception($message);

    // Closing the process
    exit;
}



// (Executing the cmds)
system("sudo rm -rf .git");
system("php x build"); 



?>