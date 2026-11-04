<?php
require "mod003_logic.php";
require "_render.php";

/* La presentación entrega datos a la plantilla; no genera fragmentos HTML. */
function mod004_getJudoHome() {
	return render( mod004_readTemplate( "judo.html" ), mod003_getHomeData() );
}

function mod004_getLogin( $profile, $csrfToken, $errors = [], $username = "" ) {
	$profile = mod003_normalizeProfile( $profile );
	$data = [
		"csrfToken"       => $csrfToken,
		"username"        => $username,
		"errors"          => array_map( fn( $message ) => [ "message" => $message ], $errors ),
		"clubChecked"     => $profile === "club" ? "checked" : "",
		"federadoChecked" => $profile === "federado" ? "checked" : "",
		"organizerChecked"=> $profile === "organizador" ? "checked" : "",
	];
	return render( mod004_readTemplate( "login.html" ), $data );
}

function mod004_getArea( $auth, $csrfToken ) {
	$data = mod003_getAreaData( $auth );
	$data[ "csrfToken" ] = $csrfToken;
	return render( mod004_readTemplate( "area.html" ), $data );
}

function mod004_showError( $message ) {
	return render( mod004_readTemplate( "error.html" ), [ "message" => $message ] );
}

function mod004_readTemplate( $file ) {
	$path = __DIR__ . "/../views/" . $file;
	if ( !is_file( $path ) ) {
		throw new LogicException( "No existe la plantilla views/$file" );
	}
	return file_get_contents( $path );
}
