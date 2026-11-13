<?php
require "mod002_dataaccess.php";
require "_uploads.php";
require "_mail.php";

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
		$deadline = $championship[ "registrationDate" ];
		if ( $isUpcoming && $deadline instanceof DateTimeImmutable && $deadline >= $today ) {
			$openCount++;
		}

		$events[] = [
			"eventDay"         => $date->format( "d" ),
			"eventMonth"       => mod003_monthAbbreviation( $date ),
			"eventName"        => $championship[ "championshipName" ],
			"eventDescription" => mod003_eventDescription( $championship, $isUpcoming ),
			"eventUrl"         => $isUpcoming
				? "inscripciones.php?campeonato=" . $championship[ "championshipId" ] . "#campeonato-" . $championship[ "championshipId" ]
				: "resultados.php?campeonato=" . $championship[ "championshipId" ],
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

/* Prepara la agenda completa, con más información que el resumen de la portada. */
function mod003_getCalendarData() {
	$today = new DateTimeImmutable( "today" );
	$events = [];

	foreach ( mod002_runQuery( "getChampionships" ) as $championship ) {
		$date = $championship[ "championshipDate" ];
		if ( !( $date instanceof DateTimeImmutable ) ) {
			continue;
		}

		$isUpcoming = $date >= $today;
		$deadline = $championship[ "registrationDate" ];
		$events[] = [
			"sortDate" => $date->format( "Y-m-d" ),
			"sortGroup" => $isUpcoming ? 0 : 1,
			"championshipName" => $championship[ "championshipName" ],
			"championshipDate" => mod003_formatSpanishDate( $date ),
			"eventDay" => $date->format( "d" ),
			"eventMonth" => mod003_monthAbbreviation( $date ),
			"venue" => $championship[ "venue" ] ?: "Sede no disponible",
			"eventStatus" => $isUpcoming ? "Próximo campeonato" : "Campeonato finalizado",
			"registrationDate" => $deadline instanceof DateTimeImmutable ? mod003_formatSpanishDate( $deadline ) : "No disponible",
			"eventAction" => $isUpcoming ? "Inscripción" : "Resultados",
			"eventUrl" => $isUpcoming
				? "inscripciones.php?campeonato=" . $championship[ "championshipId" ] . "#campeonato-" . $championship[ "championshipId" ]
				: "resultados.php?campeonato=" . $championship[ "championshipId" ],
			"statusClass" => $isUpcoming ? "calendar-event--upcoming" : "calendar-event--past",
		];
	}

	usort( $events, function ( $first, $second ) {
		if ( $first[ "sortGroup" ] !== $second[ "sortGroup" ] ) {
			return $first[ "sortGroup" ] <=> $second[ "sortGroup" ];
		}
		$dateOrder = strcmp( $first[ "sortDate" ], $second[ "sortDate" ] );
		return $first[ "sortGroup" ] === 0 ? $dateOrder : -$dateOrder;
	} );

	return [
		"pageTitle" => "Calendario de competiciones",
		"events" => $events,
		"eventCount" => count( $events ),
	];
}

/* Prepara el catálogo completo de campeonatos para la página de consulta. */
function mod003_getChampionshipCatalogData() {
	$today = new DateTimeImmutable( "today" );
	$championships = [];

	foreach ( mod002_runQuery( "getChampionshipCatalog" ) as $championship ) {
		$date = $championship[ "championshipDate" ];
		if ( !( $date instanceof DateTimeImmutable ) ) continue;
		$isUpcoming = $date >= $today;
		$championships[] = [
			"championshipId" => $championship[ "championshipId" ],
			"championshipName" => $championship[ "championshipName" ],
			"championshipDate" => mod003_formatSpanishDate( $date ),
			"eventDay" => $date->format( "d" ),
			"eventMonth" => mod003_monthAbbreviation( $date ),
			"year" => $date->format( "Y" ),
			"venue" => $championship[ "venue" ] ?: "Sede no disponible",
			"issuer" => $championship[ "issuer" ] ?: "Organizador no indicado",
			"registrationDate" => $championship[ "registrationDate" ] instanceof DateTimeImmutable ? mod003_formatSpanishDate( $championship[ "registrationDate" ] ) : "No disponible",
			"requirements" => $championship[ "requirements" ] ?: "Consulta los requisitos de la convocatoria.",
			"documents" => $championship[ "documents" ] ?: "Documentación pendiente de publicación.",
			"regulation" => $championship[ "regulation" ] ?: "Normativa pendiente de publicación.",
			"categoriesText" => $championship[ "categoriesText" ] ?: "Categorías pendientes de publicación.",
			"registeredCount" => $championship[ "registeredCount" ],
			"eventStatus" => $isUpcoming ? "Próximo campeonato" : "Campeonato finalizado",
			"eventAction" => $isUpcoming ? "Inscripción" : "Resultados",
			"status" => $isUpcoming ? "upcoming" : "past",
			"statusClass" => $isUpcoming ? "championship-card--upcoming" : "championship-card--past",
			"logoUrl" => !$championship[ "logoPath" ] || basename( $championship[ "logoPath" ] ) === "SINFOTO.svg"
				? "imagenes/logo-fmjudo.png"
				: $championship[ "logoPath" ],
			"eventUrl" => $isUpcoming
				? "inscripciones.php?campeonato=" . $championship[ "championshipId" ] . "#campeonato-" . $championship[ "championshipId" ]
				: "resultados.php?campeonato=" . $championship[ "championshipId" ],
		];
	}

	return [ "pageTitle" => "Campeonatos", "championships" => $championships, "championshipCount" => count( $championships ) ];
}

/* Prepara las convocatorias públicas con plazo vigente y un resumen del calendario próximo. */
function mod003_getPublicEnrollmentsData( $selectedChampionshipId = null ) {
	$today = new DateTimeImmutable( "today" );
	$openEnrollments = [];
	$upcomingChampionships = [];
	$selectedId = filter_var( $selectedChampionshipId, FILTER_VALIDATE_INT, [ "options" => [ "min_range" => 1 ] ] );

	foreach ( mod002_runQuery( "getUpcomingChampionships" ) as $championship ) {
		$eventDate = $championship[ "championshipDate" ];
		$deadline = $championship[ "registrationDate" ];
		if ( !( $eventDate instanceof DateTimeImmutable ) || $eventDate < $today ) {
			continue;
		}

		if ( count( $upcomingChampionships ) < 3 || ( $selectedId !== false && (int) $championship[ "championshipId" ] === (int) $selectedId ) ) {
			$upcomingChampionships[] = [
				"championshipId" => $championship[ "championshipId" ],
				"eventDay" => $eventDate->format( "d" ),
				"eventMonth" => mod003_monthAbbreviation( $eventDate ),
				"championshipName" => $championship[ "championshipName" ],
				"venue" => $championship[ "venue" ],
				"registrationStatus" => $deadline instanceof DateTimeImmutable && $deadline >= $today
					? "Inscripción abierta hasta el " . mod003_formatSpanishDate( $deadline )
					: "Convocatoria sin plazo abierto",
			];
		}

		if ( !( $deadline instanceof DateTimeImmutable ) || $deadline < $today ) {
			continue;
		}

		$remainingDays = (int) $today->diff( $deadline )->format( "%a" );
		$openEnrollments[] = [
			"championshipId" => $championship[ "championshipId" ],
			"championshipName" => $championship[ "championshipName" ],
			"championshipDate" => mod003_formatSpanishDate( $eventDate ),
			"venue" => $championship[ "venue" ],
			"issuer" => $championship[ "issuer" ] ?: "Federación Madrileña de Judo y D.A.",
			"requirements" => $championship[ "requirements" ] ?: "Consulta los requisitos en tu área privada.",
			"registrationDeadline" => mod003_formatSpanishDate( $deadline ),
			"deadlineNote" => $remainingDays === 0 ? "Último día" : ( $remainingDays === 1 ? "Queda 1 día" : "Quedan $remainingDays días" ),
			"deadlineClass" => $remainingDays <= 7 ? " enrollment-deadline--urgent" : "",
		];
	}

	return [
		"pageTitle" => "Inscripciones abiertas",
		"openEnrollments" => $openEnrollments,
		"openEnrollmentCount" => count( $openEnrollments ),
		"upcomingChampionships" => $upcomingChampionships,
	];
}

/* Agrupa los últimos campeonatos publicados y prepara el podio de cada categoría. */
function mod003_getResultsData( $selectedChampionshipId = null ) {
	$championships = [];
	$championshipIndexes = [];
	$categoryCount = 0;

	foreach ( mod002_runQuery( "getResultCategories" ) as $category ) {
		$championshipId = $category[ "championshipId" ];
		if ( !isset( $championshipIndexes[ $championshipId ] ) ) {
			$date = $category[ "championshipDate" ];
			$championshipYear = $date instanceof DateTimeImmutable ? $date->format( "Y" ) : "Año no disponible";
			$championshipName = $category[ "championshipName" ];
			$championshipDisplayName = $championshipName;
			if ( $championshipYear !== "Año no disponible" ) {
				$nameWithoutYear = trim( preg_replace( "/\\s+" . preg_quote( $championshipYear, "/" ) . "$/u", "", $championshipName ) );
				if ( $nameWithoutYear !== "" ) {
					$championshipDisplayName = $nameWithoutYear;
				}
			}
			$championshipIndexes[ $championshipId ] = count( $championships );
			$championships[] = [
				"championshipId" => $championshipId,
				"championshipName" => $championshipName,
				"championshipDisplayName" => $championshipDisplayName,
				"championshipYear" => $championshipYear,
				"championshipDate" => $date instanceof DateTimeImmutable ? mod003_formatSpanishDate( $date ) : "Fecha no disponible",
				"venue" => $category[ "venue" ],
				"categories" => [],
			];
		}

		$podium = [];
		foreach ( mod002_runQuery( "getBracketRanking", [
			":bracketId" => $category[ "bracketId" ],
			":officialBracketId" => $category[ "bracketId" ],
		] ) as $ranked ) {
			if ( $ranked[ "position" ] === null || $ranked[ "position" ] > 3 ) {
				continue;
			}
			$podium[] = [
				"position" => mod003_ordinalPosition( $ranked[ "position" ] ),
				"medalClass" => mod003_medalClass( $ranked[ "position" ] ),
				"fullName" => $ranked[ "fullName" ],
				"participantMeta" => $ranked[ "clubName" ] . ( $ranked[ "federationNumber" ] !== "" ? " · " . $ranked[ "federationNumber" ] : "" ),
			];
		}

		$index = $championshipIndexes[ $championshipId ];
		$championships[ $index ][ "categories" ][] = [
			"categoryName" => $category[ "categoryName" ],
			"podium" => $podium,
			"rankingUrl" => $category[ "sourceUrl" ] !== "" ? $category[ "sourceUrl" ] : "ranking_descargar.php?cuadro=" . $category[ "bracketId" ],
		];
		$categoryCount++;
	}

	$yearTargets = [];
	foreach ( $championships as $championship ) {
		if ( !isset( $yearTargets[ $championship[ "championshipYear" ] ] ) ) {
			$yearTargets[ $championship[ "championshipYear" ] ] = $championship[ "championshipId" ];
		}
	}

	$selectedId = filter_var( $selectedChampionshipId, FILTER_VALIDATE_INT, [ "options" => [ "min_range" => 1 ] ] );
	$visibleIndex = 0;
	if ( $selectedId !== false && $selectedId !== null ) {
		foreach ( $championships as $championshipIndex => $championship ) {
			if ( (int) $championship[ "championshipId" ] === (int) $selectedId ) {
				$visibleIndex = $championshipIndex;
				break;
			}
		}
	}

	foreach ( $championships as $championshipIndex => $championship ) {
		$nameOptions = [];
		foreach ( $championships as $option ) {
			if ( $option[ "championshipYear" ] !== $championship[ "championshipYear" ] ) {
				continue;
			}
			$nameOptions[] = [
				"optionValue" => $option[ "championshipId" ],
				"optionLabel" => $option[ "championshipDisplayName" ],
				"ariaSelected" => $option[ "championshipId" ] === $championship[ "championshipId" ] ? "true" : "false",
			];
		}

		$yearOptions = [];
		foreach ( $yearTargets as $optionYear => $optionChampionshipId ) {
			$yearOptions[] = [
				"optionValue" => $optionChampionshipId,
				"optionLabel" => $optionYear,
				"ariaSelected" => $optionYear === $championship[ "championshipYear" ] ? "true" : "false",
			];
		}

		$championships[ $championshipIndex ][ "championshipNameOptions" ] = $nameOptions;
		$championships[ $championshipIndex ][ "championshipYearOptions" ] = $yearOptions;
		$championships[ $championshipIndex ][ "hiddenAttribute" ] = $championshipIndex === $visibleIndex ? "" : "hidden";
	}

	return [
		"pageTitle" => "Resultados",
		"championships" => $championships,
		"championshipCount" => count( $championships ),
		"categoryCount" => $categoryCount,
	];
}

function mod003_getRankingDownloadData( $bracketId ) {
	$bracketId = filter_var( $bracketId, FILTER_VALIDATE_INT, [ "options" => [ "min_range" => 1 ] ] );
	if ( $bracketId === false ) {
		throw new DomainException( "La categoría solicitada no es válida." );
	}

	$metadata = null;
	foreach ( mod002_runQuery( "getResultCategories" ) as $category ) {
		if ( $category[ "bracketId" ] === $bracketId ) {
			$metadata = $category;
			break;
		}
	}
	if ( $metadata === null ) {
		throw new DomainException( "El ranking solicitado no está publicado." );
	}

	$rows = [];
	foreach ( mod002_runQuery( "getBracketRanking", [
		":bracketId" => $bracketId,
		":officialBracketId" => $bracketId,
	] ) as $ranked ) {
		$rows[] = [
			"position" => $ranked[ "position" ] === null ? "Sin clasificar" : mod003_ordinalPosition( $ranked[ "position" ] ),
			"fullName" => $ranked[ "fullName" ],
			"federationNumber" => $ranked[ "federationNumber" ],
			"clubName" => $ranked[ "clubName" ],
		];
	}

	return [
		"filename" => "ranking-" . $metadata[ "championshipId" ] . "-" . $metadata[ "subcategoryId" ] . ".pdf",
		"championshipName" => $metadata[ "championshipName" ],
		"categoryName" => $metadata[ "categoryName" ],
		"rows" => $rows,
	];
}

function mod003_medalClass( $position ) {
	$classes = [ 1 => "gold", 2 => "silver", 3 => "bronze" ];
	return $classes[ (int) $position ] ?? "classified";
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

function mod003_getDashboardData( $auth ) {
	if ( !is_array( $auth ) || !isset( $auth[ "role" ], $auth[ "userId" ] ) ) {
		throw new DomainException( "La sesión no contiene un perfil válido." );
	}

	$config = mod003_dashboardConfig( $auth[ "role" ] );
	$summaryRows = mod002_runQuery( $config[ "query" ], [ ":userId" => $auth[ "userId" ] ] );
	$summary = $summaryRows[ 0 ] ?? [
		"profileName" => $auth[ "displayName" ],
		"primaryCount" => 0,
		"secondaryCount" => 0,
		"tertiaryCount" => 0,
	];
	$events = mod003_prepareDashboardEvents( mod002_runQuery( "getUpcomingChampionships" ) );
	$documents = mod003_prepareDashboardDocuments( mod002_runQuery( "getCompetitionDocuments" ) );

	return [
		"pageTitle"       => $config[ "pageTitle" ],
		"eyebrow"         => $config[ "eyebrow" ],
		"welcomeText"     => $config[ "welcomeText" ],
		"roleClass"       => strtolower( $auth[ "role" ] ),
		"profileName"     => $summary[ "profileName" ],
		"username"        => $auth[ "username" ],
		"cards"           => mod003_dashboardCards( $auth[ "role" ], $summary, count( $events ), count( $documents ) ),
		"events"          => $events,
		"documents"       => $documents,
		"eventCount"      => count( $events ),
		"documentCount"   => count( $documents ),
	];
}

function mod003_dashboardConfig( $role ) {
	$config = [
		"CLUB" => [
			"query" => "getClubDashboard",
			"pageTitle" => "Área de clubes",
			"eyebrow" => "Gestión del club",
			"welcomeText" => "Gestiona tus judokas, prepara inscripciones y consulta toda la información de competición.",
		],
		"FEDERADO" => [
			"query" => "getFederatedDashboard",
			"pageTitle" => "Área de federados",
			"eyebrow" => "Perfil deportivo",
			"welcomeText" => "Consulta tu actividad, encuentra competiciones y mantén tu información deportiva al día.",
		],
		"ORGANIZADOR" => [
			"query" => "getOrganizerDashboard",
			"pageTitle" => "Área de organizadores",
			"eyebrow" => "Control de competición",
			"welcomeText" => "Crea eventos y convocatorias, revisa inscripciones y prepara el desarrollo de la competición.",
		],
	];
	if ( !isset( $config[ $role ] ) ) {
		throw new DomainException( "El rol de la sesión no es válido." );
	}
	return $config[ $role ];
}

function mod003_dashboardCards( $role, $summary, $eventCount, $documentCount ) {
	$common = [
		[ "cardUrl" => "#eventos", "cardNumber" => "04", "cardTitle" => "Próximos eventos", "cardText" => "Consulta fechas, sedes y plazos de inscripción.", "cardMeta" => "$eventCount publicados" ],
		[ "cardUrl" => "#documentos", "cardNumber" => "05", "cardTitle" => "Documentación y normativa", "cardText" => "Revisa requisitos, documentos y reglamentos aplicables.", "cardMeta" => "$documentCount documentos" ],
	];

	if ( $role === "CLUB" ) {
		$cards = [
			[ "cardUrl" => "club_judoka_nuevo.php", "cardNumber" => "01", "cardTitle" => "Alta de judoka", "cardText" => "Registra un nuevo federado y vincúlalo al club.", "cardMeta" => "Nueva alta" ],
			[ "cardUrl" => "club_judokas.php", "cardNumber" => "02", "cardTitle" => "Mis judokas", "cardText" => "Consulta categorías, actividad y resultados del último año.", "cardMeta" => $summary[ "primaryCount" ] . " judokas" ],
			[ "cardUrl" => "club_inscripcion.php", "cardNumber" => "03", "cardTitle" => "Inscripciones", "cardText" => "Inscribe judokas y revisa el estado de cada solicitud.", "cardMeta" => $summary[ "secondaryCount" ] . " registradas" ],
		];
	} elseif ( $role === "FEDERADO" ) {
		$cards = [
			[ "cardUrl" => "federado_ficha.php", "cardNumber" => "01", "cardTitle" => "Mi ficha", "cardText" => "Consulta tus datos federativos, club y resultados.", "cardMeta" => $summary[ "tertiaryCount" ] . " kg" ],
			[ "cardUrl" => "federado_inscripcion.php", "cardNumber" => "02", "cardTitle" => "Solicitar inscripción", "cardText" => "Pide a tu club que tramite tu participación en un campeonato.", "cardMeta" => "Enviar al club" ],
			[ "cardUrl" => "#gestion", "cardNumber" => "03", "cardTitle" => "Actividad deportiva", "cardText" => "Revisa inscripciones, combates y resultados.", "cardMeta" => $summary[ "primaryCount" ] . " inscripciones" ],
		];
	} else {
		$cards = [
			[ "cardUrl" => "organizador_campeonato_nuevo.php", "cardNumber" => "01", "cardTitle" => "Alta de campeonato", "cardText" => "Crea el evento, su sede, fecha y niveles de competición.", "cardMeta" => "Nuevo evento" ],
			[ "cardUrl" => "organizador_documentos.php", "cardNumber" => "02", "cardTitle" => "Convocatorias", "cardText" => "Publica o sustituye circulares y normativa oficial.", "cardMeta" => $summary[ "primaryCount" ] . " próximos" ],
			[ "cardUrl" => "#gestion", "cardNumber" => "03", "cardTitle" => "Inscripciones y cuadros", "cardText" => "Valida participantes y prepara combates y repescas.", "cardMeta" => $summary[ "secondaryCount" ] . " inscripciones" ],
		];
	}

	return array_merge( $cards, $common );
}

/* Agrupa los judokas del club y sus participaciones durante los últimos doce meses. */
function mod003_getClubJudokasData( $auth ) {
	if ( !is_array( $auth ) || ( $auth[ "role" ] ?? "" ) !== "CLUB" || empty( $auth[ "userId" ] ) ) {
		throw new DomainException( "La sesión no contiene un club válido." );
	}

	$judokas = mod002_runQuery( "getClubJudokas", [ ":userId" => $auth[ "userId" ] ] );
	$judokaIndexes = [];
	foreach ( $judokas as $index => $judoka ) {
		$judokaIndexes[ $judoka[ "federationNumber" ] ] = $index;
		$judokas[ $index ][ "categoryName" ] = $judoka[ "categoryName" ] ?: "Sin categoría compatible";
		$judokas[ $index ][ "competitions" ] = [];
	}

	foreach ( mod002_runQuery( "getClubJudokaRecentResults", [ ":userId" => $auth[ "userId" ] ] ) as $result ) {
		if ( !isset( $judokaIndexes[ $result[ "federationNumber" ] ] ) ) {
			continue;
		}
		$position = $result[ "position" ];
		$date = $result[ "championshipDate" ];
		$judokas[ $judokaIndexes[ $result[ "federationNumber" ] ] ][ "competitions" ][] = [
			"championshipName" => $result[ "championshipName" ],
			"championshipDate" => $date instanceof DateTimeImmutable ? $date->format( "d/m/Y" ) : "Fecha no disponible",
			"venue" => $result[ "venue" ],
			"competitionCategory" => $result[ "competitionCategory" ],
			"position" => $position === null ? "Sin clasificación" : mod003_ordinalPosition( $position ),
			"positionClass" => $position !== null && $position <= 3 ? "result-badge--podium" : "result-badge--classified",
		];
	}

	$clubName = $judokas[ 0 ][ "clubName" ] ?? ( $auth[ "displayName" ] ?? "Club" );
	return [
		"pageTitle" => "Judokas del club",
		"username" => $auth[ "username" ],
		"clubName" => $clubName,
		"judokaCount" => count( $judokas ),
		"judokas" => $judokas,
	];
}

/* Prepara la ficha privada del federado y su historial de clasificaciones. */
function mod003_getFederatedProfileData( $auth ) {
	if ( !is_array( $auth ) || ( $auth[ "role" ] ?? "" ) !== "FEDERADO" || empty( $auth[ "userId" ] ) ) {
		throw new DomainException( "La sesión no contiene un federado válido." );
	}

	$profileRows = mod002_runQuery( "getFederatedProfile", [ ":userId" => $auth[ "userId" ] ] );
	$profile = $profileRows[ 0 ] ?? null;
	if ( $profile === null ) {
		throw new DomainException( "No se ha encontrado la ficha asociada a este usuario." );
	}

	$birthDate = $profile[ "birthDate" ];
	$results = [];
	foreach ( mod002_runQuery( "getFederatedChampionshipResults", [ ":userId" => $auth[ "userId" ] ] ) as $row ) {
		$championshipDate = $row[ "championshipDate" ];
		$position = $row[ "position" ];
		$results[] = [
			"championshipName" => $row[ "championshipName" ],
			"championshipDate" => $championshipDate instanceof DateTimeImmutable ? $championshipDate->format( "d/m/Y" ) : "Fecha no disponible",
			"venue" => $row[ "venue" ],
			"subcategoryName" => $row[ "subcategoryName" ],
			"position" => $position === null ? "Sin clasificar" : mod003_ordinalPosition( $position ),
			"positionClass" => $position !== null && $position <= 3 ? "podium" : "classified",
		];
	}

	return [
		"pageTitle" => "Mi ficha",
		"username" => $auth[ "username" ],
		"initials" => mod003_initials( $profile[ "firstName" ], $profile[ "lastName" ] ),
		"fullName" => trim( $profile[ "firstName" ] . " " . $profile[ "lastName" ] ),
		"federationNumber" => $profile[ "federationNumber" ],
		"birthDate" => $birthDate instanceof DateTimeImmutable ? $birthDate->format( "d/m/Y" ) : "No disponible",
		"age" => $profile[ "age" ],
		"gender" => $profile[ "gender" ],
		"weight" => $profile[ "weight" ],
		"clubName" => $profile[ "clubName" ] ?: "Sin club asignado",
		"clubSince" => $profile[ "clubSince" ] instanceof DateTimeImmutable ? "Desde " . $profile[ "clubSince" ]->format( "d/m/Y" ) : "Afiliación no disponible",
		"championships" => $results,
		"championshipCount" => count( $results ),
	];
}

function mod003_ordinalPosition( $position ) {
	return (int) $position . ".º puesto";
}

function mod003_initials( $firstName, $lastName ) {
	$first = trim( (string) $firstName );
	$last = trim( (string) $lastName );
	$initials = ( $first !== "" ? mod003_textSlice( $first, 0, 1 ) : "" )
		. ( $last !== "" ? mod003_textSlice( $last, 0, 1 ) : "" );
	return strtoupper( $initials );
}

function mod003_textSlice( $text, $start, $length ) {
	return function_exists( "mb_substr" ) ? mb_substr( $text, $start, $length ) : substr( $text, $start, $length );
}

function mod003_prepareDashboardEvents( $championships ) {
	$events = [];
	foreach ( $championships as $championship ) {
		$date = $championship[ "championshipDate" ];
		if ( !( $date instanceof DateTimeImmutable ) ) {
			continue;
		}
		$deadline = $championship[ "registrationDate" ];
		$events[] = [
			"eventDateIso" => $date->format( "Y-m-d" ),
			"eventDay" => $date->format( "d" ),
			"eventMonth" => mod003_monthAbbreviation( $date ),
			"eventName" => $championship[ "championshipName" ],
			"eventVenue" => $championship[ "venue" ],
			"eventDeadline" => $deadline instanceof DateTimeImmutable ? "Plazo: " . mod003_formatSpanishDate( $deadline ) : "Consulta la convocatoria",
		];
	}
	return $events;
}

function mod003_prepareDashboardDocuments( $rows ) {
	$documents = [];
	foreach ( $rows as $row ) {
		$documentFiles = [];
		if ( !empty( $row[ "circularPath" ] ) ) {
			$documentFiles[] = [
				"documentUrl" => $row[ "circularPath" ],
				"documentLabel" => "Circular de inscripción",
			];
		}
		if ( !empty( $row[ "regulationPath" ] ) ) {
			$documentFiles[] = [
				"documentUrl" => $row[ "regulationPath" ],
				"documentLabel" => "Normativa oficial",
			];
		}
		$documents[] = [
			"documentTitle" => $row[ "championshipName" ],
			"documentIssuer" => $row[ "issuer" ],
			"documentRegulation" => $row[ "regulation" ],
			"documentText" => $row[ "documents" ],
			"documentMark" => $documentFiles ? "PDF" : "INFO",
			"documentFiles" => $documentFiles,
		];
	}
	return $documents;
}

function mod003_createClubJudoka( $auth, $input, $photoFile = null ) {
	$values = [
		"federationNumber" => strtoupper( trim( (string) ( $input[ "numfederado" ] ?? "" ) ) ),
		"firstName" => trim( (string) ( $input[ "nombre" ] ?? "" ) ),
		"lastName" => trim( (string) ( $input[ "apellidos" ] ?? "" ) ),
		"weight" => filter_var( $input[ "pesaje" ] ?? null, FILTER_VALIDATE_INT ),
		"birthDate" => trim( (string) ( $input[ "fecnacimiento" ] ?? "" ) ),
		"gender" => (string) ( $input[ "genero" ] ?? "" ),
	];
	$errors = [];

	if ( !preg_match( '/^[A-Z0-9]{9}$/', $values[ "federationNumber" ] ) ) {
		$errors[] = "El número federativo debe tener exactamente 9 letras o números.";
	}
	if ( $values[ "firstName" ] === "" || mod003_textLength( $values[ "firstName" ] ) > 12 ) {
		$errors[] = "El nombre es obligatorio y admite un máximo de 12 caracteres.";
	}
	if ( $values[ "lastName" ] === "" || mod003_textLength( $values[ "lastName" ] ) > 30 ) {
		$errors[] = "Los apellidos son obligatorios y admiten un máximo de 30 caracteres.";
	}
	if ( $values[ "weight" ] === false || $values[ "weight" ] < 1 || $values[ "weight" ] > 255 ) {
		$errors[] = "El pesaje debe ser un número entre 1 y 255 kg.";
	}
	if ( !mod003_validDate( $values[ "birthDate" ] ) ) {
		$errors[] = "La fecha de nacimiento no es válida.";
	} elseif ( new DateTimeImmutable( $values[ "birthDate" ] ) > new DateTimeImmutable( "-18 years" ) ) {
		$errors[] = "Para esta demostración se requiere una persona mayor de edad.";
	}
	if ( !in_array( $values[ "gender" ], [ "1", "0" ], true ) ) {
		$errors[] = "Selecciona el género deportivo.";
	}
	$photo = mod003_validateJudokaPhoto( $photoFile );
	if ( $photo[ "error" ] !== null ) {
		$errors[] = $photo[ "error" ];
	}
	if ( $errors ) {
		return [ "ok" => false, "errors" => $errors, "values" => $values ];
	}

	$photoPath = "imagenes/SINFOTO.svg";
	if ( $photo[ "temporaryPath" ] !== null ) {
		$photoPath = uploads_storeJudokaPhoto( $photo[ "temporaryPath" ], $photo[ "extension" ] );
	}

	try {
		mod002_transaction( function () use ( $auth, $values, $photoPath ) {
			mod002_runStatement( "createJudoka", [
				":federationNumber" => $values[ "federationNumber" ],
				":firstName" => $values[ "firstName" ],
				":lastName" => $values[ "lastName" ],
				":weight" => $values[ "weight" ],
				":birthDate" => $values[ "birthDate" ],
				":gender" => (int) $values[ "gender" ],
				":photo" => $photoPath,
			] );
			$link = mod002_runStatement( "linkJudokaToClub", [
				":federationNumber" => $values[ "federationNumber" ],
				":userId" => $auth[ "userId" ],
			] );
			if ( $link[ "affectedRows" ] !== 1 ) {
				throw new RuntimeException( "No se ha encontrado el club asociado a la sesión." );
			}
		} );
	} catch ( Throwable $e ) {
		if ( $photoPath !== "imagenes/SINFOTO.svg" ) {
			uploads_deleteJudokaPhoto( $photoPath );
		}
		throw $e;
	}

	return [ "ok" => true, "errors" => [], "values" => [ "federationNumber" => "", "firstName" => "", "lastName" => "", "weight" => "", "birthDate" => "", "gender" => "1" ] ];
}

function mod003_validateJudokaPhoto( $photoFile ) {
	$empty = [ "temporaryPath" => null, "extension" => null, "error" => null ];
	if ( !is_array( $photoFile ) || ( $photoFile[ "error" ] ?? UPLOAD_ERR_NO_FILE ) === UPLOAD_ERR_NO_FILE ) {
		return $empty;
	}
	if ( ( $photoFile[ "error" ] ?? UPLOAD_ERR_NO_FILE ) !== UPLOAD_ERR_OK ) {
		$empty[ "error" ] = "La fotografía no se ha podido subir correctamente.";
		return $empty;
	}
	if ( (int) ( $photoFile[ "size" ] ?? 0 ) > 2 * 1024 * 1024 ) {
		$empty[ "error" ] = "La fotografía no puede superar los 2 MB.";
		return $empty;
	}

	$temporaryPath = (string) ( $photoFile[ "tmp_name" ] ?? "" );
	if ( $temporaryPath === "" || !is_uploaded_file( $temporaryPath ) ) {
		$empty[ "error" ] = "El archivo recibido no es una subida válida.";
		return $empty;
	}
	$finfo = new finfo( FILEINFO_MIME_TYPE );
	$mime = $finfo->file( $temporaryPath );
	$extensions = [ "image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp" ];
	if ( !isset( $extensions[ $mime ] ) || @getimagesize( $temporaryPath ) === false ) {
		$empty[ "error" ] = "Selecciona una imagen JPG, PNG o WebP válida.";
		return $empty;
	}

	return [ "temporaryPath" => $temporaryPath, "extension" => $extensions[ $mime ], "error" => null ];
}

function mod003_getEnrollmentOptions( $auth ) {
	$key = $auth[ "role" ] === "CLUB" ? "getClubEnrollmentOptions" : "getFederatedEnrollmentOptions";
	return mod002_runQuery( $key, [ ":userId" => $auth[ "userId" ] ] );
}

function mod003_createEnrollment( $auth, $selectedValue ) {
	if ( ( $auth[ "role" ] ?? "" ) !== "CLUB" ) {
		throw new DomainException( "Solo los clubes pueden formalizar inscripciones." );
	}
	$options = mod003_getEnrollmentOptions( $auth );
	$selected = mod003_findEnrollmentOption( $options, $selectedValue );
	if ( $selected === null ) {
		return [ "ok" => false, "errors" => [ "Selecciona una inscripción disponible para tu perfil." ] ];
	}

	mod002_runStatement( "createEnrollment", [
		":federationNumber" => $selected[ "federationNumber" ],
		":championshipId" => $selected[ "championshipId" ],
		":subcategoryId" => $selected[ "subcategoryId" ],
		":weight" => $selected[ "weight" ],
	] );
	return [ "ok" => true, "errors" => [] ];
}

function mod003_requestClubEnrollment( $auth, $selectedValue ) {
	if ( ( $auth[ "role" ] ?? "" ) !== "FEDERADO" ) {
		throw new DomainException( "Esta solicitud solo está disponible para federados." );
	}
	$options = mod003_getEnrollmentOptions( $auth );
	$selected = mod003_findEnrollmentOption( $options, $selectedValue );
	if ( $selected === null ) {
		return [ "ok" => false, "errors" => [ "Selecciona un campeonato disponible para solicitarlo a tu club." ] ];
	}

	mail_sendEnrollmentRequest( $selected );
	return [ "ok" => true, "errors" => [], "clubName" => $selected[ "clubName" ] ];
}

function mod003_findEnrollmentOption( $options, $selectedValue ) {
	foreach ( $options as $option ) {
		if ( hash_equals( $option[ "optionValue" ], (string) $selectedValue ) ) {
			return $option;
		}
	}
	return null;
}

function mod003_getSubcategoryOptions() {
	return mod002_runQuery( "getSubcategories" );
}

function mod003_getOrganizerCallOptions() {
	return mod002_runQuery( "getOrganizerCalls" );
}

function mod003_updateCallDocuments( $selectedValue, $files ) {
	$callId = filter_var( $selectedValue, FILTER_VALIDATE_INT, [ "options" => [ "min_range" => 1 ] ] );
	$selected = null;
	foreach ( mod003_getOrganizerCallOptions() as $option ) {
		if ( $option[ "callId" ] === $callId ) {
			$selected = $option;
			break;
		}
	}
	if ( $selected === null ) {
		return [ "ok" => false, "errors" => [ "Selecciona una convocatoria válida." ] ];
	}

	$circular = mod003_validateOfficialPdf( $files[ "circular" ] ?? null, "la circular de inscripción", false );
	$regulationDocument = mod003_validateOfficialPdf( $files[ "normativa" ] ?? null, "la normativa de la convocatoria", false );
	$errors = [];
	if ( $circular[ "error" ] !== null ) $errors[] = $circular[ "error" ];
	if ( $regulationDocument[ "error" ] !== null ) $errors[] = $regulationDocument[ "error" ];
	if ( $circular[ "temporaryPath" ] === null && $regulationDocument[ "temporaryPath" ] === null ) {
		$errors[] = "Selecciona al menos un PDF para publicar o sustituir.";
	}
	if ( $errors ) return [ "ok" => false, "errors" => $errors ];

	$storedDocuments = [];
	try {
		$circularPath = null;
		$regulationPath = null;
		if ( $circular[ "temporaryPath" ] !== null ) {
			$circularPath = uploads_storeCallDocument( $circular[ "temporaryPath" ], "circular" );
			$storedDocuments[] = $circularPath;
		}
		if ( $regulationDocument[ "temporaryPath" ] !== null ) {
			$regulationPath = uploads_storeCallDocument( $regulationDocument[ "temporaryPath" ], "normativa" );
			$storedDocuments[] = $regulationPath;
		}

		mod002_runStatement( "updateCallDocuments", [
			":callId" => $selected[ "callId" ],
			":circularPath" => $circularPath,
			":regulationPath" => $regulationPath,
		] );
	} catch ( Throwable $e ) {
		foreach ( $storedDocuments as $storedDocument ) {
			uploads_deleteCallDocument( $storedDocument );
		}
		throw $e;
	}

	if ( $circularPath !== null ) uploads_deleteCallDocument( $selected[ "circularPath" ] );
	if ( $regulationPath !== null ) uploads_deleteCallDocument( $selected[ "regulationPath" ] );
	return [ "ok" => true, "errors" => [] ];
}

function mod003_createChampionshipCall( $input, $files = [] ) {
	$values = [
		"name" => trim( (string) ( $input[ "nombre" ] ?? "" ) ),
		"eventDate" => trim( (string) ( $input[ "fecha" ] ?? "" ) ),
		"venue" => trim( (string) ( $input[ "sede" ] ?? "" ) ),
		"subcategoryId" => filter_var( $input[ "subcategoria" ] ?? null, FILTER_VALIDATE_INT ),
		"issuer" => trim( (string) ( $input[ "organismo" ] ?? "" ) ),
		"deadline" => trim( (string) ( $input[ "plazo" ] ?? "" ) ),
		"requirements" => trim( (string) ( $input[ "requisitos" ] ?? "" ) ),
		"documents" => trim( (string) ( $input[ "documentos" ] ?? "" ) ),
		"regulation" => trim( (string) ( $input[ "reglamento" ] ?? "" ) ),
		"ageCutoff" => trim( (string) ( $input[ "corte_edad" ] ?? "" ) ),
	];
	$errors = [];
	if ( $values[ "name" ] === "" || mod003_textLength( $values[ "name" ] ) > 20 ) $errors[] = "El campeonato necesita un nombre de hasta 20 caracteres.";
	if ( !mod003_validDate( $values[ "eventDate" ] ) || $values[ "eventDate" ] < date( "Y-m-d" ) ) $errors[] = "La fecha del campeonato debe ser válida y futura.";
	if ( $values[ "venue" ] === "" || mod003_textLength( $values[ "venue" ] ) > 100 ) $errors[] = "La sede es obligatoria y admite hasta 100 caracteres.";
	$subcategories = mod003_getSubcategoryOptions();
	if ( !in_array( $values[ "subcategoryId" ], array_column( $subcategories, "subcategoryId" ), true ) ) $errors[] = "Selecciona una categoría válida.";
	if ( $values[ "issuer" ] === "" || mod003_textLength( $values[ "issuer" ] ) > 100 ) $errors[] = "El organismo emisor es obligatorio.";
	if ( !mod003_validDate( $values[ "deadline" ] ) || ( mod003_validDate( $values[ "eventDate" ] ) && $values[ "deadline" ] > $values[ "eventDate" ] ) ) $errors[] = "El plazo debe ser una fecha válida anterior al campeonato.";
	if ( $values[ "requirements" ] === "" ) $errors[] = "Indica los requisitos de participación.";
	if ( $values[ "documents" ] === "" ) $errors[] = "Indica la documentación necesaria.";
	if ( $values[ "regulation" ] === "" || mod003_textLength( $values[ "regulation" ] ) > 100 ) $errors[] = "Indica una versión de normativa de hasta 100 caracteres.";
	if ( !mod003_validDate( $values[ "ageCutoff" ] ) ) $errors[] = "La fecha de corte de edad no es válida.";
	$circular = mod003_validateOfficialPdf( $files[ "circular" ] ?? null, "la circular de inscripción" );
	$regulationDocument = mod003_validateOfficialPdf( $files[ "normativa" ] ?? null, "la normativa de la convocatoria" );
	if ( $circular[ "error" ] !== null ) $errors[] = $circular[ "error" ];
	if ( $regulationDocument[ "error" ] !== null ) $errors[] = $regulationDocument[ "error" ];
	if ( $errors ) return [ "ok" => false, "errors" => $errors, "values" => $values ];

	$storedDocuments = [];
	try {
		$circularPath = uploads_storeCallDocument( $circular[ "temporaryPath" ], "circular" );
		$storedDocuments[] = $circularPath;
		$regulationPath = uploads_storeCallDocument( $regulationDocument[ "temporaryPath" ], "normativa" );
		$storedDocuments[] = $regulationPath;

		mod002_transaction( function () use ( $values, $circularPath, $regulationPath ) {
			$created = mod002_runStatement( "createChampionship", [
				":name" => $values[ "name" ], ":eventDate" => $values[ "eventDate" ],
				":logo" => "imagenes/SINFOTO.svg", ":venue" => $values[ "venue" ],
			] );
			$id = $created[ "insertId" ];
			mod002_runStatement( "createCompetitionLevel", [ ":championshipId" => $id, ":subcategoryId" => $values[ "subcategoryId" ] ] );
			mod002_runStatement( "createCall", [
				":championshipId" => $id, ":issuer" => $values[ "issuer" ], ":deadline" => $values[ "deadline" ],
				":requirements" => $values[ "requirements" ], ":documents" => $values[ "documents" ],
				":regulation" => $values[ "regulation" ], ":ageCutoff" => $values[ "ageCutoff" ],
				":circularPath" => $circularPath, ":regulationPath" => $regulationPath,
			] );
		} );
	} catch ( Throwable $e ) {
		foreach ( $storedDocuments as $storedDocument ) {
			uploads_deleteCallDocument( $storedDocument );
		}
		throw $e;
	}

	return [ "ok" => true, "errors" => [], "values" => [] ];
}

function mod003_validateOfficialPdf( $file, $label, $required = true ) {
	$result = [ "temporaryPath" => null, "error" => null ];
	if ( !is_array( $file ) || ( $file[ "error" ] ?? UPLOAD_ERR_NO_FILE ) === UPLOAD_ERR_NO_FILE ) {
		if ( $required ) $result[ "error" ] = "Adjunta $label en formato PDF.";
		return $result;
	}
	if ( in_array( $file[ "error" ] ?? UPLOAD_ERR_NO_FILE, [ UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ], true ) ) {
		$result[ "error" ] = "El archivo de $label no puede superar los 10 MB.";
		return $result;
	}
	if ( ( $file[ "error" ] ?? UPLOAD_ERR_NO_FILE ) !== UPLOAD_ERR_OK ) {
		$result[ "error" ] = "El archivo de $label no se ha podido subir correctamente.";
		return $result;
	}
	if ( (int) ( $file[ "size" ] ?? 0 ) <= 0 || (int) $file[ "size" ] > 10 * 1024 * 1024 ) {
		$result[ "error" ] = "El archivo de $label debe ser un PDF de hasta 10 MB.";
		return $result;
	}

	$temporaryPath = (string) ( $file[ "tmp_name" ] ?? "" );
	if ( $temporaryPath === "" || !is_uploaded_file( $temporaryPath ) ) {
		$result[ "error" ] = "El archivo recibido para $label no es una subida válida.";
		return $result;
	}
	$finfo = new finfo( FILEINFO_MIME_TYPE );
	$signature = file_get_contents( $temporaryPath, false, null, 0, 5 );
	if ( $finfo->file( $temporaryPath ) !== "application/pdf" || $signature !== "%PDF-" ) {
		$result[ "error" ] = "El archivo de $label debe ser un PDF válido.";
		return $result;
	}

	return [ "temporaryPath" => $temporaryPath, "error" => null ];
}

function mod003_validDate( $date ) {
	$parsed = DateTimeImmutable::createFromFormat( "!Y-m-d", (string) $date );
	return $parsed !== false && $parsed->format( "Y-m-d" ) === $date;
}

function mod003_textLength( $text ) {
	return function_exists( "mb_strlen" ) ? mb_strlen( $text ) : strlen( $text );
}
