USE COMBATES2;

/*
    Plantilla para crear usuarios de acceso en una instalación local.

    1. Copia este archivo como DML_USUARIOS_COMBATES2.local.sql.
    2. Genera cada hash con PHP:
       php -r "echo password_hash('TU_CLAVE', PASSWORD_DEFAULT);"
    3. Sustituye los marcadores REEMPLAZAR_CON_HASH antes de importarlo.

    El archivo local está excluido de Git para no publicar credenciales.
*/

START TRANSACTION;

INSERT INTO USUARIOS (
    idusuario, nombre_usuario, password_hash, rol, activo
) VALUES
    (1, 'club.usuario', 'REEMPLAZAR_CON_HASH_CLUB', 'CLUB', TRUE),
    (2, 'federado.usuario', 'REEMPLAZAR_CON_HASH_FEDERADO', 'FEDERADO', TRUE),
    (3, 'organizador.usuario', 'REEMPLAZAR_CON_HASH_ORGANIZADOR', 'ORGANIZADOR', TRUE);

INSERT INTO USUARIOS_CLUB (idusuario, idclub) VALUES
    (1, 1);

INSERT INTO USUARIOS_FEDERADOS (idusuario, numfederado) VALUES
    (2, 'ZZZ000001');

INSERT INTO USUARIOS_ORGANIZADORES (idusuario, idorganizador) VALUES
    (3, 1);

COMMIT;
