<?php
            
        require_once "../config/configDBPDO.php"; //Cogemos el archivo con los parametros de conexion segun estemos en casa en desarollo o explotacion caragara un archivo diferente
        
            try {
                 //Establecer una conexión con la base de datos 
                $miDB = new PDO(HOST,USER,PASSWORD);
                //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE
                //Le ponemos de parametro - > PDO::ERRMODE_EXCEPTION. Cuando se produce un error lanza una excepción utilizando el manejador propio PDOException.
                $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                
                $consulta = <<<EOD
                        INSERT INTO T02_Departamento(T02_CodDepartamento, T02_DescDepartamento, T02_FechaCreacionDepartamento, T02_VolumenNegocio) VALUES
                            ('INF', 'Departamento de informatica',1606156754, 5),
                            ('VEN', 'Departamento de ventas',1606156754, 8),
                            ('CON', 'Departamento de contabilidad',1606156754, 9),
                            ('MAT', 'Departamento de matematicas',1606156754, 8),
                            ('MKT', 'Departamento de marketing',1606156754, 1);
                        
                        INSERT INTO T01_Usuario(T01_CodUsuario, T01_DescUsuario, T01_Password) VALUES
                            ('nereaa','Nerea Alvarez',SHA2('nereaapaso',256)),
                            ('miguel','Miguel Angel Aranda',SHA2('miguelpaso',256)),
                            ('bea','Beatriz Merino',SHA2('beapaso',256)),
                            ('nerean','Nerea Nuevo',SHA2('nereanpaso',256)),
                            ('cristinam','Cristina Manjon',SHA2('cristinampaso',256)),
                            ('susana','Susana Fabian',SHA2('susanapaso',256)),
                            ('sonia','Sonia Anton',SHA2('soniapaso',256)),
                            ('elena','Elena de Anton',SHA2('elenapaso',256)),
                            ('nacho','Nacho del Prado',SHA2('nachopaso',256)),
                            ('raul','Raul Nuñez',SHA2('raulpaso',256)),
                            ('luis','Luis Puente',SHA2('luispaso',256)),
                            ('arkaitz','Arkaitz Rodriguez',SHA2('arkaitzpaso',256)),
                            ('rodrigo','Rodrigo Robles',SHA2('rodrigopaso',256)),
                            ('javier','Javier Nieto',SHA2('javierpaso',256)),
                            ('cristinan','Cristina Nuñez',SHA2('cristinanpaso',256)),
                            ('heraclio','Heraclio Borbujo',SHA2('heracliopaso',256)),
                            ('amor','Amor Rodriguez',SHA2('amorpaso',256)),
                            ('antonio','Antonio Jañez',SHA2('antoniopaso',256)),
                            ('leticia','Leticia Nuñez',SHA2('leticiapaso',256));
EOD;
                
                $miDB->exec($consulta);
                
                echo "<h3> <span style='color: green;'>"."Valores insertados</span></h3>";//Si todo fue bien mostrara un mensaje de de que se inserto cone exito
            }
            catch (PDOException $e) {
                $error = $e->getCode();                                         //guardamos en la variable error el error que salta
                $mensaje = $e->getMessage();                                    //guardamos en la variable mensaje el mensaje del error que salta
                
                echo "ERROR $error";                                            //Mostramos el error
                echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error
            } finally {
                unset($miDB);
            }
?>
