<?php
/* Consultas de la portada. Esta capa solo contiene configuración de datos. */
const CONFIG_QUERIES = [
	"getChampionships" => [
		"sql"    => "SELECT c.idcampeonato, c.nomcampeonato, c.feccampeonato,
		                     c.sede, cv.plazo_inscripcion
		                FROM CAMPEONATOS c
		           LEFT JOIN CONVOCATORIAS cv ON cv.idcampeonato = c.idcampeonato
		            ORDER BY (c.feccampeonato >= CURDATE()) DESC,
		                     c.feccampeonato ASC",
		"fields" => [
			"idcampeonato"      => [ "championshipId",   "int"        ],
			"nomcampeonato"     => [ "championshipName", "string"     ],
			"feccampeonato"     => [ "championshipDate", "dateObject" ],
			"sede"               => [ "venue",            "string"     ],
			"plazo_inscripcion" => [ "registrationDate", "dateObject" ],
		],
		"error"  => "No se han podido cargar los campeonatos.",
	],
	"getFederationStats" => [
		"sql"    => "SELECT (SELECT COUNT(*) FROM JUDOKAS) AS total_judokas,
		                     (SELECT COUNT(*) FROM CLUBS) AS total_clubs",
		"fields" => [
			"total_judokas" => [ "judokaCount", "int" ],
			"total_clubs"   => [ "clubCount",   "int" ],
		],
		"error"  => "No se han podido cargar los datos federativos.",
	],
	"getAccessUser" => [
		"sql"    => "SELECT u.idusuario, u.nombre_usuario, u.password_hash,
		                     u.rol, u.activo,
		                     CASE u.rol
		                         WHEN 'CLUB' THEN c.nomclub
		                         WHEN 'FEDERADO' THEN CONCAT(j.nombre, ' ', j.apellidos)
		                         WHEN 'ORGANIZADOR' THEN o.nomorganizador
		                     END AS nombre_perfil
		                FROM USUARIOS u
		           LEFT JOIN USUARIOS_CLUB uc ON uc.idusuario = u.idusuario
		           LEFT JOIN CLUBS c ON c.idclub = uc.idclub
		           LEFT JOIN USUARIOS_FEDERADOS uf ON uf.idusuario = u.idusuario
		           LEFT JOIN JUDOKAS j ON j.numfederado = uf.numfederado
		           LEFT JOIN USUARIOS_ORGANIZADORES uo ON uo.idusuario = u.idusuario
		           LEFT JOIN ORGANIZADORES o ON o.idorganizador = uo.idorganizador
		               WHERE u.nombre_usuario = :username
		               LIMIT 1",
		"fields" => [
			"idusuario"      => [ "userId",      "int"    ],
			"nombre_usuario" => [ "username",    "string" ],
			"password_hash"  => [ "passwordHash","string" ],
			"rol"            => [ "role",        "string" ],
			"activo"         => [ "active",      "bool"   ],
			"nombre_perfil"  => [ "displayName", "string" ],
		],
		"error"  => "No se ha podido comprobar el acceso.",
	],
	"touchUserAccess" => [
		"sql"    => "UPDATE USUARIOS
		                  SET ultimo_acceso = CURRENT_TIMESTAMP
		                WHERE idusuario = :userId",
		"fields" => [],
		"error"  => "No se ha podido registrar el acceso.",
	],
];
