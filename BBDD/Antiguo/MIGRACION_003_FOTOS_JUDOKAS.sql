/* Amplía la ruta de las fotografías y las traslada al directorio público definitivo. */
START TRANSACTION;

ALTER TABLE JUDOKAS
    MODIFY fotojudoka VARCHAR(255) NOT NULL;

UPDATE JUDOKAS
   SET fotojudoka = CONCAT(
       'imagenes/judokas/',
       SUBSTRING(fotojudoka, CHAR_LENGTH('uploads/j/') + 1)
   )
 WHERE fotojudoka LIKE 'uploads/j/%';

COMMIT;
