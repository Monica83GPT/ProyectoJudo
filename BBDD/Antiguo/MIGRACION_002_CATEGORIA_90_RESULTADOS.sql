/* Añade un segundo ranking histórico al Sector Madrid 2025. */
START TRANSACTION;

INSERT IGNORE INTO JUDOKAS
    (numfederado, nombre, apellidos, pesaje, fecnacimiento, genero, fotojudoka)
VALUES
    ('ZZZ000007', 'Lucas', 'Martin Sanz',   89, '1999-02-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000008', 'Hugo', 'Navarro Gil',    88, '2000-03-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000009', 'Diego', 'Ortega Leon',   87, '1998-04-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000010', 'Mario', 'Serrano Vidal', 86, '2001-05-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000011', 'Pablo', 'Molina Rey',    85, '1997-06-01', TRUE, 'imagenes/SINFOTO.svg'),
    ('ZZZ000012', 'Adrian', 'Ramos Prieto', 84, '2002-07-01', TRUE, 'imagenes/SINFOTO.svg');

INSERT IGNORE INTO PERTENECIENTES (idclub, numfederado, fechaaltaclub) VALUES
    (1, 'ZZZ000007', '2025-01-01'),
    (2, 'ZZZ000008', '2025-01-01'),
    (3, 'ZZZ000009', '2025-01-01'),
    (4, 'ZZZ000010', '2025-01-01'),
    (5, 'ZZZ000011', '2025-01-01'),
    (6, 'ZZZ000012', '2025-01-01');

INSERT IGNORE INTO SUBCATEGORIAS
    (idsubcategoria, nomrangopesaje, pesajemin, pesajemax, idcategoria)
VALUES (2, '-90 kg', 81, 90, 1);

INSERT IGNORE INTO NIVELESCOMPETICION (idcampeonato, idsubcategoria) VALUES
    (1, 2), (2, 2), (3, 2), (4, 2);

INSERT IGNORE INTO INSCRITOS
    (numfederado, idcampeonato, idsubcategoria, fechainscripcion, estado, pesajeevento)
VALUES
    ('ZZZ000007', 1, 2, '2025-10-20', 'VALIDADA', 89),
    ('ZZZ000008', 1, 2, '2025-10-20', 'VALIDADA', 88),
    ('ZZZ000009', 1, 2, '2025-10-20', 'VALIDADA', 87),
    ('ZZZ000010', 1, 2, '2025-10-20', 'VALIDADA', 86),
    ('ZZZ000011', 1, 2, '2025-10-20', 'VALIDADA', 85),
    ('ZZZ000012', 1, 2, '2025-10-20', 'VALIDADA', 84);

INSERT IGNORE INTO CUADROS
    (idcuadro, idcampeonato, idsubcategoria, idformato, idsistema_repescas, tamano)
VALUES (2, 1, 2, 1, 2, 8);

INSERT IGNORE INTO PARTICIPANTES_CUADRO
    (idcuadro, numfederado, idcampeonato, idsubcategoria, posicion, cabeza_serie)
VALUES
    (2, 'ZZZ000007', 1, 2, 1, FALSE),
    (2, 'ZZZ000008', 1, 2, 2, FALSE),
    (2, 'ZZZ000009', 1, 2, 3, FALSE),
    (2, 'ZZZ000010', 1, 2, 4, FALSE),
    (2, 'ZZZ000011', 1, 2, 5, FALSE),
    (2, 'ZZZ000012', 1, 2, 6, FALSE);

INSERT IGNORE INTO COMBATES
    (idcombate, idcuadro, ronda, tabla, numcombate, numfederado_azul, numfederado_blanco, fecha, hora, tatami, estado)
VALUES
    (8, 2, 'CUARTOS', 1, 1, 'ZZZ000007', 'ZZZ000011', '2025-11-01', '10:00:00', 2, 'FINALIZADO'),
    (9, 2, 'CUARTOS', 1, 2, 'ZZZ000012', 'ZZZ000009', '2025-11-01', '10:10:00', 2, 'FINALIZADO'),
    (10, 2, 'SEMIFINAL', 1, 1, 'ZZZ000007', 'ZZZ000010', '2025-11-01', '11:00:00', 2, 'FINALIZADO'),
    (11, 2, 'SEMIFINAL', 1, 2, 'ZZZ000008', 'ZZZ000009', '2025-11-01', '11:10:00', 2, 'FINALIZADO'),
    (12, 2, 'BRONCE', 1, 1, 'ZZZ000010', 'ZZZ000011', '2025-11-01', '12:00:00', 2, 'FINALIZADO'),
    (13, 2, 'BRONCE', 1, 2, 'ZZZ000010', 'ZZZ000009', '2025-11-01', '12:10:00', 2, 'FINALIZADO'),
    (14, 2, 'FINAL', 1, 1, 'ZZZ000007', 'ZZZ000008', '2025-11-01', '12:30:00', 2, 'FINALIZADO');

INSERT IGNORE INTO RESULTADOS_COMBATE (idcombate, lado_ganador, metodo_victoria)
VALUES
    (8, 'AZUL', 'Victoria segun acta oficial'),
    (9, 'BLANCO', 'Victoria segun acta oficial'),
    (10, 'AZUL', 'Victoria segun acta oficial'),
    (11, 'AZUL', 'Victoria segun acta oficial'),
    (12, 'AZUL', 'Victoria segun acta oficial'),
    (13, 'BLANCO', 'Victoria segun acta oficial'),
    (14, 'BLANCO', 'Victoria segun acta oficial');

COMMIT;
