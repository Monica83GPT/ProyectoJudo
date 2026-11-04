<?php
require __DIR__ . "/../models/mod004_presentation.php";

mod001_startSession();

if ( isset( $_SESSION[ "auth" ] ) ) {
	header( "Location: area.php" );
	exit;
}

if ( empty( $_SESSION[ "csrf" ] ) ) {
	$_SESSION[ "csrf" ] = bin2hex( random_bytes( 32 ) );
}

$profile  = $_POST[ "perfil" ] ?? $_GET[ "perfil" ] ?? "club";
$username = trim( (string) ( $_POST[ "usuario" ] ?? "" ) );
$errors   = [];

try {
	if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
		$token = (string) ( $_POST[ "csrf" ] ?? "" );
		if ( !hash_equals( $_SESSION[ "csrf" ], $token ) ) {
			$errors[] = "La sesión del formulario ha caducado. Inténtalo de nuevo.";
		} else {
			$auth = mod003_authenticate(
				$username,
				$_POST[ "password" ] ?? "",
				$profile
			);

			if ( $auth === null ) {
				$errors[] = "El usuario, la contraseña o el tipo de acceso no son correctos.";
			} else {
				session_regenerate_id( true );
				$_SESSION[ "auth" ] = $auth;
				$_SESSION[ "csrf" ] = bin2hex( random_bytes( 32 ) );
				header( "Location: area.php" );
				exit;
			}
		}
	}

	echo mod004_getLogin( $profile, $_SESSION[ "csrf" ], $errors, $username );
} catch ( RuntimeException $e ) {
	http_response_code( 500 );
	echo mod004_showError( $e->getMessage() );
} catch ( Exception $e ) {
	error_log( $e->getMessage() );
	http_response_code( 500 );
	echo mod004_showError( "No se ha podido mostrar el acceso." );
}
