<?php
/* v04 · _render.php — rellena una plantilla HTML con datos.
   No es una capa: es una herramienta que usa la presentación (mod004). Por eso el guion bajo.

   Lo que puede llevar una plantilla:
     #nombre#                                → un dato; sale SIEMPRE escapado
     ##nombre## … ##/nombre##                → se repite una vez por cada fila de esa lista
     ##nombre:empty## … ##/nombre:empty##    → sale solo si esa lista no tiene filas
   Los nombres empiezan por letra y solo llevan letras, números y _.
   Dentro de un bloque solo se ven los datos de su fila.

   Hay dos versiones de render que hacen exactamente lo mismo:
     render()       → busca marcas y bloques a mano, con strpos y substr;
     render_regex() → los busca con una expresión regular. */


/* ============ VALIDAR LA PLANTILLA (la usan las dos versiones) ============ */

/* Devuelve la lista de problemas de la plantilla; vacía si está bien. No necesita datos. */
function validateTemplate( $template ) {
	$problems = [];
	$open     = [];	// los bloques abiertos y aún sin cerrar, en orden
	$pos      = 0;

	while ( ( $start = strpos( $template, "##", $pos ) ) !== false ) {
		$end = strpos( $template, "##", $start + 2 );
		if ( $end === false ) {
			break;
		}
		$tag = substr( $template, $start + 2, $end - $start - 2 );

		if ( $tag !== "" && $tag[ 0 ] === "/" && render_isBlockName( substr( $tag, 1 ) ) ) {
			// un cierre: ##/nombre##
			$name = substr( $tag, 1 );
			if ( count( $open ) > 0 && $open[ count( $open ) - 1 ] === $name ) {
				array_pop( $open );
			} elseif ( in_array( $name, $open, true ) ) {
				$problems[] = "##/$name## cierra antes que ##" . $open[ count( $open ) - 1 ] . "##: bloques cruzados";
				array_splice( $open, array_search( $name, $open, true ), 1 );
			} else {
				$problems[] = "##/$name## cierra un bloque que no está abierto";
			}
			$pos = $end + 2;
		} elseif ( render_isBlockName( $tag ) ) {
			// una apertura: ##nombre##
			$open[] = $tag;
			$pos    = $end + 2;
		} else {
			$pos = $start + 2;	// "##" que no es de un bloque: se sigue buscando
		}
	}

	foreach ( $open as $name ) {
		$problems[] = "##$name## no tiene su ##/$name##";
	}

	// cada ##x:empty## necesita su ##x##
	$pos = 0;
	while ( ( $start = strpos( $template, ":empty##", $pos ) ) !== false ) {
		$hash = strrpos( substr( $template, 0, $start ), "##" );
		$name = substr( $template, $hash + 2, $start - $hash - 2 );
		if ( render_isName( $name ) && strpos( $template, "##$name##" ) === false ) {
			$problems[] = "##$name:empty## no tiene su bloque ##$name##";
		}
		$pos = $start + 8;
	}

	return array_values( array_unique( $problems ) );
}


/* ============ VERSIÓN 1 · render() con strpos y substr ============ */

function render( $template, $data ) {
	foreach ( validateTemplate( $template ) as $problem ) {
		render_log( "Plantilla: $problem" );
	}
	return render_fill( $template, $data );
}

/* Recorre el texto: lo que está fuera de los bloques se rellena con render_marks;
   cada bloque se pinta con render_block (que vuelve a llamar a render_fill: recursión). */
function render_fill( $text, $data ) {
	$html = "";
	$pos  = 0;

	while ( ( $open = strpos( $text, "##", $pos ) ) !== false ) {
		$close = strpos( $text, "##", $open + 2 );
		if ( $close === false ) {
			break;
		}
		$name = substr( $text, $open + 2, $close - $open - 2 );

		if ( !render_isBlockName( $name ) ) {	// "##" que no abre un bloque
			$html .= render_marks( substr( $text, $pos, $open + 2 - $pos ), $data );
			$pos   = $open + 2;
			continue;
		}

		$endTag = "##/" . $name . "##";
		$end    = strpos( $text, $endTag, $close + 2 );
		if ( $end === false ) {	// sin cerrar: ya lo avisó validateTemplate; se deja como está
			$html .= render_marks( substr( $text, $pos, $open - $pos ), $data );
			$html .= substr( $text, $open, $close + 2 - $open );
			$pos   = $close + 2;
			continue;
		}

		$html .= render_marks( substr( $text, $pos, $open - $pos ), $data );	// lo de antes del bloque
		$inner = substr( $text, $close + 2, $end - $close - 2 );	// lo de dentro
		$html .= render_block( $name, $inner, $data, "render_fill" );
		$pos   = $end + strlen( $endTag );
	}

	return $html . render_marks( substr( $text, $pos ), $data );	// lo que queda al final
}

