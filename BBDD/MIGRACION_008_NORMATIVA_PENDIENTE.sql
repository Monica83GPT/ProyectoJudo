-- La versión de normativa se completa al publicar la documentación oficial.
ALTER TABLE CONVOCATORIAS
    MODIFY reglamento_version VARCHAR(100) NOT NULL DEFAULT 'pendiente-documentacion.pdf';
