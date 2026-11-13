USE COMBATES2;

CREATE TABLE IF NOT EXISTS CLASIFICACIONES_OFICIALES (
    idclasificacion  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    idcuadro         INT UNSIGNED NOT NULL,
    posicion         TINYINT UNSIGNED NOT NULL,
    nombre_completo  VARCHAR(100) NOT NULL,
    club             VARCHAR(100) NOT NULL,
    fuente_url       VARCHAR(500) NOT NULL,
    PRIMARY KEY (idclasificacion),
    UNIQUE KEY uq_CLASIFICACIONES_OFICIALES_resultado
        (idcuadro, posicion, nombre_completo),
    CONSTRAINT fk_CLASIFICACIONES_OFICIALES_idcuadro
        FOREIGN KEY (idcuadro)
        REFERENCES CUADROS (idcuadro)
        ON DELETE CASCADE
) ENGINE=InnoDB;
