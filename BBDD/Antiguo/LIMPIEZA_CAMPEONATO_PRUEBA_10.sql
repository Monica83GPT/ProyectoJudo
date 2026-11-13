-- Limpieza del campeonato creado únicamente para probar el alta.
-- Ámbito exacto: idcampeonato = 10.
START TRANSACTION;

DELETE FROM AVANCES_COMBATE
 WHERE idcombate_origen IN (
     SELECT idcombate FROM COMBATES
      WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10)
 )
    OR idcombate_destino IN (
     SELECT idcombate FROM COMBATES
      WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10)
 );

DELETE FROM RESULTADOS_COMBATE
 WHERE idcombate IN (
     SELECT idcombate FROM COMBATES
      WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10)
 );

DELETE FROM COMBATES
 WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10);

DELETE FROM CLASIFICACIONES_OFICIALES
 WHERE idcuadro IN (SELECT idcuadro FROM CUADROS WHERE idcampeonato = 10);

DELETE FROM PARTICIPANTES_CUADRO WHERE idcampeonato = 10;
DELETE FROM CUADROS WHERE idcampeonato = 10;
DELETE FROM INSCRITOS WHERE idcampeonato = 10;
DELETE FROM NIVELESCOMPETICION WHERE idcampeonato = 10;
DELETE FROM CONVOCATORIAS WHERE idcampeonato = 10;
DELETE FROM CAMPEONATOS WHERE idcampeonato = 10;

COMMIT;
