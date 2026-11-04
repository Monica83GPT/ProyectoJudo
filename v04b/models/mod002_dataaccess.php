<?php
require "mod001_kernel.php";
require __DIR__ . "/../config/queries.php";	// __DIR__ = la carpeta de este fichero (models)

/* v02 · Las consultas ya no van cada una en su función: viven TODAS en config/queries.php,
   cada una con su SQL y con las columnas que devuelve. Este fichero solo sabe lanzarlas.
   Lo que venga de fuera entra siempre como :parámetro, nunca pegado al SQL. */

/* Lanza la consulta que se llama $key y devuelve sus filas, ya traducidas.
   Si no hay filas devuelve un array vacío, nunca null.
   v04 · Si la consulta falla, el detalle técnico va al log y se lanza una RuntimeException
   con el mensaje para el usuario que esa consulta tiene en config/queries.php. */
function mod002_runQuery( $key, $params = [] ) {
	if ( !isset( CONFIG_QUERIES[ $key ] ) ) {
		throw new InvalidArgumentException( "No existe la consulta '$key'" );
	}
	$query = CONFIG_QUERIES[ $key ];

	try {
		$st = mod001_connectDB()->prepare( $query[ "sql" ] );
		$st->execute( $params );
		$rows = $st->fetchAll();
	} catch ( PDOException $e ) {
		error_log( "Consulta '$key': " . $e->getMessage() );	// el detalle, para el programador
		throw new RuntimeException( $query[ "error" ] );	// el mensaje, para el usuario
	}

	$arReturn = [];
	foreach ( $rows as $row ) {
		$arReturn[] = mod002_translateRow( $row, $key );
	}

	return $arReturn;
}

/* Traduce una fila con los "fields" de su consulta: cada columna pasa a llamarse como en la
   aplicación y se convierte a su tipo. Dos errores de consistencia van al log (una sola vez):
   - una columna que devuelve el SQL y no está en "fields": sigue con su nombre de la BD;
   - una columna que está en "fields" y el SQL no devuelve: llega como null.
   v04 · "fields" es asociativo (la clave es la columna de la BD): se busca con isset, sin bucle. */
function mod002_translateRow( $row, $key ) {
	static $reported = [];	// cada inconsistencia se apunta una sola vez, no una por fila
	$fields   = CONFIG_QUERIES[ $key ][ "fields" ];
	$arReturn = [];

	foreach ( $row as $column => $value ) {
		if ( !isset( $fields[ $column ] ) ) {
			if ( !isset( $reported[ "$key.$column" ] ) ) {
				error_log( "Inconsistencia: la consulta '$key' devuelve la columna '$column' y no está en sus fields" );
				$reported[ "$key.$column" ] = true;
			}
			$arReturn[ $column ] = $value;
			continue;
		}

		$name = $fields[ $column ][ 0 ];	// nombre en la aplicación
		$type = $fields[ $column ][ 1 ];	// tipo
		$arReturn[ $name ] = mod002_castValue( $value, $type, "la columna '$column' de '$key'" );
	}

	foreach ( $fields as $column => $field ) {
		if ( !array_key_exists( $column, $row ) ) {
			if ( !isset( $reported[ "$key.$column" ] ) ) {
				error_log( "Inconsistencia: la consulta '$key' tiene en sus fields la columna '$column' y el SQL no la devuelve" );
				$reported[ "$key.$column" ] = true;
			}
			$arReturn[ $field[ 0 ] ] = null;
		}
	}

	return $arReturn;
}

/* Convierte un valor al tipo que dice "fields":
     int · float · bool · decimal · string · date · dateObject
   NULL se queda en null. Un tipo que no existe, o una fecha que no lo es, es un error
   de consistencia: al log, una sola vez. */
function mod002_castValue( $value, $type, $where ) {
	static $reported = [];	// cada aviso, una sola vez

	if ( $value === null ) {
		return null;
	}

	switch ( $type ) {
		case "int":        return (int) $value;
		case "float":      return (float) $value;	// FLOAT y DOUBLE: un valor aproximado
		case "bool":       return (bool) $value;	// BOOLEAN llega como 1 / 0
		case "decimal":    return (string) $value;	// DECIMAL en texto: sin perder céntimos
		case "string":     return (string) $value;
		case "date":       return (string) $value;	// la fecha como texto: '1995-02-19'
		case "dateObject":	// la fecha como objeto de PHP: ->format( "d/m/Y" ), ->diff()…
			try {
				return new DateTimeImmutable( $value );
			} catch ( Exception $e ) {
				if ( !isset( $reported[ "$where.fecha" ] ) ) {
					error_log( "Inconsistencia: $where no es una fecha válida: '$value'" );
					$reported[ "$where.fecha" ] = true;
				}
				return null;
			}
		default:
			if ( !isset( $reported[ "$where.tipo" ] ) ) {
				error_log( "Inconsistencia: $where tiene un tipo que no existe: '$type'" );
				$reported[ "$where.tipo" ] = true;
			}
			return $value;
	}
}
