<?php
require "mod002_dataaccess.php";

/* Reúne los datos de la portada y prepara valores que no pertenecen a la vista. */
function mod003_getHomeData() {
	$championships = mod002_runQuery( "getChampionships" );
	$statsRows     = mod002_runQuery( "getFederationStats" );
	$today         = new DateTimeImmutable( "today" );
	$events        = [];
	$openCount     = 0;

	foreach ( $championships as $championship ) {
		$date = $championship[ "championshipDate" ];
		if ( !( $date instanceof DateTimeImmutable ) ) {
			continue;
		}

		$isUpcoming = $date >= $today;
		if ( $isUpcoming ) {
			$openCount++;
		}

		$events[] = [
			"eventDay"         => $date->format( "d" ),
			"eventMonth"       => mod003_monthAbbreviation( $date ),
			"eventName"        => $championship[ "championshipName" ],
			"eventDescription" => mod003_eventDescription( $championship, $isUpcoming ),
		];
	}

	$stats = $statsRows[ 0 ] ?? [ "judokaCount" => 0, "clubCount" => 0 ];

	return [
		"events"                => $events,
		"openChampionshipCount" => $openCount,
		"judokaCount"           => $stats[ "judokaCount" ],
		"clubCount"             => $stats[ "clubCount" ],
	];
}

function mod003_eventDescription( $championship, $isUpcoming ) {
	if ( !$isUpcoming ) {
		return "Finalizado · Resultados disponibles";
	}

	$description = $championship[ "venue" ];
	if ( $championship[ "registrationDate" ] instanceof DateTimeImmutable ) {
		$description .= " · Plazo: " . mod003_formatSpanishDate( $championship[ "registrationDate" ] );
	}
	return $description;
}

function mod003_monthAbbreviation( DateTimeImmutable $date ) {
	$months = [ 1 => "ENE", "FEB", "MAR", "ABR", "MAY", "JUN", "JUL", "AGO", "SEP", "OCT", "NOV", "DIC" ];
	return $months[ (int) $date->format( "n" ) ];
}

function mod003_formatSpanishDate( DateTimeImmutable $date ) {
	$months = [ 1 => "enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre" ];
	return (int) $date->format( "j" ) . " de " . $months[ (int) $date->format( "n" ) ];
}

/* Comprueba las credenciales y que el perfil elegido coincide con el rol real. */
function mod003_authenticate( $username, $password, $profile ) {
	$username = trim( (string) $username );
	$password = (string) $password;
	$profile  = mod003_normalizeProfile( $profile );

	if ( $username === "" || $password === "" || strlen( $username ) > 50 ) {
		return null;
	}

	$rows = mod002_runQuery( "getAccessUser", [ ":username" => $username ] );
	$user = $rows[ 0 ] ?? null;
	$hash = $user[ "passwordHash" ] ?? password_hash( "", PASSWORD_DEFAULT );
	$passwordIsValid = password_verify( $password, $hash );
	$expectedRole = mod003_profileRole( $profile );

	if ( $user === null || !$user[ "active" ] || !$passwordIsValid || $user[ "role" ] !== $expectedRole ) {
		return null;
	}

	mod002_runQuery( "touchUserAccess", [ ":userId" => $user[ "userId" ] ] );

	return [
		"userId"      => $user[ "userId" ],
		"username"    => $user[ "username" ],
		"role"        => $user[ "role" ],
		"displayName" => $user[ "displayName" ],
	];
}

function mod003_normalizeProfile( $profile ) {
	$profile = strtolower( trim( (string) $profile ) );
	return in_array( $profile, [ "club", "federado", "organizador" ], true ) ? $profile : "club";
}

function mod003_profileRole( $profile ) {
	$roles = [
		"club"        => "CLUB",
		"federado"    => "FEDERADO",
		"organizador" => "ORGANIZADOR",
	];
	return $roles[ mod003_normalizeProfile( $profile ) ];
}

function mod003_getAreaData( $auth ) {
	$areas = [
		"CLUB" => [
			"areaTitle"       => "Área de clubes",
			"areaEyebrow"     => "Gestión de club",
			"areaDescription" => "Gestiona judokas, inscripciones y participación en campeonatos desde un único espacio.",
		],
		"FEDERADO" => [
			"areaTitle"       => "Área de federados",
			"areaEyebrow"     => "Perfil deportivo",
			"areaDescription" => "Consulta tus datos, próximas competiciones, inscripciones y resultados deportivos.",
		],
		"ORGANIZADOR" => [
			"areaTitle"       => "Área de organizadores",
			"areaEyebrow"     => "Control de competición",
			"areaDescription" => "Prepara convocatorias, cuadros y combates con acceso a la información del evento.",
		],
	];

	if ( !is_array( $auth ) || !isset( $auth[ "role" ], $areas[ $auth[ "role" ] ] ) ) {
		throw new DomainException( "La sesión no contiene un perfil válido." );
	}

	return array_merge( $areas[ $auth[ "role" ] ], [
		"displayName" => $auth[ "displayName" ],
		"username"    => $auth[ "username" ],
	] );
}
