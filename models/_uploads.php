<?php
/* Utilidad de almacenamiento público. No es una capa del MVC. */

function uploads_storeJudokaPhoto( $temporaryPath, $extension ) {
	$directory = __DIR__ . "/../public/uploads/j";
	if ( !is_dir( $directory ) && !mkdir( $directory, 0775, true ) && !is_dir( $directory ) ) {
		throw new RuntimeException( "No se ha podido preparar la carpeta de fotografías." );
	}

	$fileName = bin2hex( random_bytes( 5 ) ) . "." . $extension;
	$destination = $directory . "/" . $fileName;
	if ( !move_uploaded_file( $temporaryPath, $destination ) ) {
		throw new RuntimeException( "No se ha podido guardar la fotografía seleccionada." );
	}

	return "uploads/j/" . $fileName;
}

function uploads_deleteJudokaPhoto( $relativePath ) {
	if ( !preg_match( '#^uploads/j/[a-f0-9]{10}\.(jpg|png|webp)$#', (string) $relativePath ) ) {
		return;
	}
	$path = __DIR__ . "/../public/" . $relativePath;
	if ( is_file( $path ) ) {
		unlink( $path );
	}
}
