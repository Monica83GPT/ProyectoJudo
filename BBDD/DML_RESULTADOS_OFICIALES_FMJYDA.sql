USE COMBATES2;

START TRANSACTION;

-- Datos transcritos de las actas oficiales publicadas por la FMJYDA.
-- No se generan números federativos, fechas de nacimiento ni combates ficticios.

INSERT INTO CATEGORIAS (nomcategoria, genero, edadmax, edadmin)
SELECT 'Junior Masculino', TRUE, 20, 15
WHERE NOT EXISTS (
    SELECT 1 FROM CATEGORIAS
    WHERE nomcategoria = 'Junior Masculino' AND genero = TRUE
);

SET @junior_masculino = (
    SELECT idcategoria FROM CATEGORIAS
    WHERE nomcategoria = 'Junior Masculino' AND genero = TRUE
    ORDER BY idcategoria LIMIT 1
);
SET @senior_masculino = (
    SELECT idcategoria FROM CATEGORIAS
    WHERE nomcategoria = 'Senior Masculino' AND genero = TRUE
    ORDER BY idcategoria LIMIT 1
);

INSERT INTO SUBCATEGORIAS (nomrangopesaje, pesajemin, pesajemax, idcategoria)
SELECT '-60 kg', 1, 60, @junior_masculino
WHERE NOT EXISTS (
    SELECT 1 FROM SUBCATEGORIAS
    WHERE nomrangopesaje = '-60 kg' AND idcategoria = @junior_masculino
);
INSERT INTO SUBCATEGORIAS (nomrangopesaje, pesajemin, pesajemax, idcategoria)
SELECT '-66 kg', 61, 66, @junior_masculino
WHERE NOT EXISTS (
    SELECT 1 FROM SUBCATEGORIAS
    WHERE nomrangopesaje = '-66 kg' AND idcategoria = @junior_masculino
);
INSERT INTO SUBCATEGORIAS (nomrangopesaje, pesajemin, pesajemax, idcategoria)
SELECT '-60 kg', 1, 60, @senior_masculino
WHERE NOT EXISTS (
    SELECT 1 FROM SUBCATEGORIAS
    WHERE nomrangopesaje = '-60 kg' AND idcategoria = @senior_masculino
);
INSERT INTO SUBCATEGORIAS (nomrangopesaje, pesajemin, pesajemax, idcategoria)
SELECT '-66 kg', 61, 66, @senior_masculino
WHERE NOT EXISTS (
    SELECT 1 FROM SUBCATEGORIAS
    WHERE nomrangopesaje = '-66 kg' AND idcategoria = @senior_masculino
);

INSERT INTO CAMPEONATOS (nomcampeonato, feccampeonato, logocampeonato, sede)
SELECT 'Sector Junior 2025', '2025-02-22', 'imagenes/SINFOTO.svg',
       'Pabellón Francisco Valcárcel, Villaviciosa de Odón'
WHERE NOT EXISTS (
    SELECT 1 FROM CAMPEONATOS
    WHERE nomcampeonato = 'Sector Junior 2025' AND feccampeonato = '2025-02-22'
);
INSERT INTO CAMPEONATOS (nomcampeonato, feccampeonato, logocampeonato, sede)
SELECT 'Sector Absoluto 2024', '2024-11-16', 'imagenes/SINFOTO.svg',
       'Pabellón Francisco Valcárcel, Villaviciosa de Odón'
WHERE NOT EXISTS (
    SELECT 1 FROM CAMPEONATOS
    WHERE nomcampeonato = 'Sector Absoluto 2024' AND feccampeonato = '2024-11-16'
);

SET @sector_junior_2025 = (
    SELECT idcampeonato FROM CAMPEONATOS
    WHERE nomcampeonato = 'Sector Junior 2025' AND feccampeonato = '2025-02-22'
    ORDER BY idcampeonato LIMIT 1
);
SET @sector_absoluto_2024 = (
    SELECT idcampeonato FROM CAMPEONATOS
    WHERE nomcampeonato = 'Sector Absoluto 2024' AND feccampeonato = '2024-11-16'
    ORDER BY idcampeonato LIMIT 1
);
SET @junior_60 = (
    SELECT idsubcategoria FROM SUBCATEGORIAS
    WHERE nomrangopesaje = '-60 kg' AND idcategoria = @junior_masculino
    ORDER BY idsubcategoria LIMIT 1
);
SET @junior_66 = (
    SELECT idsubcategoria FROM SUBCATEGORIAS
    WHERE nomrangopesaje = '-66 kg' AND idcategoria = @junior_masculino
    ORDER BY idsubcategoria LIMIT 1
);
SET @senior_60 = (
    SELECT idsubcategoria FROM SUBCATEGORIAS
    WHERE nomrangopesaje = '-60 kg' AND idcategoria = @senior_masculino
    ORDER BY idsubcategoria LIMIT 1
);
SET @senior_66 = (
    SELECT idsubcategoria FROM SUBCATEGORIAS
    WHERE nomrangopesaje = '-66 kg' AND idcategoria = @senior_masculino
    ORDER BY idsubcategoria LIMIT 1
);

INSERT IGNORE INTO NIVELESCOMPETICION (idcampeonato, idsubcategoria) VALUES
    (@sector_junior_2025, @junior_60),
    (@sector_junior_2025, @junior_66),
    (@sector_absoluto_2024, @senior_60),
    (@sector_absoluto_2024, @senior_66);

SET @formato = (SELECT idformato FROM FORMATOS_COMPETICION ORDER BY idformato LIMIT 1);
SET @repesca = (SELECT idsistema_repescas FROM SISTEMAS_REPESCA ORDER BY idsistema_repescas LIMIT 1);

