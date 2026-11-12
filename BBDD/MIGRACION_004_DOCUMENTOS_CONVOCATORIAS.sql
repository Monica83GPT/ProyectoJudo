/* Añade los PDF oficiales asociados a cada convocatoria. */
ALTER TABLE CONVOCATORIAS
    ADD COLUMN IF NOT EXISTS circular_archivo VARCHAR(255) NULL AFTER fecha_corte_edad,
    ADD COLUMN IF NOT EXISTS normativa_archivo VARCHAR(255) NULL AFTER circular_archivo;

UPDATE CONVOCATORIAS
   SET circular_archivo = 'documentos/convocatorias/pendiente-documentacion.pdf'
 WHERE circular_archivo IS NULL OR circular_archivo = '';

UPDATE CONVOCATORIAS
   SET normativa_archivo = 'documentos/convocatorias/pendiente-documentacion.pdf'
 WHERE normativa_archivo IS NULL OR normativa_archivo = '';

ALTER TABLE CONVOCATORIAS
    MODIFY circular_archivo VARCHAR(255) NOT NULL
        DEFAULT 'documentos/convocatorias/pendiente-documentacion.pdf',
    MODIFY normativa_archivo VARCHAR(255) NOT NULL
        DEFAULT 'documentos/convocatorias/pendiente-documentacion.pdf';
