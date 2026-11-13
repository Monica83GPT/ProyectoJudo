USE COMBATES2;

/*
    Datos de prueba para COMBATES2.

    Fuente de los datos deportivos:
    Sector Madrid Absoluto 2025, categoria masculina -100 kg.
    Villaviciosa de Odon (CTD), 01/11/2025.
    https://fmjudo.es/wp-content/uploads/2025/11/Sector-Madrid-Absoluto-2025-.pdf

    DATOS REALES PUBLICADOS:
    - Campeonato, fecha, sede, categoria y clasificacion.
    - Nombres de los judokas y clubes de afiliacion.

    DATOS TECNICOS FICTICIOS:
    - Licencias con prefijo ZZZ, fechas de nacimiento, pesos, direcciones,
      fechas de alta/inscripcion, horas, tatami y posiciones del cuadro.
    - El detalle de la tecnica de victoria no figura de forma inequívoca en
      el acta extraida; se conserva como "Victoria segun acta oficial".
    - La ruta imagenes/SINFOTO.svg es un marcador provisional.
    - Los plazos de inscripcion de campeonatos futuros son provisionales,
      porque el calendario publica las fechas pero no las convocatorias.

    Este archivo esta preparado para ejecutarse despues de DDL_COMBATES2.sql
    sobre una base COMBATES2 vacia.
*/

START TRANSACTION;

INSERT INTO ZONAS (idzona, nomzona) VALUES
    (1, 'Comunidad Madrid'),
    (2, 'Fuera Comunidad');

INSERT INTO MUNICIPIOS (idmunicipio, nommunicipio, idzona) VALUES
    (1, 'Valdemoro',        1),
    (2, 'Getafe',           1),
    (3, 'Sotillo',          2),
    (4, 'Collado Villalba', 1),
    (5, 'Colmenar Viejo',   1),
    (6, 'Madrid',           1);

-- Los nombres completos oficiales se mantienen en estos comentarios cuando
-- exceden los 20 caracteres permitidos por CLUBS.nomclub.
INSERT INTO CLUBS (idclub, nomclub, emailcontacto, dirclub, logoclub, idmunicipio) VALUES
    (1, 'Judo Valdemoro',      'club1@example.invalid', 'Direccion no publicada; dato tecnico provisional', 'imagenes/SINFOTO.svg', 1),
    (2, 'Judo Club Getafe',    'club2@example.invalid', 'Direccion no publicada; dato tecnico provisional', 'imagenes/SINFOTO.svg', 2),
    (3, 'Judo Club Sotillo',   'club3@example.invalid', 'Direccion no publicada; dato tecnico provisional', 'imagenes/SINFOTO.svg', 3),
    -- C.D.E. Judo Villalba Kazokukan (VKZ)
    (4, 'Judo Villalba Kazo',  'club4@example.invalid', 'Direccion no publicada; dato tecnico provisional', 'imagenes/SINFOTO.svg', 4),
    -- Club de Judo EFN Colmenar Viejo (EFN)
    (5, 'Judo EFN Colmenar',   'club5@example.invalid', 'Direccion no publicada; dato tecnico provisional', 'imagenes/SINFOTO.svg', 5),
    -- Judo Club Blanco C.D.E. (BLA)
    (6, 'Judo Club Blanco',    'club6@example.invalid', 'Direccion no publicada; dato tecnico provisional', 'imagenes/SINFOTO.svg', 6);

-- Todas las licencias ZZZ son deliberadamente ficticias.
INSERT INTO JUDOKAS (
    numfederado, nombre, apellidos, pesaje, fecnacimiento, genero, fotojudoka
) VALUES
    ('ZZZ000001', 'Samuel', 'Fernandez Latorre',      98, '2000-01-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000002', 'Javier', 'Gomez Cabello',           97, '1998-01-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000003', 'Daniel', 'Garcia Ruiz',             99, '1999-01-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000004', 'Victor', 'Javier Saez Pascual',     96, '2001-01-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000005', 'Alberto', 'Aragon de Pablos',       95, '1997-01-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000006', 'Marcos', 'Gamero Sanchez-Mateos',   94, '2002-01-01', TRUE, 'imagenes/SINFOTO.svg');

