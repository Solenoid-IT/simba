<?php



use \Solenoid\Core\Credentials;

use \Solenoid\SMTP\Connection;
use \Solenoid\SMTP\ConnectionStore;



foreach ( Credentials::fetch( '/smtp/data.json' ) as $profile => $credentials )
{// Processing each entry
    // (Setting the value)
    ConnectionStore::set( $profile, new Connection( $credentials['host'], $credentials['port'], $credentials['username'], $credentials['password'], $credentials['encryption_type'] ) );
}



?>