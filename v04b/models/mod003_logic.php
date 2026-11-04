<?php
require "mod002_dataaccess.php";

/* v03 · La lógica empieza a trabajar: lanza DOS consultas, valida el filtro,
   agrupa los jugadores por posición y convierte su altura a pies y pulgadas.
   Devuelve todo lo que necesita la página. */
function mod003_getPlayers( $idPosition ) {
	// 1 · Primera consulta: las posiciones que existen.
	$arPositions = mod002_runQuery( "getPositions" );

	// 2 · El filtro solo vale si es una de esas posiciones: la lista blanca sale de la BD.
	//     filter_var deja pasar "2" pero no "2abc" ni "abc" (con (int) "2abc" daría 2).
	$idPosition = filter_var( $idPosition, FILTER_VALIDATE_INT );
	if ( !in_array( $idPosition, array_column( $arPositions, "positionId" ), true ) ) {
		$idPosition = null;	// no existe: se enseñan todas
	}

	// 3 · Segunda consulta: los jugadores, con filtro o sin él.
	if ( $idPosition === null ) {
		$arPlayers = mod002_runQuery( "getPlayers" );
	} else {
		$arPlayers = mod002_runQuery( "getPlayersByPosition", [ ":positionId" => $idPosition ] );
	}

	// 4 · Agrupar: de una lista plana de jugadores a posición → sus jugadores.
	//     Primero un hueco por posición (así salen también las que no tienen jugadores)...
	$arGroups = [];
	foreach ( $arPositions as $position ) {
		if ( $idPosition === null || $position[ "positionId" ] === $idPosition ) {
			$arGroups[ $position[ "positionId" ] ] = [
				"positionName" => $position[ "positionName" ],
				"players"      => [],
			];
		}
	}
	//     ...y después cada jugador a su hueco, con su altura también en pies (5 · conversión).
	foreach ( $arPlayers as $player ) {
		$player[ "heightFeet" ] = mod003_toFeet( $player[ "height" ] );
		$arGroups[ $player[ "positionId" ] ][ "players" ][] = $player;
	}

	return [
		"positions" => $arPositions,	// para el <select>
		"selected"  => $idPosition,	// la elegida, o null si son todas
		"groups"    => $arGroups,	// posición → jugadores
	];
}

/* v03 · 5 · Conversión: la altura llega en centímetros y en la NBA se da en pies y pulgadas.
   1 pulgada = 2,54 cm y 1 pie = 12 pulgadas: 211 cm → 83 pulgadas → 6' 11". */
function mod003_toFeet( $cm ) {
	if ( $cm === null ) {
		return "";
	}
	$inches = (int) round( $cm / 2.54 );
	return intdiv( $inches, 12 ) . "' " . ( $inches % 12 ) . "\"";
}
