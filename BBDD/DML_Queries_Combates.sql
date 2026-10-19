USE Combates;
-- Bloque 1 Consulta 1

-- Quiero el nombre y la fecha de nacimiento de todos los judokas que este año cumplan entre 12 y 14 años.
SELECT nombre, fecnacimiento, YEAR(CURDATE()) - YEAR(fecnacimiento) AS Edad
FROM JUDOKAS
WHERE YEAR(fecnacimiento) BETWEEN (YEAR(CURDATE()) - 14) 
AND (YEAR(CURDATE()) - 12);

-- Bloque 1 Consulta 2
-- Quiero el idzona , y la suma de los municipios que hay en cada zona, 
-- ordenados por cantidad de municipios, en orden descendente.


SELECT idzona, COUNT( idmunicipio )  AS numMunicipiosporzona
FROM MUNICIPIOS
GROUP BY idzona
ORDER BY numMunicipiosporzona DESC;

-- Bloque 1 Consulta 3
-- quiero los clubs que contengan "JUDO" o "CLUB" en el nombre del club, 
-- agrupados por el municipio al que pertenecen cuando el idmunicipio sea menor que 8.

SELECT nomclub, idmunicipio, COUNT(idmunicipio)  as NumMunicipios
FROM CLUBS
WHERE nomclub LIKE "%JUDO%"
OR nomclub LIKE "%CLUB%"
GROUP BY idmunicipio
HAVING idmunicipio < 8;


-- Bloque 2 Consulta 1
-- Quiero saber el nombre de la zona de todos los clubs, aunque no tengan judokas,
-- y los judokas (numeros de federados) si los tienen, y su nombre y apellidos.
-- ordenados por el nombre del municipio.

SELECT nomclub, JUDOKAS.numfederado, nomzona, CLUBS.idclub, nommunicipio, nombre, apellidos
FROM PERTENECIENTES
INNER JOIN JUDOKAS
ON PERTENECIENTES.numfederado = JUDOKAS.numfederado
RIGHT JOIN CLUBS
ON CLUBS.idclub = PERTENECIENTES.idclub
INNER JOIN MUNICIPIOS
ON MUNICIPIOS.idmunicipio = CLUBS.idmunicipio
INNER JOIN ZONAS
ON MUNICIPIOS.idzona = ZONAS.idzona
ORDER BY nommunicipio;


-- Bloque 2 Consulta 2
-- Quiero saber,  la fecha, la subcategoria, y el resultado de los combates del campeonato Madrileño 2026,
-- con el nombre y apellidos del ganador.
-- Ordenado por la subcategoria descendiente.

SELECT nomcampeonato, fechacombate, nomrangopesaje, nombre AS NomGanador, apellidos AS apellidosGanador,
CASE 
WHEN resultado = 0 THEN 'Ganador Azul' 
WHEN resultado = 1 THEN 'Ganador Blanco' 
END AS Ganador 
FROM CAMPEONATOS 
INNER JOIN COMBATES 
ON CAMPEONATOS.idcampeonato = COMBATES.idcampeonato 
INNER JOIN SUBCATEGORIAS 
ON COMBATES.idsubcategoria = SUBCATEGORIAS.idsubcategoria 
INNER JOIN JUDOKAS 
    ON JUDOKAS.numfederado = IF(
        COMBATES.resultado = 0,
        COMBATES.numfederadoazul,
        COMBATES.numfederadoblanco
    )
 WHERE nomcampeonato = 'Madrileño 2026' 
 ORDER BY nomrangopesaje DESC;

-- Bloque 2 Consulta 3
-- Quiero saber cuantos judokas se han inscrito en cada campeonato,
-- cuantos son chicas y que porcentaje representan, con el simbolo "%" y un decimal.

SELECT nomcampeonato, COUNT(INSCRITOS.numfederado) AS NumInscriCampeonato,
 SUM( CASE WHEN genero =  '0' THEN 1 ELSE 0 END ) AS NumFemeninas,
     
 CONCAT(
     ROUND (
            SUM(CASE WHEN genero  =  '0' THEN 1 ELSE 0 END)
            * 100 / COUNT(INSCRITOS.numfederado),
            1),
    "%") AS PorcentajeFemenino
FROM INSCRITOS
INNER JOIN JUDOKAS
ON JUDOKAS.numfederado = INSCRITOS.numfederado
INNER JOIN CAMPEONATOS
ON CAMPEONATOS.idcampeonato = INSCRITOS.idcampeonato
GROUP by CAMPEONATOS.idcampeonato;

-- Bloque 2 Consulta 4
-- Quiero listar los judokas que se han inscrito, con su fecha de inscripcion y
-- no han combatido

SELECT nombre, apellidos, fechainscripcion, INSCRITOS.numfederado
FROM INSCRITOS
LEFT JOIN COMBATES
ON INSCRITOS.numfederado = COMBATES.numfederadoblanco
OR INSCRITOS.numfederado = COMBATES.numfederadoazul
INNER JOIN JUDOKAS
ON JUDOKAS.numfederado=INSCRITOS.numfederado
WHERE COMBATES.fechacombate is NULL;


