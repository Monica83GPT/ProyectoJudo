<?php
/* v02 · La configuración de las consultas. Solo datos, sin código.
   Lo lee mod002, que es el único que conoce la base de datos.
   Tipos: int · float · bool · decimal (texto, sin perder céntimos) · string ·
          date (la fecha como texto) · dateObject (la fecha como objeto DateTimeImmutable).
   Si en la base de datos cambia el nombre de una columna, se toca aquí y nada más.
   v04 · Cada consulta tiene tres posiciones:
     "sql"    → la consulta;
     "fields" → las columnas que devuelve: la clave es su nombre en la base de datos,
                y el valor, su nombre en la aplicación y su tipo;
     "error"  → el mensaje para el usuario si esa consulta falla. */
const CONFIG_QUERIES = [
	"getPlayers" => [
		"sql"    => "SELECT `idjugador`, `nomjugador`, `altura`, `idposicion`
		               FROM `jugadores` ORDER BY `nomjugador`",
		"fields" => [
			// base de datos  => aplicación,  tipo
			"idjugador"      => [ "playerId",   "int"    ],
			"nomjugador"     => [ "playerName", "string" ],
			"altura"         => [ "height",     "int"    ],
			"idposicion"     => [ "positionId", "int"    ],
		],
		"error"  => "No se ha podido cargar el listado de jugadores.",
	],
	"getPlayerById" => [
		"sql"    => "SELECT `idjugador`, `nomjugador`, `fecnacimiento`, `altura`
		               FROM `jugadores` WHERE `idjugador` = :id",
		"fields" => [
			// base de datos  => aplicación,  tipo
			"idjugador"      => [ "playerId",   "int"    ],
			"nomjugador"     => [ "playerName", "string" ],
			"fecnacimiento"  => [ "birthDate",  "date"   ],
			"altura"         => [ "height",     "int"    ],
		],
		"error"  => "No se ha podido cargar la ficha del jugador.",
	],
	"getPositions" => [
		"sql"    => "SELECT `idposicion`, `nomposicion`
		               FROM `posiciones` ORDER BY `idposicion`",
		"fields" => [
			// base de datos  => aplicación,    tipo
			"idposicion"     => [ "positionId",   "int"    ],
			"nomposicion"    => [ "positionName", "string" ],
		],
		"error"  => "No se han podido cargar las posiciones.",
	],
	"getPlayersByPosition" => [
		"sql"    => "SELECT `idjugador`, `nomjugador`, `altura`, `idposicion`
		               FROM `jugadores` WHERE `idposicion` = :positionId
		              ORDER BY `nomjugador`",
		"fields" => [
			// base de datos  => aplicación,  tipo
			"idjugador"      => [ "playerId",   "int"    ],
			"nomjugador"     => [ "playerName", "string" ],
			"altura"         => [ "height",     "int"    ],
			"idposicion"     => [ "positionId", "int"    ],
		],
		"error"  => "No se han podido cargar los jugadores de esa posición.",
	],
];
