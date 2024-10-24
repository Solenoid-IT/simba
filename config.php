<?php



// (Getting the value)
$root = __DIR__;



// (Getting the value)
$app_config = json_decode( file_get_contents( "$root/app.json" ), true );



# Returns [string]
function explicit_path (string $root, string $path)
{
    // Returning the value
    return preg_replace( '/^\./', $root, $path );
}



// (Getting the value)
$app_config['basedir'] = $root;



?>