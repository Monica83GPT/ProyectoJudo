/* Convierte las rutas largas y asigna nombres cortos antes de reducir el campo. */
UPDATE CAMPEONATOS SET logocampeonato = CASE
    WHEN nomcampeonato = 'Sector Madrid 2025' THEN 'cam0001.svg'
    WHEN nomcampeonato = 'España Absoluto 2026' THEN 'cam0002.svg'
    WHEN nomcampeonato = 'European Open 2027' THEN 'cam0003.svg'
    WHEN nomcampeonato = 'Trofeo San Isidro' THEN 'cam0004.svg'
    WHEN nomcampeonato = 'Sector Junior 2025' THEN 'cam0005.svg'
    WHEN nomcampeonato = 'Sector Absoluto 2024' THEN 'cam0006.svg'
    WHEN nomcampeonato = 'Autonómico Infantil 2026' THEN 'cam0007.svg'
    ELSE 'cam0000.svg'
END;

ALTER TABLE CAMPEONATOS
    MODIFY COLUMN logocampeonato CHAR(11) NOT NULL DEFAULT 'cam0000.svg';
