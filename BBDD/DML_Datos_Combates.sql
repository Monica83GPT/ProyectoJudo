USE Combates;

-- Opcional.
START TRANSACTION;

INSERT INTO ZONAS (
    idzona,
    nomzona
) VALUES 
    ( null, "Zona 1"     ),
    ( null, "Zona 2"     ),
    ( null, "Zona 3"     );

INSERT INTO MUNICIPIOS (
    idmunicipio,
    nommunicipio,
    idzona
) VALUES 
    ( null, "Talamanca de Jarama", 1        ),
    ( null, "Alcobendas", 1                 ),
    ( null, "Las Rozas", 2                  ),
    ( null, "Coslada", 3                    ),
    ( null, "Camarma de Esteruelas", 3      ),
    ( null, "Meco", 3                       ),
    ( null, "Algete", 1                     ),
    ( null, "Getafe", 3                     ),
    ( null, "Valdetorres", 2                );

INSERT INTO CLUBS (
    idclub,
    nomclub,
    dirclub,
    logoclub,
    idmunicipio

) VALUES 
    ( null, "WINNER", "C/ Alcalá 18", "clubs/images/img/00001.jpg" , 1                            ),
    ( null, "Colegio EL VALLE", " C/ Carranza 22" ,"clubs/images/img/00001.jpg" , 2               ),
    ( null, "Judo Club Habana", " C/ Sierra de Bobia 12","clubs/images/img/00001.jpg" ,3          ),
    ( null, "Victor Pradera", " C/ Santisima Trinidad 3", "clubs/images/img/00001.jpg", 4         ),
    ( null, "Club Judo Camarma", "C/ Mayor 20","clubs/images/img/00001.jpg" , 5                   ),
    ( null, "Gimnasio Sakura", " C/ Huertas 8","clubs/images/img/00001.jpg" , 6                   ),
    ( null, "Club Judo Alcobendas", "C/ Marqués de Valdavia 45", "clubs/images/img/00001.jpg", 2  ),
    ( null, "Judo Club Las Rozas", "C/ Real 28", "clubs/images/img/00001.jpg", 3                  ),
    ( null, "Club Judo Coslada", "C/ Doctor Fleming 14", "clubs/images/img/00001.jpg", 4          ),
    ( null, "Judo Club Meco", "C/ Mayor 35", "clubs/images/img/00001.jpg", 6                      ),
    ( null, "Club Judo Getafe", "C/ Madrid 42", "clubs/images/img/00001.jpg", 8                   ),
    ( null, "Judo Valdetorres", "C/ Constitución 16", "clubs/images/img/00001.jpg", 7        );

INSERT INTO JUDOKAS (
    numfederado,
    nombre,
    apellidos,
    pesaje,
    fecnacimiento,
    genero,
    fotojudoka

) VALUES 
    ("25JU20287", "Hector", "Diaz Areces", 71 ,"2012-03-25", 1, "judocas/images/img/00001.jpg"        ),
    ("25JU20233", "Gabriel", "De Usera García", 72 ,"2012-03-25", 1, "judocas/images/img/00001.jpg"   ),
    ("24JU20299", "Antonio", "García Perez", 65 ,"2011-04-22",1, "judocas/images/img/00001.jpg"       ),
    ("24JU20244", "Pepe", "Alba Ruiz", 64 ,"2011-02-05", 1, "judocas/images/img/00001.jpg"            ),
    ("23JU20255", "Ana", "Diaz Areces", 59 ,"2014-11-25", 0, "judocas/images/img/00001.jpg"          ),
    ("23JU20266", "EVA", "Rodriguez Armenteros", 60 ,"2014-06-07", 0, "judocas/images/img/00001.jpg" ),
    ("24JU20302", "Sergio", "Vazquez Perez", 76, "2013-01-30", 1, "judocas/images/img/00015.jpg"      ),
    ("24JU20303", "Diego", "Ramos Martin", 62, "2013-11-08", 1, "judocas/images/img/00016.jpg"        ),
    ("24JU20304", "Jorge", "Mendez Garcia", 67, "2013-05-19", 1, "judocas/images/img/00017.jpg"       ),
    ("24JU20306", "Paula", "Dominguez Lopez", 58, "2011-12-14", 0, "judocas/images/img/00019.jpg"    ),
    ("24JU20307", "Nerea", "Iglesias Martin", 63, "2010-03-07", 0, "judocas/images/img/00020.jpg"    ),
    ("24JU20308", "Sara", "Cabrera Ruiz", 56, "2011-10-25", 0, "judocas/images/img/00021.jpg"        );
    
   

