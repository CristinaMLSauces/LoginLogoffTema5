<?php
     
       require_once "../config/configDBPDO.php"; //Cogemos el archivo con los parametros de conexion segun estemos en casa en desarollo o explotacion caragara un archivo diferente
        
            try {
                //Establecer una conexión con la base de datos 
                $miDB = new PDO(HOST,USER,PASSWORD);
                //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE
                //Le ponemos de parametro - > PDO::ERRMODE_EXCEPTION. Cuando se produce un error lanza una excepción utilizando el manejador propio PDOException.
                $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $consulta = <<<EOD
                        CREATE TABLE IF NOT EXISTS T02_Departamento(
                            T02_CodDepartamento VARCHAR(3) PRIMARY KEY,
                            T02_DescDepartamento VARCHAR(255) NOT NULL,
                            T02_FechaCreacionDepartamento INT NOT NULL,
                            T02_VolumenNegocio FLOAT NOT NULL,
                            T02_FechaBajaDepartamento INT DEFAULT NULL
                        )ENGINE=INNODB;

                        CREATE TABLE IF NOT EXISTS T01_Usuario(
                            T01_CodUsuario VARCHAR(10) PRIMARY KEY,
                            T01_Password VARCHAR(64) NOT NULL,
                            T01_DescUsuario VARCHAR(255) NOT NULL,
                            T01_NumConexiones INT DEFAULT 0,
                            T01_FechaHoraUltimaConexion INT,
                            T01_Perfil enum('administrador', 'usuario') DEFAULT 'usuario',
                            T01_ImagenUsuario mediumblob NULL
                        )ENGINE=INNODB;
                EOD;
                
                $miDB->exec($consulta);
                
                echo "<h3> <span style='color: green;'>"."Tablas creadas correctamente</span></h3>";//Si no se ha producido ningún error nos mostrará "Conexión establecida con éxito"
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