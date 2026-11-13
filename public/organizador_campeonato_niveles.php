<?php
require __DIR__ . "/../models/mod004_presentation.php";
require __DIR__ . "/../models/_access.php";

access_requireRole( "ORGANIZADOR", "organizador" );
$championshipId = filter_input( INPUT_GET, "campeonato", FILTER_VALIDATE_INT ) ?: filter_input( INPUT_POST, "campeonato", FILTER_VALIDATE_INT );
$championship = mod003_getChampionshipSetup( $championshipId );
$errors = [];
$success = [];
if ( $championship === null ) {
	echo mod004_showError( "No se ha encontrado el campeonato." );
	exit;
}
if ( $_SERVER[ "REQUEST_METHOD" ] === "POST" ) {
	if ( !access_validCsrf() ) $errors[] = "La sesión del formulario ha caducado. Inténtalo de nuevo.";
	else {
		$result = mod003_saveChampionshipLevels( $championshipId, $_POST );
		$errors = $result[ "errors" ];
		if ( $result[ "ok" ] ) $success[] = "Los niveles de competición se han guardado correctamente.";
	}
}
echo mod004_getChampionshipLevelsForm( $_SESSION[ "csrf" ], $championship, mod003_getCompetitionCategories(), mod003_getCompetitionSubcategories( $championshipId ), $errors, $success );
