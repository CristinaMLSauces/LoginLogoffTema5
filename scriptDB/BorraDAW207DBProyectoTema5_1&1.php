<?php
       
        require_once "../config/configDBPDO.php"; //Cogemos el archivo con los parametros de conexion segun estemos en casa en desarollo o explotacion caragara un archivo diferente
        
            try {
                //Establecer una conexión con la base de datos 
                $miDB = new PDO(HOST,USER,PASSWORD);
                //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE
                //Le ponemos de parametro - > PDO::ERRMODE_EXCEPTION. Cuando se produce un error lanza una excepción utilizando el manejador propio PDOException.
                $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                //Guardamos la consulta con heredoc
                $consulta = <<<EOD
                        DROP TABLE T02_Departamento;
                        DROP TABLE T01_Usuario;
                EOD;
                
                $miDB->exec($consulta);          //Ejecutamos la consulta
                
                echo "<h3> <span style='color: green;'>"."Tablas borrada</span></h3>";//Si todo fue bien mostrara un mensaje de de que se borro cone exito
            }
            catch (PDOException $excepcion) {
                $error = $e->getCode();                                         //guardamos en la variable error el error que salta
                $mensaje = $e->getMessage();                                    //guardamos en la variable mensaje el mensaje del error que salta
                
                echo "ERROR $error";                                            //Mostramos el error
                echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error

            } finally {
                unset($miDB);
            }
?>


