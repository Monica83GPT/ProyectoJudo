/*
  Elimina el campeonato de prueba 10 y todas sus referencias.
  Ejecutar sobre COMBATES2 cuando se quiera limpiar ese dato de prueba.
*/
START TRANSACTION;

SELECT idcampeonato, nomcampeonato
  FROM CAMPEONATOS
 WHERE idcampeonato = 10;

DELETE FROM AVANCES_COMBATE
 WHERE idcombate_origen IN (
           SELECT idcombate
             FROM COMBATES
            WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10)
       )
    OR idcombate_destino IN (
           SELECT idcombate
             FROM COMBATES
            WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10)
       );

DELETE FROM RESULTADOS_COMBATE
 WHERE idcombate IN (
           SELECT idcombate
             FROM COMBATES
            WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10)
       );

DELETE FROM COMBATES
 WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10);

DELETE FROM CLASIFICACIONES_OFICIALES
 WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10);

DELETE FROM PARTICIPANTES_CUADRO
 WHERE idcampeonato = 10;

DELETE FROM CUADROS
 WHERE idcampeonato = 10;

DELETE FROM INSCRITOS
 WHERE idcampeonato = 10;

DELETE FROM NIVELESCOMPETICION
 WHERE idcampeonato = 10;

DELETE FROM CONVOCATORIAS
 WHERE idcampeonato = 10;

DELETE FROM CAMPEONATOS
 WHERE idcampeonato = 10;

SELECT 'campeonato' AS referencia, COUNT(*) AS restantes
  FROM CAMPEONATOS
 WHERE idcampeonato = 10
UNION ALL
SELECT 'nivelescompeticion', COUNT(*)
  FROM NIVELESCOMPETICION
 WHERE idcampeonato = 10
UNION ALL
SELECT 'inscritos', COUNT(*)
  FROM INSCRITOS
 WHERE idcampeonato = 10
UNION ALL
SELECT 'convocatorias', COUNT(*)
  FROM CONVOCATORIAS
 WHERE idcampeonato = 10;

COMMIT;
