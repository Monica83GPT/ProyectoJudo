DROP DATABASE IF EXISTS Combates;
CREATE DATABASE Combates CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Combates;

CREATE TABLE ZONAS (
    idzona      TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nomzona     VARCHAR( 20 ) NOT NULL 
) ENGINE="InnoDB";

CREATE TABLE MUNICIPIOS (
    idmunicipio    TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nommunicipio   VARCHAR( 20 ) NOT NULL,
    idzona         TINYINT UNSIGNED NOT NULL,
     CONSTRAINT fk_MUNICIPIOS_idzona 
        FOREIGN KEY ( idzona ) 
        REFERENCES ZONAS ( idzona ) 

) ENGINE="InnoDB";

CREATE TABLE CLUBS (
    idclub    SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nomclub   VARCHAR( 20 ) NOT NULL,
    dirclub   VARCHAR ( 100 ) NOT NULL,
    logoclub  CHAR (26) NOT NULL DEFAULT "clubs/images/img/00000.jpg"
     COMMENT "clubs/images/img/00001.jpg",
    idmunicipio    TINYINT UNSIGNED  NOT NULL, 
    CONSTRAINT fk_CLUBS_idmunicipio 
        FOREIGN KEY ( idmunicipio ) 
        REFERENCES MUNICIPIOS ( idmunicipio )
) ENGINE="InnoDB";

CREATE TABLE JUDOKAS (
    numfederado    CHAR ( 9 ) PRIMARY KEY,
    nombre         VARCHAR( 12 ) NOT NULL,
    apellidos      VARCHAR ( 30 ) NOT NULL,
    pesaje         TINYINT UNSIGNED NOT NULL,
    fecnacimiento  DATE NOT NULL,
    genero         BOOLEAN NOT NULL,
    fotojudoka     CHAR (28) NOT NULL
     COMMENT "judocas/images/img/00001.jpg"
) ENGINE="InnoDB";

CREATE TABLE PERTENECIENTES (
    idclub    SMALLINT UNSIGNED NOT NULL,
    CONSTRAINT fk_PERTENECIENTES_idclub 
        FOREIGN KEY ( idclub ) 
        REFERENCES CLUBS ( idclub ),

    numfederado   CHAR ( 9 ) NOT NULL,
    CONSTRAINT fk_PERTENECIENTES_numfederado 
        FOREIGN KEY ( numfederado ) 
        REFERENCES JUDOKAS ( numfederado ),
    fechaaltaclub   DATE NOT NULL,   
    PRIMARY KEY (idclub, numfederado, fechaaltaclub)
) ENGINE="InnoDB";

CREATE TABLE CATEGORIAS (
    idcategoria    TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nomcategoria   VARCHAR( 25 ) NOT NULL,
    genero         BOOLEAN NOT NULL,
    edadmax        TINYINT UNSIGNED NOT NULL,
    edadmin        TINYINT UNSIGNED NOT NULL
) ENGINE="InnoDB";

CREATE TABLE SUBCATEGORIAS (
    idsubcategoria    TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nomrangopesaje   VARCHAR( 25 ) NOT NULL,
    pesajemin        TINYINT UNSIGNED NOT NULL,
    pesajemax        TINYINT UNSIGNED NOT NULL,
    idcategoria      TINYINT UNSIGNED NOT NULL,
    CONSTRAINT fk_SUBCATEGORIAS_idcategoria
        FOREIGN KEY ( idcategoria ) 
        REFERENCES CATEGORIAS ( idcategoria )
) ENGINE="InnoDB";

CREATE TABLE CAMPEONATOS (
    idcampeonato    TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nomcampeonato   VARCHAR( 20 ) NOT NULL,
    feccampeonato   DATE NOT NULL,
    logocampeonato  CHAR (31) NOT NULL DEFAULT "campeonato/images/img/00000.jpg" 
     COMMENT "campeonato/images/img/00001.jpg."  
) ENGINE="InnoDB";

CREATE TABLE NIVELESCOMPETICION (
    idcampeonato    TINYINT UNSIGNED,
    idsubcategoria    TINYINT UNSIGNED,
    PRIMARY KEY (idcampeonato, idsubcategoria),
    CONSTRAINT fk_NIVELESCOMPETICION_idcampeonato
        FOREIGN KEY ( idcampeonato ) 
        REFERENCES CAMPEONATOS ( idcampeonato ),
    CONSTRAINT fk_NIVELESCOMPETICION_idsubcategoria
        FOREIGN KEY ( idsubcategoria ) 
        REFERENCES SUBCATEGORIAS ( idsubcategoria )
) ENGINE="InnoDB";

CREATE TABLE INSCRITOS (
    numfederado        CHAR ( 9 ),
    idcampeonato       TINYINT UNSIGNED,
    idsubcategoria     TINYINT UNSIGNED,
    fechainscripcion   DATE NOT NULL,
    PRIMARY KEY (numfederado, idcampeonato, idsubcategoria ),
    UNIQUE (numfederado, idcampeonato ),
    CONSTRAINT fk_INSCRITOS_idcampeonato
        FOREIGN KEY ( idcampeonato, idsubcategoria ) 
        REFERENCES NIVELESCOMPETICION ( idcampeonato, idsubcategoria ),
    CONSTRAINT fk_INSCRITOS_numfederado
        FOREIGN KEY ( numfederado ) 
        REFERENCES JUDOKAS ( numfederado )

        
) ENGINE="InnoDB";

CREATE TABLE COMBATES (
    numfederadoazul        CHAR ( 9 ),
    numfederadoblanco      CHAR ( 9 ),
    fechacombate           DATE,
    PRIMARY KEY (numfederadoazul, numfederadoblanco, fechacombate ),
    idcampeonato       TINYINT UNSIGNED NOT NULL,
    idsubcategoria     TINYINT UNSIGNED NOT NULL,
    resultado          BOOLEAN NOT NULL,
    
    CONSTRAINT fk_COMBATES_numfederadoazul
        FOREIGN KEY ( numfederadoazul, idcampeonato, idsubcategoria ) 
        REFERENCES INSCRITOS ( numfederado, idcampeonato, idsubcategoria ),
    
    CONSTRAINT fk_COMBATES_numfederadoblanco
        FOREIGN KEY ( numfederadoblanco, idcampeonato, idsubcategoria ) 
        REFERENCES INSCRITOS ( numfederado, idcampeonato, idsubcategoria )
        
) ENGINE="InnoDB";
