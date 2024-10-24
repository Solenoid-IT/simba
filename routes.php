<?php



use \Solenoid\Core\Routing\Route;
use \Solenoid\Core\Routing\Target;

use \App\Controllers\API;
use \App\Controllers\Authorization;
use \App\Controllers\SPA;
use \App\Controllers\DynamicFile;
use \App\Controllers\Fallback;



# debug
Route::handle( 'GET /test/[ x ]/[ y ]/[ z ]', Target::define( function ($app) { return $app->target->args; } ) );
Route::handle( 'GET /^\/tests\/(.+)/', Target::define( function ($app) { return $app->target->args; } ) );
Route::handle( 'GET /test/[ action ]/[ input ]', Target::link( Test::class, 'get' )->set_middlewares( [ 'User' ] ) );
Route::handle( 'GET /test/error', Target::define( function () { throw new \Exception('exception test'); } ) );
Route::handle( 'GET /test/perf', Target::define( function () {} ) );



// (Handing the routes)
Route::handle( 'RPC /api', Target::link( API::class, 'rpc' )->set_middlewares( [ 'RPC/Parser' ] ) );
Route::handle( 'GET /admin', Target::define( function () { header( 'Location: /admin/dashboard', true, 303 ); } ) );
Route::handle( 'GET /admin/authorization/[ token ]/[ action ]', Target::link( Authorization::class, 'get' ) );
Route::handle( 'GET /history.json', Target::define( function ($app) { return $app->fetch_history(); } ) );



// (Setting the value)
$dynamic_files =
[
    '/robots.txt',
    '/sitemap.xml'
]
;

foreach ( $dynamic_files as $id )
{// Processing each entry
    // (Handling the route)
    Route::handle( "GET $id", Target::link( DynamicFile::class, 'get' ) );
}



// (Setting the value)
$spa_routes =
[
    '/',
    '/admin/login',
    '/admin/dashboard',
    '/admin/activity_log',
    '/admin/users',
    '/admin/access_log'
]
;

foreach ( $spa_routes as $id )
{// Processing each entry
    // (Handling the route)
    Route::handle( "GET $id", Target::link( SPA::class, 'get' ) );
}



// (Handling the fallback)
Route::handle_fallback( Target::link( Fallback::class, 'view' ) );



?>