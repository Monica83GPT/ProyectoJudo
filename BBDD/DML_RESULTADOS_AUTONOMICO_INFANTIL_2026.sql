USE COMBATES2;

START TRANSACTION;

-- Fuente oficial completa:
-- https://fmjudo.es/wp-content/uploads/2026/04/ilovepdf_merged-35.pdf

INSERT INTO CATEGORIAS (nomcategoria, genero, edadmax, edadmin)
SELECT 'Infantil Femenino', FALSE, 14, 13
WHERE NOT EXISTS (
    SELECT 1 FROM CATEGORIAS
    WHERE nomcategoria = 'Infantil Femenino' AND genero = FALSE
);
INSERT INTO CATEGORIAS (nomcategoria, genero, edadmax, edadmin)
SELECT 'Infantil Masculino', TRUE, 14, 13
WHERE NOT EXISTS (
    SELECT 1 FROM CATEGORIAS
    WHERE nomcategoria = 'Infantil Masculino' AND genero = TRUE
);

SET @infantil_femenino = (
    SELECT idcategoria FROM CATEGORIAS
    WHERE nomcategoria = 'Infantil Femenino' AND genero = FALSE
    ORDER BY idcategoria LIMIT 1
);
SET @infantil_masculino = (
    SELECT idcategoria FROM CATEGORIAS
    WHERE nomcategoria = 'Infantil Masculino' AND genero = TRUE
    ORDER BY idcategoria LIMIT 1
);

DROP TEMPORARY TABLE IF EXISTS tmp_categorias_infantil_2026;
CREATE TEMPORARY TABLE tmp_categorias_infantil_2026 (
    genero BOOLEAN NOT NULL,
    peso VARCHAR(25) NOT NULL,
    pesajemin TINYINT UNSIGNED NOT NULL,
    pesajemax TINYINT UNSIGNED NOT NULL
);
INSERT INTO tmp_categorias_infantil_2026 VALUES
    (FALSE, '-40 kg', 1, 40),
    (FALSE, '-44 kg', 41, 44),
    (FALSE, '-48 kg', 45, 48),
    (FALSE, '-52 kg', 49, 52),
    (FALSE, '-57 kg', 53, 57),
    (FALSE, '-63 kg', 58, 63),
    (FALSE, '+63 kg', 64, 150),
    (TRUE, '-38 kg', 1, 38),
    (TRUE, '-42 kg', 39, 42),
    (TRUE, '-46 kg', 43, 46),
    (TRUE, '-50 kg', 47, 50),
    (TRUE, '-55 kg', 51, 55),
    (TRUE, '-60 kg', 56, 60),
    (TRUE, '-66 kg', 61, 66),
    (TRUE, '+66 kg', 67, 150);

INSERT INTO SUBCATEGORIAS (nomrangopesaje, pesajemin, pesajemax, idcategoria)
SELECT t.peso, t.pesajemin, t.pesajemax,
       CASE WHEN t.genero = TRUE THEN @infantil_masculino ELSE @infantil_femenino END
FROM tmp_categorias_infantil_2026 t
WHERE NOT EXISTS (
    SELECT 1 FROM SUBCATEGORIAS sc
    WHERE sc.nomrangopesaje = t.peso
      AND sc.idcategoria = CASE WHEN t.genero = TRUE THEN @infantil_masculino ELSE @infantil_femenino END
);

INSERT INTO CAMPEONATOS (nomcampeonato, feccampeonato, logocampeonato, sede)
SELECT 'Autonómico Infantil 2026', '2026-03-29', 'imagenes/SINFOTO.svg',
       'Pabellón Francisco Valcárcel, Villaviciosa de Odón'
WHERE NOT EXISTS (
    SELECT 1 FROM CAMPEONATOS
    WHERE nomcampeonato = 'Autonómico Infantil 2026'
      AND feccampeonato = '2026-03-29'
);

SET @autonomico_infantil_2026 = (
    SELECT idcampeonato FROM CAMPEONATOS
    WHERE nomcampeonato = 'Autonómico Infantil 2026'
      AND feccampeonato = '2026-03-29'
    ORDER BY idcampeonato LIMIT 1
);

INSERT IGNORE INTO NIVELESCOMPETICION (idcampeonato, idsubcategoria)
SELECT @autonomico_infantil_2026, sc.idsubcategoria
FROM SUBCATEGORIAS sc
WHERE sc.idcategoria IN (@infantil_femenino, @infantil_masculino);

SET @formato = (SELECT idformato FROM FORMATOS_COMPETICION ORDER BY idformato LIMIT 1);
SET @repesca = (SELECT idsistema_repescas FROM SISTEMAS_REPESCA ORDER BY idsistema_repescas LIMIT 1);

