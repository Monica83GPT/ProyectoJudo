<?php
require __DIR__ . "/../models/mod004_presentation.php";
require __DIR__ . "/../models/_access.php";

$auth = access_requireRole( "CLUB", "club" );
$errors = [];
$success = [];

if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
	if ( !access_validCsrf() ) {
		$errors[] = "La sesión del formulario ha caducado. Inténtalo de nuevo.";
	} else {
		try {
			$result = mod003_createEnrollment( $auth, $_POST[ "inscripcion" ] ?? "" );
			$errors = $result[ "errors" ];
			if ( $result[ "ok" ] ) $success[] = "La inscripción se ha enviado correctamente.";
		} catch ( RuntimeException $e ) {
			$errors[] = $e->getMessage();
		}
	}
}

echo mod004_getEnrollmentForm( $auth, $_SESSION[ "csrf" ], mod003_getEnrollmentOptions( $auth ), $errors, $success );