INSERT INTO CUADROS (
    idcampeonato, idsubcategoria, idformato, idsistema_repescas, tamano
)
SELECT @sector_junior_2025, @junior_60, @formato, @repesca, 4
WHERE NOT EXISTS (
    SELECT 1 FROM CUADROS
    WHERE idcampeonato = @sector_junior_2025 AND idsubcategoria = @junior_60
);
INSERT INTO CUADROS (
    idcampeonato, idsubcategoria, idformato, idsistema_repescas, tamano
)
SELECT @sector_junior_2025, @junior_66, @formato, @repesca, 4
WHERE NOT EXISTS (
    SELECT 1 FROM CUADROS
    WHERE idcampeonato = @sector_junior_2025 AND idsubcategoria = @junior_66
);
INSERT INTO CUADROS (
    idcampeonato, idsubcategoria, idformato, idsistema_repescas, tamano
)
SELECT @sector_absoluto_2024, @senior_60, @formato, @repesca, 4
WHERE NOT EXISTS (
    SELECT 1 FROM CUADROS
    WHERE idcampeonato = @sector_absoluto_2024 AND idsubcategoria = @senior_60
);
INSERT INTO CUADROS (
    idcampeonato, idsubcategoria, idformato, idsistema_repescas, tamano
)
SELECT @sector_absoluto_2024, @senior_66, @formato, @repesca, 4
WHERE NOT EXISTS (
    SELECT 1 FROM CUADROS
    WHERE idcampeonato = @sector_absoluto_2024 AND idsubcategoria = @senior_66
);

SET @cuadro_junior_60 = (
    SELECT idcuadro FROM CUADROS
    WHERE idcampeonato = @sector_junior_2025 AND idsubcategoria = @junior_60
);
SET @cuadro_junior_66 = (
    SELECT idcuadro FROM CUADROS
    WHERE idcampeonato = @sector_junior_2025 AND idsubcategoria = @junior_66
);
SET @cuadro_senior_60_2024 = (
    SELECT idcuadro FROM CUADROS
    WHERE idcampeonato = @sector_absoluto_2024 AND idsubcategoria = @senior_60
);
SET @cuadro_senior_66_2024 = (
    SELECT idcuadro FROM CUADROS
    WHERE idcampeonato = @sector_absoluto_2024 AND idsubcategoria = @senior_66
);

DELETE FROM CLASIFICACIONES_OFICIALES
WHERE idcuadro IN (
    @cuadro_junior_60, @cuadro_junior_66,
    @cuadro_senior_60_2024, @cuadro_senior_66_2024
);

SET @fuente_junior_2025 = 'https://fmjudo.es/wp-content/uploads/2025/02/Sector-Madrid-Junior-2025-documento-Final.pdf';
SET @fuente_absoluto_2024 = 'https://fmjudo.es/wp-content/uploads/2024/11/Sector-Absoluto-2024-event-report-informe-final.pdf';

INSERT INTO CLASIFICACIONES_OFICIALES
    (idcuadro, posicion, nombre_completo, club, fuente_url)
VALUES
    (@cuadro_junior_60, 1, 'Hugo Huerta Mansilla', 'Judo Valdemoro, C.D.E.', @fuente_junior_2025),
    (@cuadro_junior_60, 2, 'Gonzalo Arauz Hernandez', 'Tsukuri, Club', @fuente_junior_2025),
    (@cuadro_junior_60, 3, 'Iker Fernandez Fernandez', 'C.D.E. Judo Club Brunete', @fuente_junior_2025),
    (@cuadro_junior_60, 3, 'Marco Garcia Ortega', 'Yudo R. Ortega', @fuente_junior_2025),
    (@cuadro_junior_66, 1, 'Ivan Pizarro Casas', 'Judo Valdemoro, C.D.E.', @fuente_junior_2025),
    (@cuadro_junior_66, 2, 'Juan Sanchez Lopez', 'Las Rozas, Club de Judo', @fuente_junior_2025),
    (@cuadro_junior_66, 3, 'Oscar Manso Fernandez', 'Sakura, Gimnasio', @fuente_junior_2025),
    (@cuadro_junior_66, 3, 'Adrian Perez Cavero', 'Sakura, Gimnasio', @fuente_junior_2025),
    (@cuadro_senior_60_2024, 1, 'Raul Diaz Lopez', 'C.D.E. Judo Fontenebro', @fuente_absoluto_2024),
    (@cuadro_senior_60_2024, 2, 'Daniel Robles Roldan', 'Judo Club Sotillo', @fuente_absoluto_2024),
    (@cuadro_senior_60_2024, 3, 'Alvaro Lara Pinera', 'Judo Valdemoro, C.D.E.', @fuente_absoluto_2024),
    (@cuadro_senior_60_2024, 3, 'Jose Manuel Sieiro Diaz', 'Asociacion para la Practica de Judo Mostoles', @fuente_absoluto_2024),
    (@cuadro_senior_66_2024, 1, 'Adrian Nieto Chinarro', 'Judo Valdemoro, C.D.E.', @fuente_absoluto_2024),
    (@cuadro_senior_66_2024, 2, 'Juan Sanchez Lopez', 'Las Rozas, Club de Judo', @fuente_absoluto_2024),
    (@cuadro_senior_66_2024, 3, 'Daniel Martin Lobo', 'C.D.E. Judo Fontenebro', @fuente_absoluto_2024),
    (@cuadro_senior_66_2024, 3, 'Alvaro Sanchez Lopez', 'Las Rozas, Club de Judo', @fuente_absoluto_2024);

COMMIT;
