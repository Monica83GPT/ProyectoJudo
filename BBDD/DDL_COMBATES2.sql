-- ATENCION: este script elimina COMBATES2 si ya existe.
DROP DATABASE IF EXISTS COMBATES2;
CREATE DATABASE COMBATES2
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE COMBATES2;

CREATE TABLE ZONAS (
    idzona      TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomzona     VARCHAR(20) NOT NULL,
    PRIMARY KEY (idzona)
) ENGINE=InnoDB;

CREATE TABLE MUNICIPIOS (
    idmunicipio    TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nommunicipio   VARCHAR(20) NOT NULL,
    idzona         TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (idmunicipio),
    CONSTRAINT fk_MUNICIPIOS_idzona
        FOREIGN KEY (idzona)
        REFERENCES ZONAS (idzona)
) ENGINE=InnoDB;

CREATE TABLE CLUBS (
    idclub         SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomclub        VARCHAR(20) NOT NULL,
    emailcontacto  VARCHAR(120) NOT NULL,
    dirclub        VARCHAR(100) NOT NULL,
    logoclub       CHAR(26) NOT NULL DEFAULT 'clubs/images/img/00000.jpg',
    idmunicipio    TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (idclub),
    UNIQUE KEY uq_CLUBS_email (emailcontacto),
    CONSTRAINT fk_CLUBS_idmunicipio
        FOREIGN KEY (idmunicipio)
        REFERENCES MUNICIPIOS (idmunicipio)
) ENGINE=InnoDB;

CREATE TABLE JUDOKAS (
    numfederado    CHAR(9) NOT NULL,
    nombre         VARCHAR(12) NOT NULL,
    apellidos      VARCHAR(30) NOT NULL,
    pesaje         TINYINT UNSIGNED NOT NULL,
    fecnacimiento  DATE NOT NULL,
    genero         BOOLEAN NOT NULL,
    fotojudoka     CHAR(28) NOT NULL,
    PRIMARY KEY (numfederado)
) ENGINE=InnoDB;

CREATE TABLE PERTENECIENTES (
    idclub          SMALLINT UNSIGNED NOT NULL,
    numfederado     CHAR(9) NOT NULL,
    fechaaltaclub   DATE NOT NULL,
    PRIMARY KEY (idclub, numfederado, fechaaltaclub),
    CONSTRAINT fk_PERTENECIENTES_idclub
        FOREIGN KEY (idclub)
        REFERENCES CLUBS (idclub),
    CONSTRAINT fk_PERTENECIENTES_numfederado
        FOREIGN KEY (numfederado)
        REFERENCES JUDOKAS (numfederado)
) ENGINE=InnoDB;

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
        FOREIGN KEY (idusuario)
        REFERENCES USUARIOS (idusuario),
    CONSTRAINT fk_USUARIOS_CLUB_idclub
        FOREIGN KEY (idclub)
        REFERENCES CLUBS (idclub)
) ENGINE=InnoDB;

CREATE TABLE USUARIOS_FEDERADOS (
    idusuario    SMALLINT UNSIGNED NOT NULL,
    numfederado  CHAR(9) NOT NULL,
    PRIMARY KEY (idusuario),
    UNIQUE KEY uq_USUARIOS_FEDERADOS_numfederado (numfederado),
    CONSTRAINT fk_USUARIOS_FEDERADOS_idusuario
        FOREIGN KEY (idusuario)
        REFERENCES USUARIOS (idusuario),
    CONSTRAINT fk_USUARIOS_FEDERADOS_numfederado
        FOREIGN KEY (numfederado)
        REFERENCES JUDOKAS (numfederado)
) ENGINE=InnoDB;

CREATE TABLE USUARIOS_ORGANIZADORES (
    idusuario      SMALLINT UNSIGNED NOT NULL,
    idorganizador  SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (idusuario),
    UNIQUE KEY uq_USUARIOS_ORGANIZADORES_idorganizador (idorganizador),
    CONSTRAINT fk_USUARIOS_ORGANIZADORES_idusuario
        FOREIGN KEY (idusuario)
        REFERENCES USUARIOS (idusuario),
    CONSTRAINT fk_USUARIOS_ORGANIZADORES_idorganizador
        FOREIGN KEY (idorganizador)
        REFERENCES ORGANIZADORES (idorganizador)
) ENGINE=InnoDB;

CREATE TABLE CATEGORIAS (
    idcategoria    TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomcategoria   VARCHAR(25) NOT NULL,
    genero         BOOLEAN NOT NULL,
    edadmax        TINYINT UNSIGNED NOT NULL,
    edadmin        TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (idcategoria),
    CONSTRAINT chk_CATEGORIAS_edades
        CHECK (edadmin <= edadmax)
) ENGINE=InnoDB;

