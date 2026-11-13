<?php
require __DIR__ . "/../models/mod004_presentation.php";
require __DIR__ . "/../models/_access.php";

$auth = access_requireRole( "CLUB", "club" );
$number = (string) ( $_GET[ "numfederado" ] ?? $_POST[ "numfederado" ] ?? "" );
$values = mod003_getClubJudokaForEdit( $auth, $number );
$errors = [];
$success = [];
if ( $values === null ) {
	echo mod004_showError( "Este judoka no pertenece actualmente a tu club." );
	exit;
}
if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
	if ( !access_validCsrf() ) $errors[] = "La sesión del formulario ha caducado. Inténtalo de nuevo.";
	else {
		try {
			$result = mod003_updateClubJudoka( $auth, $number, $_POST, $_FILES[ "foto" ] ?? null );
			$values = $result[ "values" ] ?: $values;
			$errors = $result[ "errors" ];
			if ( $result[ "ok" ] ) $success[] = "La ficha del judoka se ha actualizado correctamente.";
		} catch ( RuntimeException $e ) { $errors[] = $e->getMessage(); }
	}
}
echo mod004_getJudokaEditForm( $_SESSION[ "csrf" ], $values, $errors, $success );