-- Segundo cuadro de resultados de prueba para la categoría -90 kg.
INSERT INTO JUDOKAS (
    numfederado, nombre, apellidos, pesaje, fecnacimiento, genero, fotojudoka
) VALUES
    ('ZZZ000007', 'Lucas', 'Martin Sanz',   89, '1999-02-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000008', 'Hugo', 'Navarro Gil',    88, '2000-03-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000009', 'Diego', 'Ortega Leon',   87, '1998-04-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000010', 'Mario', 'Serrano Vidal', 86, '2001-05-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000011', 'Pablo', 'Molina Rey',    85, '1997-06-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000012', 'Adrian', 'Ramos Prieto', 84, '2002-07-01', TRUE, 'imagenes/SINFOTO.svg');

INSERT INTO PERTENECIENTES (idclub, numfederado, fechaaltaclub) VALUES
    (1, 'ZZZ000001', '2025-01-01'),
    (2, 'ZZZ000002', '2025-01-01'),
    (3, 'ZZZ000003', '2025-01-01'),
    (4, 'ZZZ000004', '2025-01-01'),
    (5, 'ZZZ000005', '2025-01-01'),
    (6, 'ZZZ000006', '2025-01-01');

INSERT INTO PERTENECIENTES (idclub, numfederado, fechaaltaclub) VALUES
    (1, 'ZZZ000007', '2025-01-01'),
    (2, 'ZZZ000008', '2025-01-01'),
    (3, 'ZZZ000009', '2025-01-01'),
    (4, 'ZZZ000010', '2025-01-01'),
    (5, 'ZZZ000011', '2025-01-01'),
    (6, 'ZZZ000012', '2025-01-01');

INSERT INTO ORGANIZADORES (
    idorganizador, nomorganizador, emailcontacto
) VALUES
    (1, 'Federacion Madrilena de Judo y D.A.', 'competiciones@fmjudo.es');

INSERT INTO CATEGORIAS (
    idcategoria, nomcategoria, genero, edadmax, edadmin
) VALUES
    (1, 'Senior Masculino', TRUE, 99, 18);

INSERT INTO SUBCATEGORIAS (
    idsubcategoria, nomrangopesaje, pesajemin, pesajemax, idcategoria
) VALUES
    (1, '-100 kg', 91, 100, 1),
    (2, '-90 kg', 81, 90, 1);

INSERT INTO CAMPEONATOS (
    idcampeonato, nomcampeonato, feccampeonato, logocampeonato, sede
) VALUES
    (1, 'Sector Madrid 2025', '2025-11-01', 'cam0001.svg',
     'CTD de Villaviciosa de Odon'),
    -- Proximos campeonatos del calendario oficial 2026/2027.
    (2, 'España Absoluto 2026', '2026-11-28', 'cam0002.svg',
     'Lugo'),
    (3, 'European Open 2027', '2027-04-24', 'cam0003.svg',
     'La Nucia (Alicante)'),
    (4, 'Trofeo San Isidro', '2027-05-09', 'cam0004.svg',
     'Villaviciosa de Odon');

INSERT INTO NIVELESCOMPETICION (idcampeonato, idsubcategoria) VALUES
    (1, 1),
    (1, 2),
    (2, 1),
    (2, 2),
    (3, 1),
    (3, 2),
    (4, 1),
    (4, 2);

INSERT INTO FORMATOS_COMPETICION (
    idformato, nombreformato, descripcion
) VALUES
    (1, 'Eliminatoria de 8',
     'Cuadro eliminatorio de hasta ocho participantes con dos medallas de bronce.');

-- DDL_COMBATES2.sql ya crea el sistema 1, "Sin repesca".
INSERT INTO SISTEMAS_REPESCA (
    idsistema_repescas, nombre_sistema, descripcion
) VALUES
    (2, 'Repesca semifinalistas',
     'Los perdedores vinculados a semifinales acceden a los combates por el bronce.');

INSERT INTO INSCRITOS (
    numfederado, idcampeonato, idsubcategoria,
    fechainscripcion, estado, pesajeevento
) VALUES
    ('ZZZ000001', 1, 1, '2025-10-20', 'VALIDADA', 98),
    ('ZZZ000002', 1, 1, '2025-10-20', 'VALIDADA', 97),
    ('ZZZ000003', 1, 1, '2025-10-20', 'VALIDADA', 99),
    ('ZZZ000004', 1, 1, '2025-10-20', 'VALIDADA', 96),
    ('ZZZ000005', 1, 1, '2025-10-20', 'VALIDADA', 95),
    ('ZZZ000006', 1, 1, '2025-10-20', 'VALIDADA', 94);

