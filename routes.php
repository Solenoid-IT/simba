<?php



use \Solenoid\Core\Routing\Route;
use \Solenoid\Core\Routing\Target;

use \App\Controllers\Test;
use \App\Controllers\ApiGateway;
use \App\Controllers\Authorization;
use \App\Controllers\SPA;
use \App\Controllers\DynamicFile;
use \App\Controllers\Fallback;

use \App\Middlewares\User as UserMiddleware;



# debug
Route::bind( 'GET /test/{ x }/{ y }/{ z }', function (int $x, int $y, int $z) { return "$x-$y-$z"; } );
Route::bind( new Route( 'GET', '/^\/tests\/(.+)/' ), function (string $match, string $value) { return $value; } );
Route::bind( 'GET /test/{ action }/{ input }', [ Test::class, 'get' ] )->via( [ UserMiddleware::class ] );
Route::bind( 'GET /test/error', function () { throw new \Exception('exception test'); } );
Route::bind( 'GET /test/perf', function () {} );
Route::bind( 'GET /test', [ Test::class, 'run' ] );



// (Binding the routes)
Route::bind( 'RPC /api', [ ApiGateway::class, 'process_action' ] );
Route::bind( 'GET /admin', function () { header( 'Location: /admin/dashboard', true, 303 ); } );
Route::bind( 'GET /admin/authorization/[ token ]/[ action ]', [ Authorization::class, 'get' ] );
Route::bind( 'GET /history.json',  function ($app) { return $app->fetch_history(); } );



// (Setting the value)
$dynamic_files =
[
    '/robots.txt',
    '/sitemap.xml'
]
;

foreach ( $dynamic_files as $id )
{// Processing each entry
    // (Binding the route)
    Route::bind( "GET $id", [ DynamicFile::class, 'get' ] );
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
    // (Binding the route)
    Route::bind( "GET $id", [ SPA::class, 'get' ] );
}



// (Binding the route)
Route::bind( 'RPC /fluid', function () { return 'fluid'; } );



// (Defining the fallback)
Route::fallback( [ Fallback::class, 'view' ] );



?>