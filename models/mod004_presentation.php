<?php
require "mod003_logic.php";
require "_render.php";
require "_pdf.php";

/* La presentación entrega datos a la plantilla; no genera fragmentos HTML. */
function mod004_getJudoHome() {
	return render( mod004_readTemplate( "judo.html" ), mod003_getHomeData() );
}

function mod004_getResults() {
	return render( mod004_readTemplate( "resultados.html" ), mod003_getResultsData() );
}

function mod004_getPublicEnrollments() {
	return render( mod004_readTemplate( "inscripciones.html" ), mod003_getPublicEnrollmentsData() );
}

function mod004_getFormDesignPreview() {
	return render( mod004_readTemplate( "formularios_pruebas.html" ), [] );
}

function mod004_getRankingPdf( $bracketId ) {
	$data = mod003_getRankingDownloadData( $bracketId );
	$data[ "filename" ] = preg_replace( '/\.csv$/i', ".pdf", $data[ "filename" ] );
	return [
		"filename" => $data[ "filename" ],
		"content" => mod004_buildRankingPdf( $data ),
	];
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

function mod004_getDashboard( $auth, $csrfToken ) {
	$data = mod003_getDashboardData( $auth );
	$data[ "csrfToken" ] = $csrfToken;
	return render( mod004_readTemplate( "dashboard.html" ), $data );
}

function mod004_getClubJudokas( $auth, $csrfToken ) {
	$data = mod003_getClubJudokasData( $auth );
	$data[ "csrfToken" ] = $csrfToken;
	return render( mod004_readTemplate( "club_judokas.html" ), $data );
}

function mod004_getFederatedProfile( $auth, $csrfToken ) {
	$data = mod003_getFederatedProfileData( $auth );
	$data[ "csrfToken" ] = $csrfToken;
	return render( mod004_readTemplate( "federado_ficha.html" ), $data );
}

function mod004_getJudokaForm( $csrfToken, $values, $errors = [], $success = [], $returnToEnrollment = false ) {
	$data = [
		"csrfToken" => $csrfToken,
		"federationNumber" => $values[ "federationNumber" ] ?? "",
		"firstName" => $values[ "firstName" ] ?? "",
		"lastName" => $values[ "lastName" ] ?? "",
		"weight" => $values[ "weight" ] ?? "",
		"birthDate" => $values[ "birthDate" ] ?? "",
		"maleChecked" => ( $values[ "gender" ] ?? "1" ) === "1" ? "checked" : "",
		"femaleChecked" => ( $values[ "gender" ] ?? "1" ) === "0" ? "checked" : "",
		"returnUrl" => $returnToEnrollment ? "club_inscripcion.php" : "club.php",
		"returnLabel" => $returnToEnrollment ? "Volver a la inscripción" : "Volver al área de clubes",
		"returnValue" => $returnToEnrollment ? "club_inscripcion.php" : "",
		"errors" => mod004_messages( $errors ),
		"success" => mod004_messages( $success ),
	];
	return render( mod004_readTemplate( "club_judoka_form.html" ), $data );
}

function mod004_getEnrollmentForm( $auth, $csrfToken, $options, $errors = [], $success = [] ) {
	$isClub = $auth[ "role" ] === "CLUB";
	$data = [
		"pageTitle" => $isClub ? "Inscribir judoka" : "Solicitar inscripción",
		"eyebrow" => $isClub ? "Gestión del club" : "Perfil deportivo",
		"formDescription" => $isClub ? "Selecciona un judoka del club y una competición compatible." : "Selecciona un campeonato compatible. Enviaremos un correo a tu club para que tramite la inscripción.",
		"backUrl" => $isClub ? "club.php" : "federado.php",
		"fieldLabel" => $isClub ? "Judoka y competición disponible" : "Campeonato que quieres solicitar",
		"submitLabel" => $isClub ? "Enviar inscripción" : "Solicitar a mi club",
		"emptyText" => $isClub ? "No hay inscripciones compatibles disponibles en este momento." : "No hay campeonatos compatibles o no consta un club de contacto.",
		"csrfToken" => $csrfToken,
		"options" => $options,
		"relatedCreate" => $isClub ? [ [
			"createUrl" => "club_judoka_nuevo.php?return=club_inscripcion.php",
			"createLabel" => "¿No aparece el judoka? Darlo de alta",
		] ] : [],
		"errors" => mod004_messages( $errors ),
		"success" => mod004_messages( $success ),
	];
	return render( mod004_readTemplate( "inscripcion_form.html" ), $data );
}

function mod004_getChampionshipForm( $csrfToken, $values, $subcategories, $errors = [], $success = [] ) {
	foreach ( $subcategories as $index => $subcategory ) {
		$subcategories[ $index ][ "selected" ] = (int) ( $values[ "subcategoryId" ] ?? 0 ) === $subcategory[ "subcategoryId" ] ? "selected" : "";
	}
	$data = [
		"csrfToken" => $csrfToken,
		"name" => $values[ "name" ] ?? "",
		"eventDate" => $values[ "eventDate" ] ?? "",
		"venue" => $values[ "venue" ] ?? "",
		"issuer" => $values[ "issuer" ] ?? "Federacion Madrilena de Judo y D.A.",
		"deadline" => $values[ "deadline" ] ?? "",
		"requirements" => $values[ "requirements" ] ?? "",
		"documentsValue" => $values[ "documents" ] ?? "",
		"regulation" => $values[ "regulation" ] ?? "",
		"ageCutoff" => $values[ "ageCutoff" ] ?? "",
		"subcategories" => $subcategories,
		"errors" => mod004_messages( $errors ),
		"success" => mod004_messages( $success ),
	];
	return render( mod004_readTemplate( "organizador_campeonato_form.html" ), $data );
}

function mod004_messages( $messages ) {
	return array_map( fn( $message ) => [ "message" => $message ], $messages );
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
