USE COMBATES2;

-- Añade el contacto necesario para que los federados soliciten la inscripción
-- a su club. No se introduce ningún NULL: cada club recibe un valor único.
ALTER TABLE CLUBS
    ADD COLUMN IF NOT EXISTS emailcontacto VARCHAR(120) NOT NULL DEFAULT '' AFTER nomclub;

UPDATE CLUBS
   SET emailcontacto = CONCAT('club', idclub, '@example.invalid')
 WHERE emailcontacto = '';

ALTER TABLE CLUBS
    MODIFY emailcontacto VARCHAR(120) NOT NULL;

CREATE UNIQUE INDEX IF NOT EXISTS uq_CLUBS_email ON CLUBS (emailcontacto);
