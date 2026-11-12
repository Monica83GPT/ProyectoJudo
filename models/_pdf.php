<?php

/* Generador PDF pequeño para documentos de texto sin dependencias externas. */
function mod004_buildRankingPdf( $data ) {
	$lines = [
		[ 18, "Ranking oficial" ],
		[ 12, $data[ "championshipName" ] ],
		[ 11, "Categoría: " . $data[ "categoryName" ] ],
		[ 9, "Generado el " . date( "d/m/Y" ) ],
		[ 10, "" ],
		[ 10, "PUESTO    JUDOKA                                      LICENCIA       CLUB" ],
		[ 9, str_repeat( "-", 104 ) ],
	];

	foreach ( $data[ "rows" ] as $row ) {
		$lines[] = [ 10, mod004_pdf_clip( $row[ "position" ], 10 ) . "    " . mod004_pdf_clip( $row[ "fullName" ], 42 ) ];
		$lines[] = [ 9, "          Licencia: " . mod004_pdf_clip( $row[ "federationNumber" ], 18 ) . "    Club: " . mod004_pdf_clip( $row[ "clubName" ], 42 ) ];
		$lines[] = [ 8, "" ];
	}

	$chunks = array_chunk( $lines, 43 );
	$objects = [];
	$objects[] = "<< /Type /Catalog /Pages 2 0 R >>";
	$objects[] = "";
	$objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";
	$pageReferences = [];
	foreach ( $chunks as $chunk ) {
		$content = "BT\n";
		$y = 790;
		foreach ( $chunk as $line ) {
			$fontSize = $line[ 0 ];
			$content .= "/F1 " . $fontSize . " Tf 1 0 0 1 48 " . $y . " Tm (" . mod004_pdf_escape( $line[ 1 ] ) . ") Tj\n";
			$y -= $fontSize >= 16 ? 26 : ( $fontSize >= 10 ? 18 : 14 );
		}
		$content .= "ET";
		$contentObject = count( $objects ) + 1;
		$pageObject = $contentObject + 1;
		$objects[] = "<< /Length " . strlen( $content ) . " >>\nstream\n" . $content . "\nendstream";
		$objects[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents " . $contentObject . " 0 R >>";
		$pageReferences[] = $pageObject . " 0 R";
	}
	$objects[ 1 ] = "<< /Type /Pages /Kids [ " . implode( " ", $pageReferences ) . " ] /Count " . count( $pageReferences ) . " >>";

	$pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
	$offsets = [ 0 ];
	foreach ( $objects as $index => $object ) {
		$objectNumber = $index + 1;
		$offsets[] = strlen( $pdf );
		$pdf .= $objectNumber . " 0 obj\n" . $object . "\nendobj\n";
	}
	$xrefOffset = strlen( $pdf );
	$pdf .= "xref\n0 " . ( count( $objects ) + 1 ) . "\n0000000000 65535 f \n";
	for ( $index = 1; $index < count( $offsets ); $index++ ) {
		$pdf .= sprintf( "%010d 00000 n \n", $offsets[ $index ] );
	}
	$pdf .= "trailer\n<< /Size " . ( count( $objects ) + 1 ) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF";
	return $pdf;
}

function mod004_buildPendingDocumentationPdf() {
	$lines = [
		[ 20, "Documentación pendiente" ],
		[ 12, "Federación Madrileña de Judo y Deportes Asociados" ],
		[ 10, "" ],
		[ 11, "La documentación oficial de esta convocatoria todavía no ha sido publicada." ],
		[ 11, "Este archivo es un marcador temporal y será sustituido por el organizador." ],
		[ 10, "" ],
		[ 10, "Consulta de nuevo la convocatoria antes de realizar la inscripción." ],
	];

	$content = "BT\n";
	$y = 760;
	foreach ( $lines as $line ) {
		$content .= "/F1 " . $line[ 0 ] . " Tf 1 0 0 1 48 " . $y . " Tm (" . mod004_pdf_escape( $line[ 1 ] ) . ") Tj\n";
		$y -= $line[ 0 ] >= 18 ? 32 : 24;
	}
	$content .= "ET";

	$objects = [
		"<< /Type /Catalog /Pages 2 0 R >>",
		"<< /Type /Pages /Kids [ 5 0 R ] /Count 1 >>",
		"<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>",
		"<< /Length " . strlen( $content ) . " >>\nstream\n" . $content . "\nendstream",
		"<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 3 0 R >> >> /Contents 4 0 R >>",
	];

	$pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
	$offsets = [ 0 ];
	foreach ( $objects as $index => $object ) {
		$offsets[] = strlen( $pdf );
		$pdf .= ( $index + 1 ) . " 0 obj\n" . $object . "\nendobj\n";
	}
	$xrefOffset = strlen( $pdf );
	$pdf .= "xref\n0 " . ( count( $objects ) + 1 ) . "\n0000000000 65535 f \n";
	for ( $index = 1; $index < count( $offsets ); $index++ ) {
		$pdf .= sprintf( "%010d 00000 n \n", $offsets[ $index ] );
	}
	$pdf .= "trailer\n<< /Size " . ( count( $objects ) + 1 ) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF";
	return $pdf;
}

function mod004_pdf_escape( $value ) {
	$value = (string) $value;
	if ( function_exists( "iconv" ) ) {
		$converted = iconv( "UTF-8", "Windows-1252//TRANSLIT//IGNORE", $value );
		if ( $converted !== false ) {
			$value = $converted;
		}
	}
	return str_replace( [ "\\", "(", ")", "\r", "\n" ], [ "\\\\", "\\(", "\\)", " ", " " ], $value );
}

function mod004_pdf_clip( $value, $length ) {
	$value = (string) $value;
	if ( function_exists( "mb_strimwidth" ) ) {
		return mb_strimwidth( $value, 0, $length, "…", "UTF-8" );
	}
	return strlen( $value ) > $length ? substr( $value, 0, $length - 3 ) . "..." : $value;
}
