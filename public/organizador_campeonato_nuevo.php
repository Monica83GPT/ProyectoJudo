<?php
require __DIR__ . "/../models/mod004_presentation.php";
require __DIR__ . "/../models/_access.php";

$auth = access_requireRole( "ORGANIZADOR", "organizador" );
$values = [];
$errors = [];
$success = [];

if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
	if ( !access_validCsrf() ) {
		$errors[] = "La sesión del formulario ha caducado. Inténtalo de nuevo.";
	} else {
		try {
			$result = mod003_createChampionshipCall( $_POST, $_FILES );
			$values = $result[ "values" ];
			$errors = $result[ "errors" ];
			if ( $result[ "ok" ] ) {
				header( "Location: organizador_campeonato_niveles.php?campeonato=" . (int) $result[ "championshipId" ] );
				exit;
			}
		} catch ( RuntimeException $e ) {
			$errors[] = $e->getMessage();
		}
	}
}

echo mod004_getChampionshipForm( $_SESSION[ "csrf" ], $values, [], $errors, $success );
