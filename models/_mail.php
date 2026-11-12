<?php
/* Auxiliar compartido para el envío de correos; no constituye una capa nueva. */

function mail_sendEnrollmentRequest( $request ) {
	$recipient = trim( (string) ( $request[ "clubEmail" ] ?? "" ) );
	if ( filter_var( $recipient, FILTER_VALIDATE_EMAIL ) === false ) {
		throw new RuntimeException( "El club no tiene un correo de contacto válido." );
	}

	$configPath = __DIR__ . "/../config/mail.php";
	if ( !is_file( $configPath ) ) {
		throw new RuntimeException( "Falta configurar el remitente de correo de la aplicación." );
	}
	$config = require $configPath;
	$fromAddress = trim( (string) ( $config[ "from_address" ] ?? "" ) );
	$fromName = trim( (string) ( $config[ "from_name" ] ?? "FMJYDA Competiciones" ) );
	if ( filter_var( $fromAddress, FILTER_VALIDATE_EMAIL ) === false || preg_match( '/[\r\n]/', $fromName ) ) {
		throw new RuntimeException( "La configuración del remitente de correo no es válida." );
	}

	$date = $request[ "championshipDate" ] ?? false;
	$dateText = $date instanceof DateTimeImmutable ? $date->format( "d/m/Y" ) : "Fecha no disponible";
	$subjectText = "Solicitud de inscripción · " . $request[ "championshipName" ];
	$subject = "=?UTF-8?B?" . base64_encode( $subjectText ) . "?=";
	$body = implode( "\r\n", [
		"Hola, " . $request[ "clubName" ] . ":",
		"",
		"El federado " . $request[ "fullName" ] . " solicita que el club tramite su inscripción.",
		"",
		"Campeonato: " . $request[ "championshipName" ],
		"Categoría: " . $request[ "categoryName" ],
		"Fecha: " . $dateText,
		"Sede: " . $request[ "venue" ],
		"Número federativo: " . $request[ "federationNumber" ],
		"Peso registrado: " . $request[ "weight" ] . " kg",
		"",
		"Esta comunicación es una solicitud. La inscripción debe realizarla el club desde su área privada.",
	] );
	$encodedName = "=?UTF-8?B?" . base64_encode( $fromName ) . "?=";
	$headers = implode( "\r\n", [
		"From: $encodedName <$fromAddress>",
		"MIME-Version: 1.0",
		"Content-Type: text/plain; charset=UTF-8",
		"Content-Transfer-Encoding: 8bit",
	] );

	if ( !@mail( $recipient, $subject, $body, $headers ) ) {
		throw new RuntimeException( "No se ha podido enviar la solicitud al club. Inténtalo más tarde." );
	}
}
