<?php
require __DIR__ . "/../models/mod004_presentation.php";

mod001_startSession();

if ( !isset( $_SESSION[ "auth" ] ) ) {
	header( "Location: login.php?perfil=federado" );
	exit;
}
if ( $_SESSION[ "auth" ][ "role" ] !== "FEDERADO" ) {
	header( "Location: area.php" );
	exit;
}
if ( empty( $_SESSION[ "csrf" ] ) ) {
	$_SESSION[ "csrf" ] = bin2hex( random_bytes( 32 ) );
}

try {
	echo mod004_getDashboard( $_SESSION[ "auth" ], $_SESSION[ "csrf" ] );
} catch ( Exception $e ) {
	error_log( $e->getMessage() );
	http_response_code( 500 );
	echo mod004_showError( "No se ha podido mostrar el área de federados." );
}
