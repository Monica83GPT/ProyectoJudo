<?php
require __DIR__ . "/../models/mod004_presentation.php";
require __DIR__ . "/../models/_access.php";

$auth = access_requireRole( "FEDERADO", "federado" );

try {
	echo mod004_getFederatedProfile( $auth, $_SESSION[ "csrf" ] );
} catch ( Exception $e ) {
	error_log( $e->getMessage() );
	http_response_code( 500 );
	echo mod004_showError( "No se ha podido mostrar tu ficha federativa." );
}