INSERT INTO CATEGORIAS (
    idcategoria,
    nomcategoria,
    genero,
    edadmax,
    edadmin


) VALUES 
    ( null, "Cadete Masculino", 1, 17, 15      ),
    ( null, "Cadete Femenino", 0, 17, 15     ),
    ( null, "Infantil Masculino", 1, 14, 12    ),
    ( null, "Infantil Femenino", 0, 14, 12    );

INSERT INTO SUBCATEGORIAS (
    idsubcategoria,
    nomrangopesaje,
    pesajemin,
    pesajemax,
    idcategoria

) VALUES 
    ( null, "-72 Cadete Masculino", 66, 72, 1      ),
    ( null, "-65 Cadete Masculino", 50, 65, 1      ),
    ( null, "-68 Cadete Femenino", 62, 68, 2       ),
    ( null, "-61 Cadete Femenino", 45, 61, 2       ),
    ( null, "+66 Infantil Masculino", 66, 100, 3   ),
    ( null, "-65 Infantil Masculino", 63, 65, 3    ),
    ( null, "+63 Infantil Femenino", 63, 100, 4    ),
    ( null, "-62 Infantil Femenino", 54, 62, 4     );

INSERT INTO CAMPEONATOS (
    idcampeonato,
    nomcampeonato,
    feccampeonato,
    logocampeonato

) VALUES 
    ( null, "Madrileño 2026","2026-03-27", "campeonato/images/img/00001.jpg"  ),
    ( null, "Municipios 2026","2026-01-20", "campeonato/images/img/00001.jpg" );
    
INSERT INTO NIVELESCOMPETICION (
    idcampeonato,
    idsubcategoria

) VALUES 
    ( 1, 1  ),
    ( 1, 2  ),
    ( 1, 3  ),
    ( 1, 4  ),
    ( 1, 5  ),
    ( 1, 6  ),
    ( 1, 7  ),
    ( 1, 8  ),
    ( 2, 2  ),
    ( 2, 4  ),
    ( 2, 6  );

INSERT INTO INSCRITOS (
    numfederado,
    idcampeonato,
    idsubcategoria,
    fechainscripcion


) VALUES 
    ( "23JU20266", "1", "8", "2026-02-20" ),
    ( "23JU20255", "1", "8", "2026-03-20" ),
    ( "25JU20287","1", "5", "2026-03-05"  ),
    ( "25JU20233","1", "5", "2026-03-10"  ),
    ( "24JU20299","1", "2", "2026-03-12"  ),
    ( "24JU20244","1", "2", "2026-02-12"  ),
    ( "24JU20306","1", "8", "2026-03-12"  ),
    ( "24JU20308","1", "8", "2026-02-12"  );



INSERT INTO COMBATES (
    numfederadoazul,
    numfederadoblanco,
    fechacombate,
    idcampeonato,
    idsubcategoria,
    resultado

) VALUES 
    ( "23JU20266", "23JU20255", "2026-03-27", "1", "8", "0"  ),
    ( "25JU20287", "25JU20233", "2026-03-27", "1",  "5", "1" ),
    ( "25JU20287", "25JU20233", "2025-03-27", "1",  "5", "0" ),
    ( "25JU20287", "25JU20233", "2024-03-27", "1",  "5", "0" ),
    ( "24JU20299", "24JU20244", "2026-03-27", "1", "2", "0"  );

    INSERT INTO PERTENECIENTES (
    idclub,
    numfederado,
    fechaaltaclub


) VALUES 
    ( "1","23JU20266", "2020-02-20" ),
    ( "2","23JU20255", "2022-03-20" ),
    ( "7","25JU20287", "2024-03-05"  ),
    ( "5","25JU20233", "2024-03-10"  ),
    ( "7","25JU20233", "2026-03-10"  ),
    ( "1","25JU20233", "2026-04-10"  ),
    ( "2","24JU20299", "2020-03-12"  ),
    ( "7","24JU20244", "2020-02-12"  ),
    ( "12","24JU20306", "2020-03-12"  ),
    ( "12","24JU20308", "2020-02-12"  );

COMMIT;