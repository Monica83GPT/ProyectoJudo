USE COMBATES2;

CREATE TABLE ORGANIZADORES (
    idorganizador   SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomorganizador  VARCHAR(100) NOT NULL,
    emailcontacto   VARCHAR(120) NOT NULL,
    PRIMARY KEY (idorganizador),
    UNIQUE KEY uq_ORGANIZADORES_email (emailcontacto)
) ENGINE=InnoDB;

CREATE TABLE USUARIOS (
    idusuario       SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre_usuario  VARCHAR(50) NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    rol             ENUM('CLUB', 'FEDERADO', 'ORGANIZADOR') NOT NULL,
    activo          BOOLEAN NOT NULL DEFAULT TRUE,
    fecha_alta      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (idusuario),
    UNIQUE KEY uq_USUARIOS_nombre (nombre_usuario)
) ENGINE=InnoDB;

CREATE TABLE USUARIOS_CLUB (
    idusuario  SMALLINT UNSIGNED NOT NULL,
    idclub     SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (idusuario),
    UNIQUE KEY uq_USUARIOS_CLUB_idclub (idclub),
    CONSTRAINT fk_USUARIOS_CLUB_idusuario
        FOREIGN KEY (idusuario) REFERENCES USUARIOS (idusuario),
    CONSTRAINT fk_USUARIOS_CLUB_idclub
        FOREIGN KEY (idclub) REFERENCES CLUBS (idclub)
) ENGINE=InnoDB;

CREATE TABLE USUARIOS_FEDERADOS (
    idusuario    SMALLINT UNSIGNED NOT NULL,
    numfederado  CHAR(9) NOT NULL,
    PRIMARY KEY (idusuario),
    UNIQUE KEY uq_USUARIOS_FEDERADOS_numfederado (numfederado),
    CONSTRAINT fk_USUARIOS_FEDERADOS_idusuario
        FOREIGN KEY (idusuario) REFERENCES USUARIOS (idusuario),
    CONSTRAINT fk_USUARIOS_FEDERADOS_numfederado
        FOREIGN KEY (numfederado) REFERENCES JUDOKAS (numfederado)
) ENGINE=InnoDB;

CREATE TABLE USUARIOS_ORGANIZADORES (
    idusuario      SMALLINT UNSIGNED NOT NULL,
    idorganizador  SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (idusuario),
    UNIQUE KEY uq_USUARIOS_ORGANIZADORES_idorganizador (idorganizador),
    CONSTRAINT fk_USUARIOS_ORGANIZADORES_idusuario
        FOREIGN KEY (idusuario) REFERENCES USUARIOS (idusuario),
    CONSTRAINT fk_USUARIOS_ORGANIZADORES_idorganizador
        FOREIGN KEY (idorganizador) REFERENCES ORGANIZADORES (idorganizador)
) ENGINE=InnoDB;
