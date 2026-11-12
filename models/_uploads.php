<?php
/* Utilidad de almacenamiento público. No es una capa del MVC. */

function uploads_storeJudokaPhoto( $temporaryPath, $extension ) {
	$directory = __DIR__ . "/../public/imagenes/judokas";
	if ( !is_dir( $directory ) && !mkdir( $directory, 0775, true ) && !is_dir( $directory ) ) {
		throw new RuntimeException( "No se ha podido preparar la carpeta de fotografías." );
	}

	$fileName = bin2hex( random_bytes( 5 ) ) . "." . $extension;
	$destination = $directory . "/" . $fileName;
	if ( !move_uploaded_file( $temporaryPath, $destination ) ) {
		throw new RuntimeException( "No se ha podido guardar la fotografía seleccionada." );
	}

	return "imagenes/judokas/" . $fileName;
}

function uploads_deleteJudokaPhoto( $relativePath ) {
	if ( !preg_match( '#^imagenes/judokas/[a-f0-9]{10}\.(jpg|png|webp)$#', (string) $relativePath ) ) {
		return;
	}
	$path = __DIR__ . "/../public/" . $relativePath;
	if ( is_file( $path ) ) {
		unlink( $path );
	}
}

function uploads_storeCallDocument( $temporaryPath, $documentType ) {
	if ( !in_array( $documentType, [ "circular", "normativa" ], true ) ) {
		throw new InvalidArgumentException( "El tipo de documento oficial no es válido." );
	}

	$directory = __DIR__ . "/../public/documentos/convocatorias";
	if ( !is_dir( $directory ) && !mkdir( $directory, 0775, true ) && !is_dir( $directory ) ) {
		throw new RuntimeException( "No se ha podido preparar la carpeta de documentación oficial." );
	}

	$fileName = $documentType . "-" . bin2hex( random_bytes( 5 ) ) . ".pdf";
	$destination = $directory . "/" . $fileName;
	if ( !move_uploaded_file( $temporaryPath, $destination ) ) {
		throw new RuntimeException( "No se ha podido guardar el documento oficial seleccionado." );
	}

	return "documentos/convocatorias/" . $fileName;
}

function uploads_deleteCallDocument( $relativePath ) {
	if ( !preg_match( '#^documentos/convocatorias/(circular|normativa)-[a-f0-9]{10}\.pdf$#', (string) $relativePath ) ) {
		return;
	}
	$path = __DIR__ . "/../public/" . $relativePath;
	if ( is_file( $path ) ) {
		unlink( $path );
	}
}
