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
	"getChampionshipCatalog" => [
		"sql" => "SELECT c.idcampeonato, c.nomcampeonato, c.feccampeonato, c.logocampeonato, c.sede,
		                 cv.organismo_emisor, cv.plazo_inscripcion, cv.requisitos, cv.documentos,
		                 cv.reglamento_version, cv.circular_archivo, cv.normativa_archivo,
		                 COUNT(DISTINCT CONCAT(i.numfederado, '|', i.idsubcategoria)) AS inscritos,
		                 GROUP_CONCAT(DISTINCT CONCAT(cat.nomcategoria, ' · ', sc.nomrangopesaje)
		                              ORDER BY cat.nomcategoria, sc.pesajemin SEPARATOR ' · ') AS categorias
		            FROM CAMPEONATOS c
	       LEFT JOIN CONVOCATORIAS cv ON cv.idcampeonato = c.idcampeonato
	       LEFT JOIN NIVELESCOMPETICION nc ON nc.idcampeonato = c.idcampeonato
	       LEFT JOIN SUBCATEGORIAS sc ON sc.idsubcategoria = nc.idsubcategoria
	       LEFT JOIN CATEGORIAS cat ON cat.idcategoria = sc.idcategoria
	       LEFT JOIN INSCRITOS i ON i.idcampeonato = c.idcampeonato
	        GROUP BY c.idcampeonato, c.nomcampeonato, c.feccampeonato, c.logocampeonato, c.sede,
	                 cv.organismo_emisor, cv.plazo_inscripcion, cv.requisitos, cv.documentos,
	                 cv.reglamento_version, cv.circular_archivo, cv.normativa_archivo
	        ORDER BY (c.feccampeonato >= CURDATE()) DESC, c.feccampeonato ASC, c.idcampeonato ASC",
		"fields" => [
			"idcampeonato" => [ "championshipId", "int" ],
			"nomcampeonato" => [ "championshipName", "string" ],
			"feccampeonato" => [ "championshipDate", "dateObject" ],
			"logocampeonato" => [ "logoPath", "string" ],
			"sede" => [ "venue", "string" ],
			"organismo_emisor" => [ "issuer", "string" ],
			"plazo_inscripcion" => [ "registrationDate", "dateObject" ],
			"requisitos" => [ "requirements", "string" ],
			"documentos" => [ "documents", "string" ],
			"reglamento_version" => [ "regulation", "string" ],
			"circular_archivo" => [ "circularPath", "string" ],
			"normativa_archivo" => [ "regulationPath", "string" ],
			"inscritos" => [ "registeredCount", "int" ],
			"categorias" => [ "categoriesText", "string" ],
		],
		"error" => "No se ha podido cargar el catálogo de campeonatos.",
	],
	"getResultCategories" => [
		"sql" => "SELECT cu.idcuadro, c.idcampeonato, c.nomcampeonato, c.feccampeonato,
		                 c.sede, sc.idsubcategoria,
		                 CONCAT(cat.nomcategoria, ' · ', sc.nomrangopesaje) AS categoria,
		                 COALESCE((
		                   SELECT MIN(co.fuente_url)
		                     FROM CLASIFICACIONES_OFICIALES co
		                    WHERE co.idcuadro = cu.idcuadro
		                 ), '') AS fuente_resultados
		            FROM CUADROS cu
		            JOIN CAMPEONATOS c ON c.idcampeonato = cu.idcampeonato
		            JOIN SUBCATEGORIAS sc ON sc.idsubcategoria = cu.idsubcategoria
		            JOIN CATEGORIAS cat ON cat.idcategoria = sc.idcategoria
		           WHERE EXISTS (
		                 SELECT 1
		                   FROM COMBATES cb
		                   JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                  WHERE cb.idcuadro = cu.idcuadro
		                    AND UPPER(cb.ronda) = 'FINAL'
		           ) OR EXISTS (
		                 SELECT 1
		                   FROM CLASIFICACIONES_OFICIALES co
		                  WHERE co.idcuadro = cu.idcuadro
		           )
		        ORDER BY c.feccampeonato DESC, c.idcampeonato DESC,
		                 cat.nomcategoria, sc.pesajemin",
		"fields" => [
			"idcuadro" => [ "bracketId", "int" ],
			"idcampeonato" => [ "championshipId", "int" ],
			"nomcampeonato" => [ "championshipName", "string" ],
			"feccampeonato" => [ "championshipDate", "dateObject" ],
			"sede" => [ "venue", "string" ],
			"idsubcategoria" => [ "subcategoryId", "int" ],
			"categoria" => [ "categoryName", "string" ],
			"fuente_resultados" => [ "sourceUrl", "string" ],
		],
		"error" => "No se han podido cargar las categorías con resultados.",
	],
	"getBracketRanking" => [
		"sql" => "SELECT ranking.numfederado, ranking.judoka, ranking.club, ranking.puesto
		            FROM (
		            SELECT pc.numfederado, CONCAT(j.nombre, ' ', j.apellidos) AS judoka,
		                 COALESCE((
		                   SELECT cl.nomclub
		                     FROM PERTENECIENTES p
		                     JOIN CLUBS cl ON cl.idclub = p.idclub
		                    WHERE p.numfederado = pc.numfederado
		                      AND p.fechaaltaclub <= c.feccampeonato
		                 ORDER BY p.fechaaltaclub DESC, p.idclub DESC
		                    LIMIT 1
		                 ), 'Sin club asignado') AS club,
		                 CASE
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'FINAL'
		                        AND CASE rc.lado_ganador WHEN 'AZUL' THEN cb.numfederado_azul ELSE cb.numfederado_blanco END = pc.numfederado
		                   ) THEN 1
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'FINAL'
		                        AND pc.numfederado IN (cb.numfederado_azul, cb.numfederado_blanco)
		                   ) THEN 2
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'BRONCE'
		                        AND CASE rc.lado_ganador WHEN 'AZUL' THEN cb.numfederado_azul ELSE cb.numfederado_blanco END = pc.numfederado
		                   ) THEN 3
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'BRONCE'
		                        AND pc.numfederado IN (cb.numfederado_azul, cb.numfederado_blanco)
		                   ) THEN 5
		                   ELSE NULL
		                 END AS puesto
		            FROM PARTICIPANTES_CUADRO pc
		            JOIN JUDOKAS j ON j.numfederado = pc.numfederado
		            JOIN CUADROS cu ON cu.idcuadro = pc.idcuadro
		            JOIN CAMPEONATOS c ON c.idcampeonato = cu.idcampeonato
		           WHERE pc.idcuadro = :bracketId
		            UNION ALL
		            SELECT '' AS numfederado, co.nombre_completo AS judoka,
		                   co.club, co.posicion AS puesto
		              FROM CLASIFICACIONES_OFICIALES co
		             WHERE co.idcuadro = :officialBracketId
		            ) ranking
		        ORDER BY ranking.puesto IS NULL, ranking.puesto, ranking.judoka",
		"fields" => [
			"numfederado" => [ "federationNumber", "string" ],
			"judoka" => [ "fullName", "string" ],
			"club" => [ "clubName", "string" ],
			"puesto" => [ "position", "int" ],
		],
		"error" => "No se ha podido cargar el ranking de la categoría.",
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
	"getClubDashboard" => [
		"sql"    => "SELECT c.nomclub AS profile_name,
		                     COUNT(DISTINCT p.numfederado) AS primary_count,
		                     COUNT(DISTINCT CONCAT(i.numfederado, '-', i.idcampeonato, '-', i.idsubcategoria)) AS secondary_count,
		                     COUNT(DISTINCT CASE WHEN i.estado = 'VALIDADA' THEN i.numfederado END) AS tertiary_count
		                FROM USUARIOS_CLUB uc
		                JOIN CLUBS c ON c.idclub = uc.idclub
		           LEFT JOIN PERTENECIENTES p ON p.idclub = c.idclub
		                 AND p.idclub = (
		                     SELECT p2.idclub
		                       FROM PERTENECIENTES p2
		                      WHERE p2.numfederado = p.numfederado
		                   ORDER BY p2.fechaaltaclub DESC, p2.idclub DESC
		                      LIMIT 1
		                 )
		           LEFT JOIN INSCRITOS i ON i.numfederado = p.numfederado
		               WHERE uc.idusuario = :userId
		            GROUP BY c.idclub, c.nomclub",
		"fields" => [
			"profile_name"   => [ "profileName",   "string" ],
			"primary_count"  => [ "primaryCount",  "int"    ],
			"secondary_count"=> [ "secondaryCount","int"    ],
			"tertiary_count" => [ "tertiaryCount", "int"    ],
		],
		"error" => "No se han podido cargar los datos del club.",
	],
	"getClubJudokas" => [
		"sql" => "SELECT j.numfederado, j.nombre, j.apellidos, j.pesaje,
		                 c.nomclub,
		                 (SELECT CONCAT(cat.nomcategoria, ' · ', sc.nomrangopesaje)
		                    FROM CATEGORIAS cat
		                    JOIN SUBCATEGORIAS sc ON sc.idcategoria = cat.idcategoria
		                   WHERE cat.genero = j.genero
		                     AND TIMESTAMPDIFF(YEAR, j.fecnacimiento, CURDATE()) BETWEEN cat.edadmin AND cat.edadmax
		                     AND j.pesaje BETWEEN sc.pesajemin AND sc.pesajemax
		                ORDER BY cat.edadmin DESC, sc.pesajemin DESC
		                   LIMIT 1) AS categoria_actual
		            FROM USUARIOS_CLUB uc
		            JOIN CLUBS c ON c.idclub = uc.idclub
		            JOIN PERTENECIENTES p ON p.idclub = c.idclub
		            JOIN JUDOKAS j ON j.numfederado = p.numfederado
		           WHERE uc.idusuario = :userId
		             AND p.idclub = (
		                 SELECT p2.idclub
		                   FROM PERTENECIENTES p2
		                  WHERE p2.numfederado = p.numfederado
		               ORDER BY p2.fechaaltaclub DESC, p2.idclub DESC
		                  LIMIT 1
		             )
		             AND p.fechaaltaclub = (
		                 SELECT MAX(p3.fechaaltaclub)
		                   FROM PERTENECIENTES p3
		                  WHERE p3.numfederado = p.numfederado
		                    AND p3.idclub = p.idclub
		             )
		        ORDER BY j.apellidos, j.nombre",
		"fields" => [
			"numfederado" => [ "federationNumber", "string" ],
			"nombre" => [ "firstName", "string" ],
			"apellidos" => [ "lastName", "string" ],
			"pesaje" => [ "weight", "int" ],
			"nomclub" => [ "clubName", "string" ],
			"categoria_actual" => [ "categoryName", "string" ],
		],
		"error" => "No se han podido cargar los judokas del club.",
	],
	"getClubJudokaRecentResults" => [
		"sql" => "SELECT pc.numfederado, c.nomcampeonato, c.feccampeonato, c.sede,
		                 CONCAT(cat.nomcategoria, ' · ', sc.nomrangopesaje) AS categoria_competicion,
		                 CASE
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'FINAL'
		                        AND CASE rc.lado_ganador WHEN 'AZUL' THEN cb.numfederado_azul ELSE cb.numfederado_blanco END = pc.numfederado
		                   ) THEN 1
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'FINAL'
		                        AND pc.numfederado IN (cb.numfederado_azul, cb.numfederado_blanco)
		                   ) THEN 2
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'BRONCE'
		                        AND CASE rc.lado_ganador WHEN 'AZUL' THEN cb.numfederado_azul ELSE cb.numfederado_blanco END = pc.numfederado
		                   ) THEN 3
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'BRONCE'
		                        AND pc.numfederado IN (cb.numfederado_azul, cb.numfederado_blanco)
		                   ) THEN 5
		                   ELSE NULL
		                 END AS puesto
		            FROM USUARIOS_CLUB uc
		            JOIN PERTENECIENTES p ON p.idclub = uc.idclub
		            JOIN PARTICIPANTES_CUADRO pc ON pc.numfederado = p.numfederado
		            JOIN CUADROS cu ON cu.idcuadro = pc.idcuadro
		            JOIN CAMPEONATOS c ON c.idcampeonato = cu.idcampeonato
		            JOIN SUBCATEGORIAS sc ON sc.idsubcategoria = cu.idsubcategoria
		            JOIN CATEGORIAS cat ON cat.idcategoria = sc.idcategoria
		           WHERE uc.idusuario = :userId
		             AND p.idclub = (
		                 SELECT p2.idclub FROM PERTENECIENTES p2
		                  WHERE p2.numfederado = p.numfederado
		               ORDER BY p2.fechaaltaclub DESC, p2.idclub DESC LIMIT 1
		             )
		             AND p.fechaaltaclub = (
		                 SELECT MAX(p3.fechaaltaclub) FROM PERTENECIENTES p3
		                  WHERE p3.numfederado = p.numfederado
		                    AND p3.idclub = p.idclub
		             )
		             AND c.feccampeonato BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()
		        ORDER BY pc.numfederado, c.feccampeonato DESC, c.nomcampeonato",
		"fields" => [
			"numfederado" => [ "federationNumber", "string" ],
			"nomcampeonato" => [ "championshipName", "string" ],
			"feccampeonato" => [ "championshipDate", "dateObject" ],
			"sede" => [ "venue", "string" ],
			"categoria_competicion" => [ "competitionCategory", "string" ],
			"puesto" => [ "position", "int" ],
		],
		"error" => "No se han podido cargar los resultados recientes del club.",
	],
	"getFederatedDashboard" => [
		"sql"    => "SELECT CONCAT(j.nombre, ' ', j.apellidos) AS profile_name,
		                     (SELECT COUNT(*) FROM INSCRITOS i WHERE i.numfederado = j.numfederado) AS primary_count,
		                     (SELECT COUNT(*) FROM COMBATES cb WHERE cb.numfederado_azul = j.numfederado OR cb.numfederado_blanco = j.numfederado) AS secondary_count,
		                     j.pesaje AS tertiary_count
		                FROM USUARIOS_FEDERADOS uf
		                JOIN JUDOKAS j ON j.numfederado = uf.numfederado
		               WHERE uf.idusuario = :userId",
		"fields" => [
			"profile_name"   => [ "profileName",   "string" ],
			"primary_count"  => [ "primaryCount",  "int"    ],
			"secondary_count"=> [ "secondaryCount","int"    ],
			"tertiary_count" => [ "tertiaryCount", "int"    ],
		],
		"error" => "No se han podido cargar los datos del federado.",
	],
	"getFederatedProfile" => [
		"sql" => "SELECT j.numfederado, j.nombre, j.apellidos, j.fecnacimiento,
		                 TIMESTAMPDIFF(YEAR, j.fecnacimiento, CURDATE()) AS edad,
		                 CASE WHEN j.genero = 1 THEN 'Masculino' ELSE 'Femenino' END AS genero,
		                 j.pesaje, c.nomclub, p.fechaaltaclub
		            FROM USUARIOS_FEDERADOS uf
		            JOIN JUDOKAS j ON j.numfederado = uf.numfederado
		       LEFT JOIN PERTENECIENTES p
		              ON p.idclub = (
		                  SELECT p2.idclub
		                    FROM PERTENECIENTES p2
		                   WHERE p2.numfederado = j.numfederado
		                ORDER BY p2.fechaaltaclub DESC, p2.idclub DESC
		                   LIMIT 1
		              )
		             AND p.numfederado = j.numfederado
		             AND p.fechaaltaclub = (
		                  SELECT MAX(p3.fechaaltaclub)
		                    FROM PERTENECIENTES p3
		                   WHERE p3.numfederado = j.numfederado
		              )
		       LEFT JOIN CLUBS c ON c.idclub = p.idclub
		           WHERE uf.idusuario = :userId
		           LIMIT 1",
		"fields" => [
			"numfederado" => [ "federationNumber", "string" ],
			"nombre" => [ "firstName", "string" ],
			"apellidos" => [ "lastName", "string" ],
			"fecnacimiento" => [ "birthDate", "dateObject" ],
			"edad" => [ "age", "int" ],
			"genero" => [ "gender", "string" ],
			"pesaje" => [ "weight", "int" ],
			"nomclub" => [ "clubName", "string" ],
			"fechaaltaclub" => [ "clubSince", "dateObject" ],
		],
		"error" => "No se ha podido cargar la ficha del federado.",
	],
	"getFederatedChampionshipResults" => [
		"sql" => "SELECT c.nomcampeonato, c.feccampeonato, c.sede, sc.nomrangopesaje,
		                 CASE
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'FINAL'
		                        AND CASE rc.lado_ganador WHEN 'AZUL' THEN cb.numfederado_azul ELSE cb.numfederado_blanco END = pc.numfederado
		                   ) THEN 1
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'FINAL'
		                        AND pc.numfederado IN (cb.numfederado_azul, cb.numfederado_blanco)
		                   ) THEN 2
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'BRONCE'
		                        AND CASE rc.lado_ganador WHEN 'AZUL' THEN cb.numfederado_azul ELSE cb.numfederado_blanco END = pc.numfederado
		                   ) THEN 3
		                   WHEN EXISTS (
		                     SELECT 1 FROM COMBATES cb
		                     JOIN RESULTADOS_COMBATE rc ON rc.idcombate = cb.idcombate
		                      WHERE cb.idcuadro = pc.idcuadro AND UPPER(cb.ronda) = 'BRONCE'
		                        AND pc.numfederado IN (cb.numfederado_azul, cb.numfederado_blanco)
		                   ) THEN 5
		                   ELSE NULL
		                 END AS puesto
		            FROM USUARIOS_FEDERADOS uf
		            JOIN PARTICIPANTES_CUADRO pc ON pc.numfederado = uf.numfederado
		            JOIN CUADROS cu ON cu.idcuadro = pc.idcuadro
		            JOIN CAMPEONATOS c ON c.idcampeonato = cu.idcampeonato
		            JOIN SUBCATEGORIAS sc ON sc.idsubcategoria = cu.idsubcategoria
		           WHERE uf.idusuario = :userId
		        ORDER BY c.feccampeonato DESC, c.nomcampeonato",
		"fields" => [
			"nomcampeonato" => [ "championshipName", "string" ],
			"feccampeonato" => [ "championshipDate", "dateObject" ],
			"sede" => [ "venue", "string" ],
			"nomrangopesaje" => [ "subcategoryName", "string" ],
			"puesto" => [ "position", "int" ],
		],
		"error" => "No se ha podido cargar el historial de campeonatos.",
	],
	"getOrganizerDashboard" => [
		"sql"    => "SELECT o.nomorganizador AS profile_name,
		                     (SELECT COUNT(*) FROM CAMPEONATOS c WHERE c.feccampeonato >= CURDATE()) AS primary_count,
		                     (SELECT COUNT(*) FROM INSCRITOS) AS secondary_count,
		                     (SELECT COUNT(*) FROM COMBATES cb WHERE cb.estado IN ('PROGRAMADO', 'EN_CURSO')) AS tertiary_count
		                FROM USUARIOS_ORGANIZADORES uo
		                JOIN ORGANIZADORES o ON o.idorganizador = uo.idorganizador
		               WHERE uo.idusuario = :userId",
		"fields" => [
			"profile_name"   => [ "profileName",   "string" ],
			"primary_count"  => [ "primaryCount",  "int"    ],
			"secondary_count"=> [ "secondaryCount","int"    ],
			"tertiary_count" => [ "tertiaryCount", "int"    ],
		],
		"error" => "No se han podido cargar los datos del organizador.",
	],
	"getUpcomingChampionships" => [
		"sql"    => "SELECT c.idcampeonato, c.nomcampeonato, c.feccampeonato,
		                     c.sede, cv.plazo_inscripcion, cv.organismo_emisor,
		                     cv.requisitos
		                FROM CAMPEONATOS c
		           LEFT JOIN CONVOCATORIAS cv ON cv.idcampeonato = c.idcampeonato
		               WHERE c.feccampeonato >= CURDATE()
		            ORDER BY c.feccampeonato ASC",
		"fields" => [
			"idcampeonato"      => [ "championshipId",   "int"        ],
			"nomcampeonato"     => [ "championshipName", "string"     ],
			"feccampeonato"     => [ "championshipDate", "dateObject" ],
			"sede"               => [ "venue",            "string"     ],
			"plazo_inscripcion" => [ "registrationDate", "dateObject" ],
			"organismo_emisor"  => [ "issuer",           "string"     ],
			"requisitos"        => [ "requirements",     "string"     ],
		],
		"error" => "No se han podido cargar los próximos campeonatos.",
	],
	"getCompetitionDocuments" => [
		"sql"    => "SELECT c.nomcampeonato, cv.organismo_emisor, cv.requisitos,
		                     cv.documentos, cv.reglamento_version,
		                     cv.circular_archivo, cv.normativa_archivo
		                FROM CONVOCATORIAS cv
		                JOIN CAMPEONATOS c ON c.idcampeonato = cv.idcampeonato
		            ORDER BY c.feccampeonato DESC",
		"fields" => [
			"nomcampeonato"     => [ "championshipName", "string" ],
			"organismo_emisor"  => [ "issuer",           "string" ],
			"requisitos"        => [ "requirements",     "string" ],
			"documentos"        => [ "documents",        "string" ],
			"reglamento_version"=> [ "regulation",       "string" ],
			"circular_archivo"  => [ "circularPath",      "string" ],
			"normativa_archivo" => [ "regulationPath",    "string" ],
		],
		"error" => "No se han podido cargar los documentos y normativas.",
	],
	"createJudoka" => [
		"sql" => "INSERT INTO JUDOKAS
		                 (numfederado, nombre, apellidos, pesaje, fecnacimiento, genero, fotojudoka)
		          VALUES (:federationNumber, :firstName, :lastName, :weight, :birthDate, :gender, :photo)",
		"fields" => [],
		"error" => "No se ha podido crear el judoka. Comprueba que el número federativo no exista.",
	],
	"linkJudokaToClub" => [
		"sql" => "INSERT INTO PERTENECIENTES (idclub, numfederado, fechaaltaclub)
		          SELECT uc.idclub, :federationNumber, CURDATE()
		            FROM USUARIOS_CLUB uc
		           WHERE uc.idusuario = :userId",
		"fields" => [],
		"error" => "No se ha podido vincular el judoka con el club.",
	],
	"getClubEnrollmentOptions" => [
		"sql" => "SELECT DISTINCT
		                     CONCAT(j.numfederado, '|', c.idcampeonato, '|', sc.idsubcategoria) AS option_value,
		                     CONCAT(j.nombre, ' ', j.apellidos, ' · ', c.nomcampeonato, ' · ', sc.nomrangopesaje) AS option_label,
		                     j.numfederado, c.idcampeonato, sc.idsubcategoria, j.pesaje
		                FROM USUARIOS_CLUB uc
		                JOIN PERTENECIENTES p ON p.idclub = uc.idclub
		                JOIN JUDOKAS j ON j.numfederado = p.numfederado
		                JOIN NIVELESCOMPETICION nc
		                JOIN CAMPEONATOS c ON c.idcampeonato = nc.idcampeonato
		                JOIN SUBCATEGORIAS sc ON sc.idsubcategoria = nc.idsubcategoria
		                JOIN CATEGORIAS cat ON cat.idcategoria = sc.idcategoria
		               WHERE uc.idusuario = :userId
		                 AND c.feccampeonato >= CURDATE()
		                 AND cat.genero = j.genero
		                 AND TIMESTAMPDIFF(YEAR, j.fecnacimiento, c.feccampeonato) BETWEEN cat.edadmin AND cat.edadmax
		                 AND j.pesaje BETWEEN sc.pesajemin AND sc.pesajemax
		                 AND NOT EXISTS (
		                     SELECT 1 FROM INSCRITOS i
		                      WHERE i.numfederado = j.numfederado
		                        AND i.idcampeonato = c.idcampeonato
		                 )
		            ORDER BY c.feccampeonato, j.apellidos",
		"fields" => [
			"option_value"   => [ "optionValue",      "string" ],
			"option_label"   => [ "optionLabel",      "string" ],
			"numfederado"    => [ "federationNumber", "string" ],
			"idcampeonato"   => [ "championshipId",   "int"    ],
			"idsubcategoria" => [ "subcategoryId",    "int"    ],
			"pesaje"          => [ "weight",           "int"    ],
		],
		"error" => "No se han podido cargar las inscripciones disponibles para el club.",
	],
	"getFederatedEnrollmentOptions" => [
		"sql" => "SELECT CONCAT(j.numfederado, '|', c.idcampeonato, '|', sc.idsubcategoria) AS option_value,
		                 CONCAT(c.nomcampeonato, ' · ', sc.nomrangopesaje, ' · ', c.sede) AS option_label,
		                 j.numfederado, CONCAT(j.nombre, ' ', j.apellidos) AS judoka,
		                 c.idcampeonato, c.nomcampeonato, c.feccampeonato, c.sede,
		                 sc.idsubcategoria,
		                 CONCAT(cat.nomcategoria, ' · ', sc.nomrangopesaje) AS categoria,
		                 j.pesaje, cl.nomclub, cl.emailcontacto
		            FROM USUARIOS_FEDERADOS uf
		            JOIN JUDOKAS j ON j.numfederado = uf.numfederado
		            JOIN CLUBS cl ON cl.idclub = (
		                 SELECT p.idclub
		                   FROM PERTENECIENTES p
		                  WHERE p.numfederado = j.numfederado
		               ORDER BY p.fechaaltaclub DESC, p.idclub DESC
		                  LIMIT 1
		            )
		            JOIN NIVELESCOMPETICION nc
		            JOIN CAMPEONATOS c ON c.idcampeonato = nc.idcampeonato
		            JOIN SUBCATEGORIAS sc ON sc.idsubcategoria = nc.idsubcategoria
		            JOIN CATEGORIAS cat ON cat.idcategoria = sc.idcategoria
		           WHERE uf.idusuario = :userId
		             AND c.feccampeonato >= CURDATE()
		             AND cat.genero = j.genero
		             AND TIMESTAMPDIFF(YEAR, j.fecnacimiento, c.feccampeonato) BETWEEN cat.edadmin AND cat.edadmax
		             AND j.pesaje BETWEEN sc.pesajemin AND sc.pesajemax
		             AND NOT EXISTS (
		                 SELECT 1 FROM INSCRITOS i
		                  WHERE i.numfederado = j.numfederado
		                    AND i.idcampeonato = c.idcampeonato
		             )
		        ORDER BY c.feccampeonato",
		"fields" => [
			"option_value"   => [ "optionValue",      "string" ],
			"option_label"   => [ "optionLabel",      "string" ],
			"numfederado"    => [ "federationNumber", "string" ],
			"judoka"          => [ "fullName",         "string" ],
			"idcampeonato"   => [ "championshipId",   "int"    ],
			"nomcampeonato"  => [ "championshipName", "string" ],
			"feccampeonato"  => [ "championshipDate", "dateObject" ],
			"sede"            => [ "venue",            "string" ],
			"idsubcategoria" => [ "subcategoryId",    "int"    ],
			"categoria"       => [ "categoryName",     "string" ],
			"pesaje"          => [ "weight",           "int"    ],
			"nomclub"         => [ "clubName",         "string" ],
			"emailcontacto"   => [ "clubEmail",        "string" ],
		],
		"error" => "No se han podido cargar las solicitudes disponibles para tu club.",
	],
	"createEnrollment" => [
		"sql" => "INSERT INTO INSCRITOS
		                 (numfederado, idcampeonato, idsubcategoria, fechainscripcion, estado, pesajeevento)
		          VALUES (:federationNumber, :championshipId, :subcategoryId, CURDATE(), 'ENVIADA', :weight)",
		"fields" => [],
		"error" => "No se ha podido registrar la inscripción.",
	],
	"getSubcategories" => [
		"sql" => "SELECT sc.idsubcategoria,
		                 CONCAT(cat.nomcategoria, ' · ', sc.nomrangopesaje) AS subcategory_name
		            FROM SUBCATEGORIAS sc
		            JOIN CATEGORIAS cat ON cat.idcategoria = sc.idcategoria
		        ORDER BY cat.nomcategoria, sc.pesajemin",
		"fields" => [
			"idsubcategoria"  => [ "subcategoryId",   "int"    ],
			"subcategory_name"=> [ "subcategoryName", "string" ],
		],
		"error" => "No se han podido cargar las categorías de competición.",
	],
	"getOrganizerCalls" => [
		"sql" => "SELECT cv.idconvocatoria, c.nomcampeonato, c.feccampeonato,
		                 cv.circular_archivo, cv.normativa_archivo,
		                 CONCAT(c.nomcampeonato, ' · ', DATE_FORMAT(c.feccampeonato, '%d/%m/%Y')) AS option_label
		            FROM CONVOCATORIAS cv
		            JOIN CAMPEONATOS c ON c.idcampeonato = cv.idcampeonato
		        ORDER BY c.feccampeonato DESC, cv.idconvocatoria DESC",
		"fields" => [
			"idconvocatoria" => [ "callId", "int" ],
			"nomcampeonato" => [ "championshipName", "string" ],
			"feccampeonato" => [ "championshipDate", "dateObject" ],
			"circular_archivo" => [ "circularPath", "string" ],
			"normativa_archivo" => [ "regulationPath", "string" ],
			"option_label" => [ "optionLabel", "string" ],
		],
		"error" => "No se han podido cargar las convocatorias.",
	],
	"updateCallDocuments" => [
		"sql" => "UPDATE CONVOCATORIAS
		             SET circular_archivo = COALESCE(:circularPath, circular_archivo),
		                 normativa_archivo = COALESCE(:regulationPath, normativa_archivo)
		           WHERE idconvocatoria = :callId",
		"fields" => [],
		"error" => "No se ha podido actualizar la documentación de la convocatoria.",
	],
	"createChampionship" => [
		"sql" => "INSERT INTO CAMPEONATOS
		                 (nomcampeonato, feccampeonato, logocampeonato, sede)
		          VALUES (:name, :eventDate, :logo, :venue)",
		"fields" => [],
		"error" => "No se ha podido crear el campeonato.",
	],
	"createCompetitionLevel" => [
		"sql" => "INSERT INTO NIVELESCOMPETICION (idcampeonato, idsubcategoria)
		          VALUES (:championshipId, :subcategoryId)",
		"fields" => [],
		"error" => "No se ha podido asignar la categoría al campeonato.",
	],
	"createCall" => [
		"sql" => "INSERT INTO CONVOCATORIAS
			                 (idcampeonato, organismo_emisor, plazo_inscripcion, requisitos,
			                  documentos, reglamento_version, fecha_corte_edad,
			                  circular_archivo, normativa_archivo)
		          VALUES (:championshipId, :issuer, :deadline, :requirements,
			                  :documents, :regulation, :ageCutoff,
			                  :circularPath, :regulationPath)",
		"fields" => [],
		"error" => "No se ha podido crear la convocatoria.",
	],
];
