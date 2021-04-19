<?php
  
    
    if(isset($_REQUEST['registrarse'])){
        header('Location: registro.php');
        exit;
    }
   
    require_once '../core/210322ValidacionFormularios.php';                     //Incluimos la librería de validación para comprobar los campos del formulario
    require_once "../config/configDBPDO.php";                              //Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 

    $entradaOK = true;              
    define("OBLIGATORIO", 1); 


    $aErrores = ['CodUsuario' => null,                                          //Creamos el array de errores
                 'Password' => null];                                           //Lo inicializamos a null

    if(isset($_REQUEST['aceptar'])){                                            //Si el usuario le ha dado al boton de iniciar sesion  entonces hace lo siguiente
        
        $aErrores['CodUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['CodUsuario'], 15, 3, OBLIGATORIO);  //Validamos que los campos sean correctos con al libreria de validacion
        $aErrores['Password'] = validacionFormularios::validarPassword($_REQUEST['Password'], 8, 3, 1, OBLIGATORIO);
        
        try{                                                                    //Vamos a validar primero que el usuario sea correcto
            
            $miDB = new PDO(HOST,USER,PASSWORD);                                //Establecer una conexión con la base de datos 
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE

            $consulta = "Select T01_Password from T01_Usuario where T01_CodUsuario=:CodUsuario";  //Creamos la consulta para buscar ese usuario en la tabla de usuarios
            $consultaUsuario = $miDB->prepare($consulta);                       //Preparamos la consulta
            $parametrosUsuario = [":CodUsuario" => $_REQUEST['CodUsuario']];

            $consultaUsuario->execute($parametrosUsuario);                      //Pasamos los parámetros a la consulta
            $oRegistro = $consultaUsuario->fetchObject();                       //nos devuelve la fila como un objeto
            
            if($consultaUsuario->rowCount()>0){                                 //Si la consulta devuelve por lo menos una fila
                $passwordEncriptado=hash("sha256", ($_REQUEST['CodUsuario'].$_REQUEST['Password'])); //Calculamos el resumen del nombre y la contraseña juntos y lo guardamos en $passwordEncriptado
                if($passwordEncriptado!=$oRegistro->T01_Password){               //Comparamos el resumen que hemos hecho con el que hay en la tabla, si es diferente 
                    $aErrores['Password'] = "Error";                   // La contraseña esta mal, y guardamos un mensaje de error
                }
            }else{                                                              //Si la consulta no devuelve ninguna fila el usuario esta mal
                    $aErrores['CodUsuario'] = "Error";                  //Entonces guardamos el error en el array de errores
           
            }
            
        }catch(PDOException $e){
            $error = $e->getCode();                                             //guardamos en la variable error el error que salta
            $mensaje = $e->getMessage();                                        //guardamos en la variable mensaje el mensaje del error que salta

            echo "ERROR $error";                                                //Mostramos el error
            echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error
        } finally {
           unset($miDB);                                                        //cerramos la conexion con la base de datos
        }
        

        foreach ($aErrores as $campo => $error){                                //Recorre el array en busca de errores, con que haya uno entra
            if ($error != null){                
                $entradaOK = false;                                             //Y nos cambia la variable entrada a false
                $_REQUEST[$campo]="";                                            //Limpiamos los campos del formulario
                
            }
        }
    }else{
        $entradaOK = false;                                                     // Si el usuario no le ha dado al boton iniciar sesion, entradaok se queda en false hasta que lo rellene
    }
    
    if($entradaOK){                                                             // Si el usuario ha rellenado el formulario correctamente rellenamos el array aFormulario con las respuestas introducidas por el usuario
        try{                                                                    //validamos que la CodUsuario sea correcta
            
            $miDB = new PDO(HOST,USER,PASSWORD);                                //Establecer una conexión con la base de datos 
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE


            $consulta = "Select T01_NumConexiones, T01_FechaHoraUltimaConexion from T01_Usuario where T01_CodUsuario=:CodUsuario"; //Preparamos la consulta, que nos va a mostrar los datos de ese usuario
            $resultadoConsulta = $miDB->prepare($consulta);                     //Preparamos la consulta
            $parametros = [":CodUsuario" => $_REQUEST['CodUsuario']];

            $resultadoConsulta->execute($parametros);                           //Ejecutamos la consulta
            $oRegistro = $resultadoConsulta->fetchObject();                      //Obtenemos el primer registro de la consulta

            $nConexiones = $oRegistro->T01_NumConexiones;                       //Almacenamos el numero de conexiones almacenado en la base de datos
            $fechaHoraUltimaConexion = $oRegistro->T01_FechaHoraUltimaConexion; //Almacenamos la fecha hora de la ultima conexion almacenada en la base de datos

            settype($nConexiones, "integer");                                   //Convertimos en entero el numero de conexiones devualto por la consulta
            
            $sqlUpdate = "Update T01_Usuario set T01_NumConexiones = :NumConexiones, T01_FechaHoraUltimaConexion=:FechaHoraUltimaConexion where T01_CodUsuario=:CodUsuario"; //Declaramos la consulta para actualizar el numero de conexiones
            $consultaUpdate = $miDB->prepare($sqlUpdate);                       //Preparamos la consulta
            $parametrosUpdate = [":NumConexiones" => ($nConexiones+1),
                                 ":FechaHoraUltimaConexion" => time(),
                                 ":CodUsuario" => $_REQUEST['CodUsuario']];
            $consultaUpdate->execute($parametrosUpdate);                        //Pasamos los parámetros a la consulta y la ejecutamos

            session_start();                                                    //Iniciamos la sesión
                $_SESSION['usuarioDAW207LoginLogoffTema5']=$_REQUEST['CodUsuario'];     //Almacenamos en una variable de sesión el codigo del usuario
                $_SESSION['FechaHoraUltimaConexionAnterior']=$fechaHoraUltimaConexion;  //Almacenamos la fecha hora de la ultima conexion en una variable de sesion
                header('Location: programa.php');                               //Y pasamos al programa
            exit;
            
            
        }catch(PDOException $e){
            $error = $e->getCode();                                             //guardamos en la variable error el error que salta
            $mensaje = $e->getMessage();                                        //guardamos en la variable mensaje el mensaje del error que salta

            echo "ERROR $error";                                                //Mostramos el error
            echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error
        } finally {
           unset($miDB);                                                        //cerramos la conexion con la base de datos
        }
    }else{                                                                      //Si el usuario no ha rellenado el formulario correctamente volvera a rellenarlo
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../webroot/css/estilo.css" rel="stylesheet">
        <link rel="icon" type="image/png" sizes="16x16" href="../images/favicon-16x16.png">
        <title>LoginLogoff</title>
    </head>
    <body>
        <header>
            <h1>Login Logoff Tema 5</h1>
        </header>
            <a class="home" href="../../proyectoDWES/index.php"><img src="../images/casa.png"/></a>
            
            <div class="box">   
                <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" >
                
                    <div class="input-container">   
                        <label>Usuario</label>	
                        <input type="text" name="CodUsuario" value=
                        "<?php if($aErrores['CodUsuario'] == NULL && isset($_REQUEST['CodUsuario'])){ echo $_REQUEST['CodUsuario'];}?>">
                        <?php if ($aErrores['CodUsuario'] != null) { echo "  ⚠️".$aErrores['CodUsuario']."<br>"; } ?> <br>
                    </div>    
                        <br><br>
                    <div class="input-container">
                         <label>Password</label>
                            <input type = "Password"  name = "Password" value=
                            "<?php if($aErrores['Password'] == NULL && isset($_REQUEST['Password'])){ echo $_REQUEST['Password'];}?>">
                            <?php if ($aErrores['Password'] != null) { echo "   ⚠️".$aErrores['Password']."<br>"; } ?> <br>
                       
                    </div> 
                        <br><br>
                        <input type="submit" value="INICIAR SESION" name="aceptar" class="enviar"> <br><br>
                        <input type="submit" value="REGISTRATE" name="registrarse" class="registro">
                    
                </form>
            </div>
         <footer>
            <p class="footer"> 2020-21 I.E.S. Los sauces. ©Todos los derechos reservados. Cristina Manjon Lacalle <p> 
            <a href="https://github.com/CristinaMLSauces/LoginLogoffTema5.git" target="_blank"> <img src="../images/git.png" class="logogit" /> </a>
        </footer>

    </body>
</html>

<?php
    }
?>