/* Cambia cada #nombre# del texto por su valor escapado. */
function render_marks( $text, $data ) {
	$html = "";
	$pos  = 0;

	while ( ( $start = strpos( $text, "#", $pos ) ) !== false ) {
		$end = strpos( $text, "#", $start + 1 );
		if ( $end === false ) {
			break;
		}
		$name = substr( $text, $start + 1, $end - $start - 1 );

		if ( render_isName( $name ) ) {	// es una marca
			$html .= substr( $text, $pos, $start - $pos ) . render_value( $name, $data );
			$pos   = $end + 1;
		} else {	// un # que no es de una marca (un color de CSS, un enlace #arriba…)
			$html .= substr( $text, $pos, $start + 1 - $pos );
			$pos   = $start + 1;
		}
	}

	return $html . substr( $text, $pos );
}


/* ============ VERSIÓN 2 · render_regex() con una expresión regular ============ */

function render_regex( $template, $data ) {
	foreach ( validateTemplate( $template ) as $problem ) {
		render_log( "Plantilla: $problem" );
	}
	return render_regex_fill( $template, $data );
}

/* Una sola expresión encuentra, de izquierda a derecha, o un bloque o una marca:
     ##(nombre)##(lo de dentro)##/\1##   → \1 obliga a que el cierre tenga el mismo nombre
     #(nombre)#                          → una marca
   La /s del final hace que el punto también case con los saltos de línea. */
function render_regex_fill( $text, $data ) {
	return preg_replace_callback(
		'/##([A-Za-z]\w*(?::empty)?)##(.*?)##\/\1##|#([A-Za-z]\w*)#/s',
		function ( $m ) use ( $data ) {
			if ( isset( $m[ 3 ] ) ) {	// ha casado la segunda parte: una #marca#
				return render_value( $m[ 3 ], $data );
			}
			return render_block( $m[ 1 ], $m[ 2 ], $data, "render_regex_fill" );
		},
		$text
	);
}


/* ============ LO COMÚN A LAS DOS VERSIONES ============ */

/* Pinta un bloque. $fill es el nombre de la función que rellena lo de dentro
   (render_fill o render_regex_fill): así las dos versiones comparten esta parte. */
function render_block( $name, $inner, $data, $fill ) {
	// ##nombre:empty## → sale con los datos de fuera, solo si la lista está vacía
	if ( substr( $name, -6 ) === ":empty" ) {
		$list = substr( $name, 0, -6 );
		if ( isset( $data[ $list ] ) && is_array( $data[ $list ] ) && count( $data[ $list ] ) === 0 ) {
			return $fill( $inner, $data );
		}
		return "";
	}

	// ##nombre## → una vez por cada fila de la lista
	if ( !isset( $data[ $name ] ) || !is_array( $data[ $name ] ) ) {
		render_log( "Plantilla: la plantilla pide el bloque ##$name## y los datos no lo traen" );
		return "";
	}
	$html = "";
	foreach ( $data[ $name ] as $row ) {
		$html .= $fill( $inner, $row );
	}
	return $html;
}

/* El valor de una marca, escapado. Si los datos no lo traen: hueco vacío y aviso en el log. */
function render_value( $name, $data ) {
	if ( !array_key_exists( $name, $data ) || is_array( $data[ $name ] ) ) {
		render_log( "Plantilla: la marca #$name# no tiene valor en los datos" );
		return "";
	}
	return htmlspecialchars( (string) $data[ $name ] );
}

/* ¿Es un nombre válido de marca? Empieza por letra; luego letras, números o _. */
function render_isName( $name ) {
	return $name !== "" && ctype_alpha( $name[ 0 ] ) && ctype_alnum( str_replace( "_", "", $name ) );
}

/* ¿Es un nombre válido de bloque? Un nombre, o un nombre seguido de :empty. */
function render_isBlockName( $name ) {
	if ( substr( $name, -6 ) === ":empty" ) {
		$name = substr( $name, 0, -6 );
	}
	return render_isName( $name );
}

/* Apunta un aviso en el log, una sola vez por petición. */
function render_log( $message ) {
	static $done = [];
	if ( !isset( $done[ $message ] ) ) {
		error_log( $message );
		$done[ $message ] = true;
	}
}