INSERT INTO INSCRITOS (
    numfederado, idcampeonato, idsubcategoria,
    fechainscripcion, estado, pesajeevento
) VALUES
    ('ZZZ000007', 1, 2, '2025-10-20', 'VALIDADA', 89),
    ('ZZZ000008', 1, 2, '2025-10-20', 'VALIDADA', 88),
    ('ZZZ000009', 1, 2, '2025-10-20', 'VALIDADA', 87),
    ('ZZZ000010', 1, 2, '2025-10-20', 'VALIDADA', 86),
    ('ZZZ000011', 1, 2, '2025-10-20', 'VALIDADA', 85),
    ('ZZZ000012', 1, 2, '2025-10-20', 'VALIDADA', 84);

INSERT INTO CONVOCATORIAS (
    idconvocatoria, idcampeonato, organismo_emisor, plazo_inscripcion,
    requisitos, documentos, reglamento_version
) VALUES
    (1, 1, 'Federacion Madrilena de Judo y D.A.', '2025-10-20',
     'Dato tecnico: licencia en vigor y categoria Senior -100 kg.',
     'Acta oficial del Sector Madrid Absoluto 2025.',
     'Normativa aplicable en 2025; version no publicada en el acta.'),
    -- Las fechas y sedes siguientes son oficiales. Los plazos, requisitos
    -- concretos y documentos son provisionales hasta publicarse la circular.
    (2, 2, 'Real Federación Española de Judo y D.A.', '2026-11-20',
     'Provisional: licencia en vigor, categoria Senior -100 kg y clasificacion cuando proceda.',
     'Pendiente de circular oficial; fecha y sede constan en el calendario RFEJYDA 2026.',
     'Pendiente de circular oficial'),
    (3, 3, 'European Judo Union / RFEJYDA', '2027-04-09',
     'Provisional: licencia en vigor, categoria Senior -100 kg y autorizacion federativa.',
     'Pendiente de circular oficial; fecha y sede constan en el calendario FMJYDA 2026/2027.',
     'Pendiente de circular oficial'),
    (4, 4, 'Federacion Madrilena de Judo y D.A.', '2027-04-30',
     'Provisional: licencia en vigor, grado kyu admitido y categoria Senior -100 kg.',
     'Pendiente de circular oficial; evento incluido en el calendario FMJYDA 2026/2027.',
     'Pendiente de circular oficial');

INSERT INTO CUADROS (
    idcuadro, idcampeonato, idsubcategoria, idformato,
    idsistema_repescas, tamano
) VALUES
    (1, 1, 1, 1, 2, 8),
    (2, 1, 2, 1, 2, 8);

-- Orden tecnico del cuadro; no representa ranking oficial ni cabeza de serie.
INSERT INTO PARTICIPANTES_CUADRO (
    idcuadro, numfederado, idcampeonato, idsubcategoria,
    posicion, cabeza_serie
) VALUES
    (1, 'ZZZ000002', 1, 1, 1, FALSE), -- Javier Gomez
    (1, 'ZZZ000005', 1, 1, 2, FALSE), -- Alberto Aragon
    (1, 'ZZZ000004', 1, 1, 3, FALSE), -- Victor Saez
    (1, 'ZZZ000006', 1, 1, 4, FALSE), -- Marcos Gamero
    (1, 'ZZZ000001', 1, 1, 5, FALSE), -- Samuel Fernandez
    (1, 'ZZZ000003', 1, 1, 6, FALSE); -- Daniel Garcia

INSERT INTO PARTICIPANTES_CUADRO (
    idcuadro, numfederado, idcampeonato, idsubcategoria,
    posicion, cabeza_serie
) VALUES
    (2, 'ZZZ000007', 1, 2, 1, FALSE),
    (2, 'ZZZ000008', 1, 2, 2, FALSE),
    (2, 'ZZZ000009', 1, 2, 3, FALSE),
    (2, 'ZZZ000010', 1, 2, 4, FALSE),
    (2, 'ZZZ000011', 1, 2, 5, FALSE),
    (2, 'ZZZ000012', 1, 2, 6, FALSE);