INSERT INTO CUADROS (
    idcampeonato, idsubcategoria, idformato, idsistema_repescas, tamano
)
SELECT @autonomico_infantil_2026, nc.idsubcategoria, @formato, @repesca, 4
FROM NIVELESCOMPETICION nc
WHERE nc.idcampeonato = @autonomico_infantil_2026
  AND NOT EXISTS (
      SELECT 1 FROM CUADROS cu
      WHERE cu.idcampeonato = nc.idcampeonato
        AND cu.idsubcategoria = nc.idsubcategoria
  );

DELETE co
FROM CLASIFICACIONES_OFICIALES co
JOIN CUADROS cu ON cu.idcuadro = co.idcuadro
WHERE cu.idcampeonato = @autonomico_infantil_2026;

DROP TEMPORARY TABLE IF EXISTS tmp_resultados_infantil_2026;
CREATE TEMPORARY TABLE tmp_resultados_infantil_2026 (
    genero BOOLEAN NOT NULL,
    peso VARCHAR(25) NOT NULL,
    posicion TINYINT UNSIGNED NOT NULL,
    nombre_completo VARCHAR(100) NOT NULL,
    club VARCHAR(100) NOT NULL
);

INSERT INTO tmp_resultados_infantil_2026 VALUES
    (FALSE, '-40 kg', 1, 'Arantxa Agüero Iglesias', 'Sakura, Gimnasio'),
    (FALSE, '-40 kg', 2, 'Valeria Soriano Fernandez', 'Club Hiroshima'),
    (FALSE, '-40 kg', 3, 'Blanca Retamar Jaudenes', 'C.D.E. Judo Fontenebro'),
    (FALSE, '-40 kg', 3, 'Ada García Iruretagoyena', 'Lorkan C.D.E.'),
    (FALSE, '-44 kg', 1, 'Shazira Pérez Pinel', 'Tsukuri, Club'),
    (FALSE, '-44 kg', 2, 'Carla Almendros Rincon', 'Seseña C.D.E.'),
    (FALSE, '-44 kg', 3, 'Aitana Paz Gonzalez', 'C.D.E. Judo Lorenzo'),
    (FALSE, '-44 kg', 3, 'Natalia Rojas Marco', 'El Valle, Club Deportivo Elemental'),
    (FALSE, '-48 kg', 1, 'Valeria Aparicio Barreiro', 'Sakura, Gimnasio'),
    (FALSE, '-48 kg', 2, 'Nora Martin de Loeches Castro', 'Judo Javier Linger'),
    (FALSE, '-48 kg', 3, 'Valeria Mayo Garcia', 'Club Deportivo Fresnos'),
    (FALSE, '-48 kg', 3, 'Valeria Vallejo Arrabal', 'Ronin Club de Judo'),
    (FALSE, '-52 kg', 1, 'Luna Sanchez Gonzalez', 'Judo Club Blanco C.D.E.'),
    (FALSE, '-52 kg', 2, 'Andrea Jimenez Díaz-Hellin', 'Hiroshima Sport'),
    (FALSE, '-52 kg', 3, 'Adriana Sofía Cárcamo Herrera', 'Judo Club Blanco C.D.E.'),
    (FALSE, '-52 kg', 3, 'Valeria Cordero García', 'Judo Club Blanco C.D.E.'),
    (FALSE, '-57 kg', 1, 'Sofia Garcia Herrero', 'Club Judogui JS Actividades Deportivas'),
    (FALSE, '-57 kg', 2, 'Carlota Montilla Muñoz de Morales', 'Aranjuez, Judo Club'),
    (FALSE, '-57 kg', 3, 'Itziar Garcia Almazan', 'C.D.E. Judo Lorenzo'),
    (FALSE, '-57 kg', 3, 'Sara Roldan Campos', 'Budokan, Club'),
    (FALSE, '-63 kg', 1, 'Luna Benitez Manzano', 'Club Deportivo Abaco'),
    (FALSE, '-63 kg', 2, 'Angels Ferrer de Dios', 'Ichi-Ban, Gimnasio'),
    (FALSE, '-63 kg', 3, 'Margot Diaz Altabella', 'Club Hiroshima'),
    (FALSE, '-63 kg', 3, 'Maria Sol Torrealba Sanchez', 'Suchil, Gimnasio.-C.D.E.'),
    (FALSE, '+63 kg', 1, 'Victoria García Martín', 'Las Rozas, Club de Judo'),
    (FALSE, '+63 kg', 2, 'Sara Isabel Boga Pop', 'Budokan, Club'),
    (FALSE, '+63 kg', 3, 'Nerea Masso Florea', 'Judo Club Rivas-Vaciamadrid'),
    (FALSE, '+63 kg', 3, 'Elena Muñoz Ramírez', 'Educando en Deporte C.D.E.'),
    (TRUE, '-38 kg', 1, 'Ivan Flores Pascual', 'Club Hiroshima'),
    (TRUE, '-38 kg', 2, 'Leo Pinilla Plata', 'Judo Club Blanco C.D.E.'),
    (TRUE, '-38 kg', 3, 'Marco Crespo Antonio', 'Judo Club Blanco C.D.E.'),
    (TRUE, '-38 kg', 3, 'Pedro Jimenez Cidoncha', 'Bushidokwai, Club'),
    (TRUE, '-42 kg', 1, 'Jorge Juzdado Gordillo', 'Club de Judo EFN Colmenar Viejo'),
    (TRUE, '-42 kg', 2, 'Nicolas Muñoz Doñate', 'Ibaraki Judo Mostoles'),
    (TRUE, '-42 kg', 3, 'Aleksandr Prasol', 'Ronin Club de Judo'),
    (TRUE, '-42 kg', 3, 'Antonio Santos Lamela', 'Judo Ciempozuelos -David Martin'),
    (TRUE, '-46 kg', 1, 'Pablo Barragan Blanco', 'Judo Club Blanco C.D.E.'),
    (TRUE, '-46 kg', 2, 'Marcos Barroso Donoso', 'Club Hiroshima'),
    (TRUE, '-46 kg', 3, 'Isaac Sttefano Villanueva Villegas', 'Jushirokan, Club'),
    (TRUE, '-46 kg', 3, 'Darío Antelo García', 'Tsukuri, Club'),
    (TRUE, '-50 kg', 1, 'Pablo Adrian Rojas Terceros', 'Tsukuri, Club'),
    (TRUE, '-50 kg', 2, 'Alonso Polo del Pino', 'Judo Inokuma'),
    (TRUE, '-50 kg', 3, 'Kendri Alain Guzman Diaz', 'Judo Club Rivas-Vaciamadrid'),
    (TRUE, '-50 kg', 3, 'Javier Santamaria Ruiz', 'El Valle, Club Deportivo Elemental'),
    (TRUE, '-55 kg', 1, 'Elías López Gordo', 'C.D.E. Judo Club Brunete'),
    (TRUE, '-55 kg', 2, 'Adán Valero Ferreira', 'Manuel Jimenez, Gimnasio'),
    (TRUE, '-55 kg', 3, 'Miquel Arranz Socias', 'Seseña C.D.E.'),
    (TRUE, '-55 kg', 3, 'Alejandro Cuesta Acedo', 'Judo Inokuma'),
    (TRUE, '-60 kg', 1, 'Samuel Augusto Torres Parra', 'Jushirokan, Club'),
    (TRUE, '-60 kg', 2, 'Mario García Arrighi', 'Club Deportivo Elemental Yutori Sports'),
    (TRUE, '-60 kg', 3, 'Asier San Vicente Botas', 'Tsukuri, Club'),
    (TRUE, '-60 kg', 3, 'Johan Santos Ramos', 'Judo Ciempozuelos -David Martin'),
    (TRUE, '-66 kg', 1, 'Hugo Alonso Lebrusan', 'Judo Club Blanco C.D.E.'),
    (TRUE, '-66 kg', 2, 'Yeimar Santiago Caicedo Gomez', 'Jushirokan, Club'),
    (TRUE, '-66 kg', 3, 'Nicolas Freire Garcia-Serrano', 'C.D.E. Judo Fontenebro'),
    (TRUE, '-66 kg', 3, 'Néstor de Francisco Hernán-Gómez', 'Judo Club Kaizen'),
    (TRUE, '+66 kg', 1, 'Alejandro Espinosa Garcia', 'Asociacion para la Practica de Judo Mostoles'),
    (TRUE, '+66 kg', 2, 'Ignacio Sanchez Benito', 'Victor Pradera, Club'),
    (TRUE, '+66 kg', 3, 'Luis Campiñez Valero', 'Judo Leganes, Club Deportivo'),
    (TRUE, '+66 kg', 3, 'Gabriel Hernandez Fernandez', 'Judo Ciempozuelos -David Martin');

INSERT INTO CLASIFICACIONES_OFICIALES (
    idcuadro, posicion, nombre_completo, club, fuente_url
)
SELECT cu.idcuadro, r.posicion, r.nombre_completo, r.club,
       'https://fmjudo.es/wp-content/uploads/2026/04/ilovepdf_merged-35.pdf'
FROM tmp_resultados_infantil_2026 r
JOIN CATEGORIAS cat
  ON cat.nomcategoria = CASE WHEN r.genero = TRUE THEN 'Infantil Masculino' ELSE 'Infantil Femenino' END
 AND cat.genero = r.genero
JOIN SUBCATEGORIAS sc
  ON sc.idcategoria = cat.idcategoria
 AND sc.nomrangopesaje = r.peso
JOIN CUADROS cu
  ON cu.idcampeonato = @autonomico_infantil_2026
 AND cu.idsubcategoria = sc.idsubcategoria;

DROP TEMPORARY TABLE tmp_resultados_infantil_2026;
DROP TEMPORARY TABLE tmp_categorias_infantil_2026;

COMMIT;
