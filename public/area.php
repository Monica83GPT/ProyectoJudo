<?php
require __DIR__ . "/../models/mod004_presentation.php";

mod001_startSession();

if ( !isset( $_SESSION[ "auth" ] ) ) {
	header( "Location: login.php" );
	exit;
}

$routes = [
	"CLUB"        => "club.php",
	"FEDERADO"    => "federado.php",
	"ORGANIZADOR" => "organizador.php",
];
$role = $_SESSION[ "auth" ][ "role" ] ?? "";

if ( !isset( $routes[ $role ] ) ) {
	unset( $_SESSION[ "auth" ] );
	header( "Location: login.php" );
	exit;
}

header( "Location: " . $routes[ $role ] );
exit;