CREATE TABLE SUBCATEGORIAS (
    idsubcategoria    TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomrangopesaje    VARCHAR(25) NOT NULL,
    pesajemin         TINYINT UNSIGNED NOT NULL,
    pesajemax         TINYINT UNSIGNED NOT NULL,
    idcategoria       TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (idsubcategoria),
    CONSTRAINT chk_SUBCATEGORIAS_pesos
        CHECK (pesajemin <= pesajemax),
    CONSTRAINT fk_SUBCATEGORIAS_idcategoria
        FOREIGN KEY (idcategoria)
        REFERENCES CATEGORIAS (idcategoria)
) ENGINE=InnoDB;

CREATE TABLE CAMPEONATOS (
    idcampeonato    TINYINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nomcampeonato   VARCHAR(20) NOT NULL,
    feccampeonato   DATE NOT NULL,
    logocampeonato  CHAR(31) NOT NULL DEFAULT 'campeonato/images/img/00000.jpg',
    sede            VARCHAR(100) NOT NULL,
    PRIMARY KEY (idcampeonato)
) ENGINE=InnoDB;

CREATE TABLE NIVELESCOMPETICION (
    idcampeonato     TINYINT UNSIGNED NOT NULL,
    idsubcategoria   TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (idcampeonato, idsubcategoria),
    CONSTRAINT fk_NIVELESCOMPETICION_idcampeonato
        FOREIGN KEY (idcampeonato)
        REFERENCES CAMPEONATOS (idcampeonato),
    CONSTRAINT fk_NIVELESCOMPETICION_idsubcategoria
        FOREIGN KEY (idsubcategoria)
        REFERENCES SUBCATEGORIAS (idsubcategoria)
) ENGINE=InnoDB;

CREATE TABLE FORMATOS_COMPETICION (
    idformato       SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombreformato   VARCHAR(50) NOT NULL,
    descripcion     VARCHAR(255) NOT NULL,
    PRIMARY KEY (idformato),
    UNIQUE KEY uq_FORMATOS_COMPETICION_nombre (nombreformato)
) ENGINE=InnoDB;

CREATE TABLE SISTEMAS_REPESCA (
    idsistema_repescas   SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre_sistema       VARCHAR(50) NOT NULL,
    descripcion          VARCHAR(255) NOT NULL,
    PRIMARY KEY (idsistema_repescas),
    UNIQUE KEY uq_SISTEMAS_REPESCA_nombre (nombre_sistema)
) ENGINE=InnoDB;

INSERT INTO SISTEMAS_REPESCA (nombre_sistema, descripcion)
VALUES ('Sin repesca', 'El perdedor de un combate no accede a una repesca.');

CREATE TABLE INSCRITOS (
    numfederado       CHAR(9) NOT NULL,
    idcampeonato      TINYINT UNSIGNED NOT NULL,
    idsubcategoria    TINYINT UNSIGNED NOT NULL,
    fechainscripcion  DATE NOT NULL,
    estado            ENUM('BORRADOR', 'ENVIADA', 'PENDIENTE', 'VALIDADA', 'RECHAZADA', 'RETIRADA') NOT NULL DEFAULT 'ENVIADA',
    pesajeevento      TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (numfederado, idcampeonato, idsubcategoria),
    UNIQUE KEY uq_INSCRITOS_judoka_campeonato (numfederado, idcampeonato),
    CONSTRAINT fk_INSCRITOS_nivel
        FOREIGN KEY (idcampeonato, idsubcategoria)
        REFERENCES NIVELESCOMPETICION (idcampeonato, idsubcategoria),
    CONSTRAINT fk_INSCRITOS_numfederado
        FOREIGN KEY (numfederado)
        REFERENCES JUDOKAS (numfederado)
) ENGINE=InnoDB;

CREATE TABLE CONVOCATORIAS (
    idconvocatoria     SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    idcampeonato       TINYINT UNSIGNED NOT NULL,
    organismo_emisor   VARCHAR(100) NOT NULL,
    plazo_inscripcion  DATE NOT NULL,
    requisitos         TEXT NOT NULL,
    documentos         TEXT NOT NULL,
    reglamento_version VARCHAR(100) NOT NULL,
    fecha_corte_edad   DATE NOT NULL,
    PRIMARY KEY (idconvocatoria),
    CONSTRAINT fk_CONVOCATORIAS_idcampeonato
        FOREIGN KEY (idcampeonato)
        REFERENCES CAMPEONATOS (idcampeonato)
) ENGINE=InnoDB;