-- Las horas son tecnicas y sirven para disponer de datos validos en el modelo.
-- Los ganadores reproducen la clasificacion final del acta oficial.
INSERT INTO COMBATES (
    idcombate, idcuadro, ronda, tabla, numcombate,
    numfederado_azul, numfederado_blanco,
    fecha, hora, tatami, estado
) VALUES
    (1, 1, 'CUARTOS', 1, 1, 'ZZZ000002', 'ZZZ000005', '2025-11-01', '10:00:00', 1, 'FINALIZADO'),
    (2, 1, 'CUARTOS', 1, 2, 'ZZZ000006', 'ZZZ000001', '2025-11-01', '10:10:00', 1, 'FINALIZADO'),
    (3, 1, 'SEMIFINAL', 1, 1, 'ZZZ000002', 'ZZZ000004', '2025-11-01', '11:00:00', 1, 'FINALIZADO'),
    (4, 1, 'SEMIFINAL', 1, 2, 'ZZZ000001', 'ZZZ000003', '2025-11-01', '11:10:00', 1, 'FINALIZADO'),
    (5, 1, 'BRONCE', 1, 1, 'ZZZ000003', 'ZZZ000005', '2025-11-01', '12:00:00', 1, 'FINALIZADO'),
    (6, 1, 'BRONCE', 1, 2, 'ZZZ000004', 'ZZZ000006', '2025-11-01', '12:10:00', 1, 'FINALIZADO'),
    (7, 1, 'FINAL', 1, 1, 'ZZZ000002', 'ZZZ000001', '2025-11-01', '12:30:00', 1, 'FINALIZADO');

INSERT INTO COMBATES (
    idcombate, idcuadro, ronda, tabla, numcombate,
    numfederado_azul, numfederado_blanco,
    fecha, hora, tatami, estado
) VALUES
    (8, 2, 'CUARTOS', 1, 1, 'ZZZ000007', 'ZZZ000011', '2025-11-01', '10:00:00', 2, 'FINALIZADO'),
    (9, 2, 'CUARTOS', 1, 2, 'ZZZ000012', 'ZZZ000009', '2025-11-01', '10:10:00', 2, 'FINALIZADO'),
    (10, 2, 'SEMIFINAL', 1, 1, 'ZZZ000007', 'ZZZ000010', '2025-11-01', '11:00:00', 2, 'FINALIZADO'),
    (11, 2, 'SEMIFINAL', 1, 2, 'ZZZ000008', 'ZZZ000009', '2025-11-01', '11:10:00', 2, 'FINALIZADO'),
    (12, 2, 'BRONCE', 1, 1, 'ZZZ000010', 'ZZZ000011', '2025-11-01', '12:00:00', 2, 'FINALIZADO'),
    (13, 2, 'BRONCE', 1, 2, 'ZZZ000010', 'ZZZ000009', '2025-11-01', '12:10:00', 2, 'FINALIZADO'),
    (14, 2, 'FINAL', 1, 1, 'ZZZ000007', 'ZZZ000008', '2025-11-01', '12:30:00', 2, 'FINALIZADO');

INSERT INTO RESULTADOS_COMBATE (
    idcombate, lado_ganador, metodo_victoria
) VALUES
    (1, 'AZUL',   'Victoria segun acta oficial'),
    (2, 'BLANCO', 'Victoria segun acta oficial'),
    (3, 'AZUL',   'Victoria segun acta oficial'),
    (4, 'AZUL',   'Victoria segun acta oficial'),
    (5, 'AZUL',   'Victoria segun acta oficial'),
    (6, 'AZUL',   'Victoria segun acta oficial'),
    (7, 'BLANCO', 'Victoria segun acta oficial');

INSERT INTO RESULTADOS_COMBATE (
    idcombate, lado_ganador, metodo_victoria
) VALUES
    (8, 'AZUL',   'Victoria segun acta oficial'),
    (9, 'BLANCO', 'Victoria segun acta oficial'),
    (10, 'AZUL',  'Victoria segun acta oficial'),
    (11, 'AZUL',  'Victoria segun acta oficial'),
    (12, 'AZUL',  'Victoria segun acta oficial'),
    (13, 'BLANCO','Victoria segun acta oficial'),
    (14, 'BLANCO','Victoria segun acta oficial');

INSERT INTO AVANCES_COMBATE (
    idcombate_origen, tipo, idcombate_destino, posicion_destino
) VALUES
    (1, 'GANA',   3, 'AZUL'),
    (1, 'PIERDE', 5, 'BLANCO'),
    (2, 'GANA',   4, 'AZUL'),
    (2, 'PIERDE', 6, 'BLANCO'),
    (3, 'GANA',   7, 'AZUL'),
    (3, 'PIERDE', 6, 'AZUL'),
    (4, 'GANA',   7, 'BLANCO'),
    (4, 'PIERDE', 5, 'AZUL');

COMMIT;

-- Resumen esperado tras la carga:
-- 12 judokas, 6 clubes, 4 campeonatos, 4 convocatorias,
-- 2 cuadros historicos, 14 combates y 14 resultados.
