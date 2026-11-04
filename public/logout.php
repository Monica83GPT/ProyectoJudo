<?php
require __DIR__ . "/../models/mod001_kernel.php";

mod001_startSession();

if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
	$token = (string) ( $_POST[ "csrf" ] ?? "" );
	if ( isset( $_SESSION[ "csrf" ] ) && hash_equals( $_SESSION[ "csrf" ], $token ) ) {
		$_SESSION = [];
		if ( ini_get( "session.use_cookies" ) ) {
			$params = session_get_cookie_params();
			setcookie( session_name(), "", time() - 42000, $params[ "path" ], $params[ "domain" ], $params[ "secure" ], $params[ "httponly" ] );
		}
		session_destroy();
	}
}

header( "Location: login.php" );
exit;
