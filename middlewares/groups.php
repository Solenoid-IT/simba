<?php



use \Solenoid\Core\Middleware;

use \App\Middlewares\RPC;
use \App\Middlewares\User as UserMiddleware;
use \App\Middlewares\Editor as EditorMiddleware;



// (Adding the middlewares)
Middleware::add( 'RPC/Authenticator', RPC\Authenticator::class );
Middleware::add( 'RPC/Parser', RPC\Parser::class );
Middleware::add( 'User', UserMiddleware::class );
Middleware::add( 'Editor', EditorMiddleware::class );



?>