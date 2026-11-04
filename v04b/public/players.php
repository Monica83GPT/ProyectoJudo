<?php
	// v04b: el controlador vive en public/; todo lo demás está fuera, un nivel más arriba
	require __DIR__ . "/../models/mod004_presentation.php";

	// v03: el filtro llega por la URL (players.php?positionId=2).
	// El controlador no lo valida: lo pasa tal cual y decide la lógica (mod003).
	$idPosition = $_GET[ "positionId" ] ?? null;

	// v04: el controlador imprime la página que devuelve la presentación.
	try {
		echo mod004_getPlayers( $idPosition );
	} catch ( RuntimeException $e ) {
		// ha fallado una consulta: su mensaje para el usuario viene de config/queries.php
		// (el detalle técnico ya lo ha apuntado mod002 en el log)
		http_response_code( 500 );
		echo mod004_showError( $e->getMessage() );
	} catch ( Exception $e ) {
		// cualquier otro fallo, que no es de una consulta (una plantilla que no está, una clave
		// de consulta mal escrita…): al log, y un mensaje genérico
		error_log( $e->getMessage() );
		http_response_code( 500 );
		echo mod004_showError( "No se ha podido mostrar la página." );
	}
