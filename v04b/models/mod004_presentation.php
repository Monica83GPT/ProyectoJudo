<?php
require "mod003_logic.php";
require "_render.php";

/* v04 · La presentación ya no fabrica HTML: prepara los datos para la plantilla
   y se los da a render(). El HTML lo escribe el maquetador en views/players.html. */
function mod004_getPlayers( $idPosition ) {
	$dataPlayers = mod003_getPlayers( $idPosition );

	$dataPlayers[ "positions" ] = mod004_markSelected( $dataPlayers[ "positions" ], $dataPlayers[ "selected" ] );

	return render( mod004_readTemplate( "players.html" ), $dataPlayers );
	// la otra versión, con expresiones regulares, da exactamente lo mismo:
	// return render_regex( mod004_readTemplate( "players.html" ), $dataPlayers );
}

/* v04 · Lo único que es solo de presentación: qué opción del <select> va marcada.
   A cada posición le añade "selected" ("selected" o vacío) para la marca #selected#. */
function mod004_markSelected( $positions, $selected ) {
	foreach ( $positions as $i => $position ) {
		$positions[ $i ][ "selected" ] = ( $position[ "positionId" ] === $selected ) ? "selected" : "";
	}
	return $positions;
}

/* v04 · La página de error, también con su plantilla: views/error.html. */
function mod004_showError( $message ) {
	return render( mod004_readTemplate( "error.html" ), [ "message" => $message ] );
}

/* v04 · Lee una plantilla de la carpeta views. Si no existe, lanza una excepción
   (file_get_contents solo daría un aviso y la página saldría en blanco). */
function mod004_readTemplate( $file ) {
	$path = __DIR__ . "/../views/" . $file;
	if ( !is_file( $path ) ) {
		throw new LogicException( "No existe la plantilla views/$file" );
	}
	return file_get_contents( $path );
}
