<?php
require __DIR__ . "/../models/mod004_presentation.php";
require __DIR__ . "/../models/_access.php";

access_requireRole( "ORGANIZADOR", "organizador" );
$selectedValue = "";
$errors = [];
$success = [];

if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
	$selectedValue = (string) ( $_POST[ "convocatoria" ] ?? "" );
	if ( !access_validCsrf() ) {
		$errors[] = "La sesión del formulario ha caducado. Inténtalo de nuevo.";
	} else {
		try {
			$result = mod003_updateCallDocuments( $selectedValue, $_FILES );
			$errors = $result[ "errors" ];
			if ( $result[ "ok" ] ) $success[] = "La documentación oficial se ha publicado correctamente.";
		} catch ( RuntimeException $e ) {
			$errors[] = $e->getMessage();
		}
	}
}

echo mod004_getOrganizerDocumentsForm(
	$_SESSION[ "csrf" ],
	mod003_getOrganizerCallOptions(),
	$selectedValue,
	$errors,
	$success
);
