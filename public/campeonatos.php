<?php
require __DIR__ . "/../models/mod004_presentation.php";

try {
	echo mod004_getChampionships();
} catch ( RuntimeException $e ) {
	http_response_code( 500 );
	echo mod004_showError( $e->getMessage() );
} catch ( Exception $e ) {
	error_log( $e->getMessage() );
	http_response_code( 500 );
	echo mod004_showError( "No se han podido mostrar los campeonatos." );
}
