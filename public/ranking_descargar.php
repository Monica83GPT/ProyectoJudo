<?php
require __DIR__ . "/../models/mod004_presentation.php";

$bracketId = filter_input( INPUT_GET, "cuadro", FILTER_VALIDATE_INT, [ "options" => [ "min_range" => 1 ] ] );

try {
	$download = mod004_getRankingPdf( $bracketId );
	header( "Content-Type: application/pdf" );
	header( 'Content-Disposition: attachment; filename="' . $download[ "filename" ] . '"' );
	header( "Content-Length: " . strlen( $download[ "content" ] ) );
	header( "X-Content-Type-Options: nosniff" );
	echo $download[ "content" ];
} catch ( DomainException $e ) {
	http_response_code( 404 );
	echo mod004_showError( $e->getMessage() );
} catch ( RuntimeException $e ) {
	http_response_code( 500 );
	echo mod004_showError( $e->getMessage() );
} catch ( Exception $e ) {
	error_log( $e->getMessage() );
	http_response_code( 500 );
	echo mod004_showError( "No se ha podido descargar el ranking." );
}
