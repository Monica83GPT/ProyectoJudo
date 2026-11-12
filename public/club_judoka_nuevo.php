<?php
require __DIR__ . "/../models/mod004_presentation.php";
require __DIR__ . "/../models/_access.php";

$auth = access_requireRole( "CLUB", "club" );
$values = [ "gender" => "1" ];
$errors = [];
$success = [];
$returnToEnrollment = ( $_GET[ "return" ] ?? $_POST[ "return" ] ?? "" ) === "club_inscripcion.php";

if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
	if ( !access_validCsrf() ) {
		$errors[] = "La sesión del formulario ha caducado. Inténtalo de nuevo.";
	} else {
		try {
			$result = mod003_createClubJudoka( $auth, $_POST, $_FILES[ "foto" ] ?? null );
			$values = $result[ "values" ];
			$errors = $result[ "errors" ];
			if ( $result[ "ok" ] ) $success[] = "El judoka se ha creado y vinculado correctamente al club.";
		} catch ( RuntimeException $e ) {
			$errors[] = $e->getMessage();
		}
	}
}

echo mod004_getJudokaForm( $_SESSION[ "csrf" ], $values, $errors, $success, $returnToEnrollment );