CREATE TABLE CUADROS (
    idcuadro             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idcampeonato         TINYINT UNSIGNED NOT NULL,
    idsubcategoria       TINYINT UNSIGNED NOT NULL,
    idformato            SMALLINT UNSIGNED NOT NULL,
    idsistema_repescas   SMALLINT UNSIGNED NOT NULL,
    tamano               SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (idcuadro),
    UNIQUE KEY uq_CUADROS_campeonato_subcategoria (idcampeonato, idsubcategoria),
    UNIQUE KEY uq_CUADROS_contexto (idcuadro, idcampeonato, idsubcategoria),
    CONSTRAINT chk_CUADROS_tamano
        CHECK (tamano > 0),
    CONSTRAINT fk_CUADROS_nivel
        FOREIGN KEY (idcampeonato, idsubcategoria)
        REFERENCES NIVELESCOMPETICION (idcampeonato, idsubcategoria),
    CONSTRAINT fk_CUADROS_idformato
        FOREIGN KEY (idformato)
        REFERENCES FORMATOS_COMPETICION (idformato),
    CONSTRAINT fk_CUADROS_idsistema_repescas
        FOREIGN KEY (idsistema_repescas)
        REFERENCES SISTEMAS_REPESCA (idsistema_repescas)
) ENGINE=InnoDB;

CREATE TABLE PARTICIPANTES_CUADRO (
    idcuadro          INT UNSIGNED NOT NULL,
    numfederado       CHAR(9) NOT NULL,
    idcampeonato      TINYINT UNSIGNED NOT NULL,
    idsubcategoria    TINYINT UNSIGNED NOT NULL,
    posicion          SMALLINT UNSIGNED NOT NULL,
    cabeza_serie      BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (idcuadro, numfederado),
    UNIQUE KEY uq_PARTICIPANTES_CUADRO_posicion (idcuadro, posicion),
    CONSTRAINT fk_PARTICIPANTES_CUADRO_contexto
        FOREIGN KEY (idcuadro, idcampeonato, idsubcategoria)
        REFERENCES CUADROS (idcuadro, idcampeonato, idsubcategoria),
    CONSTRAINT fk_PARTICIPANTES_CUADRO_inscripcion
        FOREIGN KEY (numfederado, idcampeonato, idsubcategoria)
        REFERENCES INSCRITOS (numfederado, idcampeonato, idsubcategoria)
) ENGINE=InnoDB;

CREATE TABLE COMBATES (
    idcombate            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idcuadro             INT UNSIGNED NOT NULL,
    ronda                VARCHAR(25) NOT NULL,
    tabla                SMALLINT UNSIGNED NOT NULL,
    numcombate           SMALLINT UNSIGNED NOT NULL,
    numfederado_azul     CHAR(9) NOT NULL,
    numfederado_blanco   CHAR(9) NOT NULL,
    fecha                DATE NOT NULL,
    hora                 TIME NOT NULL,
    tatami               SMALLINT UNSIGNED NOT NULL,
    estado               ENUM('PROGRAMADO', 'EN_CURSO', 'FINALIZADO', 'ANULADO') NOT NULL DEFAULT 'PROGRAMADO',
    PRIMARY KEY (idcombate),
    UNIQUE KEY uq_COMBATES_cuadro_ronda_tabla_numero (idcuadro, ronda, tabla, numcombate),
    CONSTRAINT chk_COMBATES_participantes_distintos
        CHECK (numfederado_azul <> numfederado_blanco),
    CONSTRAINT fk_COMBATES_participante_azul
        FOREIGN KEY (idcuadro, numfederado_azul)
        REFERENCES PARTICIPANTES_CUADRO (idcuadro, numfederado),
    CONSTRAINT fk_COMBATES_participante_blanco
        FOREIGN KEY (idcuadro, numfederado_blanco)
        REFERENCES PARTICIPANTES_CUADRO (idcuadro, numfederado)
) ENGINE=InnoDB;

CREATE TABLE RESULTADOS_COMBATE (
    idcombate        INT UNSIGNED NOT NULL,
    lado_ganador     ENUM('AZUL', 'BLANCO') NOT NULL,
    metodo_victoria  VARCHAR(40) NOT NULL,
    PRIMARY KEY (idcombate),
    CONSTRAINT fk_RESULTADOS_COMBATE_idcombate
        FOREIGN KEY (idcombate)
        REFERENCES COMBATES (idcombate)
) ENGINE=InnoDB;

CREATE TABLE AVANCES_COMBATE (
    idcombate_origen    INT UNSIGNED NOT NULL,
    tipo                ENUM('GANA', 'PIERDE') NOT NULL,
    idcombate_destino   INT UNSIGNED NOT NULL,
    posicion_destino    ENUM('AZUL', 'BLANCO') NOT NULL,
    PRIMARY KEY (idcombate_origen, tipo),
    CONSTRAINT chk_AVANCES_COMBATE_destino_distinto
        CHECK (idcombate_origen <> idcombate_destino),
    CONSTRAINT fk_AVANCES_COMBATE_origen
        FOREIGN KEY (idcombate_origen)
        REFERENCES COMBATES (idcombate),
    CONSTRAINT fk_AVANCES_COMBATE_destino
        FOREIGN KEY (idcombate_destino)
        REFERENCES COMBATES (idcombate)
) ENGINE=InnoDB;
