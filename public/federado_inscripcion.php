<?php
require __DIR__ . "/../models/mod004_presentation.php";
require __DIR__ . "/../models/_access.php";

$auth = access_requireRole( "FEDERADO", "federado" );
$errors = [];
$success = [];

if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
	if ( !access_validCsrf() ) {
		$errors[] = "La sesión del formulario ha caducado. Inténtalo de nuevo.";
	} else {
		try {
			$result = mod003_requestClubEnrollment( $auth, $_POST[ "inscripcion" ] ?? "" );
			$errors = $result[ "errors" ];
			if ( $result[ "ok" ] ) $success[] = "La solicitud se ha enviado por correo a " . $result[ "clubName" ] . ". El club deberá formalizar tu inscripción.";
		} catch ( RuntimeException $e ) {
			$errors[] = $e->getMessage();
		}
	}
}

$options = [];
try {
	$options = mod003_getEnrollmentOptions( $auth );
} catch ( RuntimeException $e ) {
	if ( !in_array( $e->getMessage(), $errors, true ) ) {
		$errors[] = $e->getMessage();
	}
}

echo mod004_getEnrollmentForm( $auth, $_SESSION[ "csrf" ], $options, $errors, $success );
