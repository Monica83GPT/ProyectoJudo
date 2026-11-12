<?php
require_once __DIR__ . "/mod001_kernel.php";

function access_requireRole( $role, $profile ) {
	mod001_startSession();
	if ( !isset( $_SESSION[ "auth" ] ) ) {
		header( "Location: login.php?perfil=$profile" );
		exit;
	}
	if ( ( $_SESSION[ "auth" ][ "role" ] ?? "" ) !== $role ) {
		header( "Location: area.php" );
		exit;
	}
	if ( empty( $_SESSION[ "csrf" ] ) ) {
		$_SESSION[ "csrf" ] = bin2hex( random_bytes( 32 ) );
	}
	return $_SESSION[ "auth" ];
}

function access_validCsrf() {
	$token = (string) ( $_POST[ "csrf" ] ?? "" );
	return isset( $_SESSION[ "csrf" ] ) && hash_equals( $_SESSION[ "csrf" ], $token );
}