-- Bloque 3 Consulta 1
-- Quiero saber el nombre de los clubs Que tienen algun judoka ganador
-- perteneciendo el judoka a ese club en el momento del combate,
-- y el numero de combates ganados de todos sus judokas de cada campeonato.

SELECT nomclub, COUNT(PERTENECIENTES.idclub) AS NumCombatesGanados
FROM CAMPEONATOS 
INNER JOIN COMBATES 
ON CAMPEONATOS.idcampeonato = COMBATES.idcampeonato 
INNER JOIN PERTENECIENTES
ON PERTENECIENTES.numfederado =  IF(
        COMBATES.resultado = 0,
        COMBATES.numfederadoazul,
        COMBATES.numfederadoblanco
    )
INNER JOIN CLUBS
ON PERTENECIENTES.idclub = CLUBS.idclub
 
WHERE PERTENECIENTES.fechaaltaclub = (
    SELECT MAX(fechaaltaclub)
    FROM PERTENECIENTES AS P1
    WHERE P1.numfederado = PERTENECIENTES.numfederado
   AND p1.fechaaltaclub <= COMBATES.fechacombate
)
GROUP BY CLUBS.idclub DESC;

-- Bloque 3 Consulta 2
-- quiero los inscritos por subcategoria y zona del judoka 
-- segun su club en el momento de la fecha de campeonato al que se inscribe

SELECT INSCRITOS.numfederado, INSCRITOS.idsubcategoria, ZONAS.idzona
FROM INSCRITOS
INNER JOIN JUDOKAS
ON JUDOKAS.numfederado = INSCRITOS.numfederado
INNER JOIN CAMPEONATOS
ON CAMPEONATOS.idcampeonato = INSCRITOS.idcampeonato
INNER JOIN PERTENECIENTES
ON INSCRITOS.numfederado = PERTENECIENTES.numfederado
INNER JOIN CLUBS
ON PERTENECIENTES.idclub = CLUBS.idclub

INNER JOIN MUNICIPIOS
ON MUNICIPIOS.idmunicipio = CLUBS.idmunicipio
INNER JOIN ZONAS
ON MUNICIPIOS.idzona = ZONAS.idzona

WHERE PERTENECIENTES.fechaaltaclub = (
    SELECT MAX(fechaaltaclub)
    FROM PERTENECIENTES AS P1
    WHERE P1.numfederado = PERTENECIENTES.numfederado
   AND p1.fechaaltaclub <= (SELECT (CAMPEONATOS.feccampeonato)
                            WHERE CAMPEONATOS.idcampeonato = INSCRITOS.idcampeonato)
);

-- Bloque 3 Consulta 3
-- quiero saber cuanto combates han perdido en todos los campeonatos 
-- los judoKas que ha ganado combate en la ultima ronda


SELECT j1.nombre, j1.apellidos,  j1.numfederado, COUNT(j1.numfederado) AS CombatesPerdidos
FROM COMBATES AS c3
INNER JOIN JUDOKAS AS j1
ON j1.numfederado =  IF(
        c3.resultado = 1,
        c3.numfederadoazul,
       c3.numfederadoblanco)
       
WHERE j1.numfederado IN (
    SELECT j2.numfederado--  nomrangopesaje, nomcampeonato
    FROM JUDOKAS AS j2
    INNER JOIN COMBATES AS c1
    ON j2.numfederado =  IF(
        c1.resultado = 0,
        c1.numfederadoazul,
       c1.numfederadoblanco)
       
    AND c1.fechacombate = (
    SELECT MAX(fechacombate)
 	FROM COMBATES AS c2
	WHERE c2.numfederadoazul = c1.numfederadoazul
 	AND  c2.numfederadoblanco = c1.numfederadoblanco)
    )
    GROUP BY j1.numfederado;

-- NO EVALUAR

-- agrupar por categorias a los judokas segun su edad y genero,
-- y por subcategoria segun su categoria y su peso.
-- mostrar nomrangopesaje,nombre, apellidos, numfederado, edad genero y pesaje del judoka

SELECT nombre, apellidos, numfederado, (YEAR(CURDATE()) - YEAR(fecnacimiento)) AS EdadJudoka,
      nomrangopesaje, JUDOKAS.genero,JUDOKAS.pesaje
FROM JUDOKAS
INNER JOIN CATEGORIAS 
 ON CATEGORIAS.edadmax >= (YEAR(CURDATE()) - YEAR(fecnacimiento))
 AND CATEGORIAS.edadmin <= (YEAR(CURDATE()) - YEAR(fecnacimiento))
AND CATEGORIAS.genero = JUDOKAS.genero
INNER JOIN SUBCATEGORIAS
ON CATEGORIAS.idcategoria = SUBCATEGORIAS.idcategoria
AND JUDOKAS.pesaje BETWEEN SUBCATEGORIAS.pesajemin AND SUBCATEGORIAS.pesajemax;