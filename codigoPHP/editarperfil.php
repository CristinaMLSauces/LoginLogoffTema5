<?php
   
    session_start();                                                            //Recuperamos la sesion iniciada
    
    if (!isset($_SESSION['usuarioDAW207LoginLogoffTema5'])) {                   //Si el usuario no se ha autentificado
        header('Location: login.php');                                          //Redirigimos al usuario al ejercicio01.php para que se autentifique
        exit;
    }
    
    if(isset($_REQUEST['cambiarPassword'])){                                            //Si le ha dado al boton de salir
        header('Location: cambiarPassword.php');
        exit;
    }
    
    if(isset($_REQUEST['cancelar'])){                                           //Si el ha dado al boton de cancelar
        header('Location: programa.php');
        exit;
    }
   

    require_once '../core/210322ValidacionFormularios.php';                     //Incluimos la librería de validación para comprobar los campos del formulario
    require_once "../config/configDBPDO.php";                              //Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 
    
    if(isset($_REQUEST['eliminarCuenta'])){                                     //Si el usuario le ha dado al boton de eliminar cuenta
        try{
            $miDB = new PDO(HOST,USER,PASSWORD);                                //Establecer una conexión con la base de datos 
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE

            $sql = "DELETE from T01_Usuario where T01_CodUsuario=:CodUsuario";  //Guardamos la consulta para borrar toda la fila con ese cod usuario
            $consulta = $miDB->prepare($sql);                                   //Preparamos la consulta
            $parametros = [":CodUsuario" => $_SESSION['usuarioDAW207LoginLogoffTema5']];  //guardamos en parametros cual es la fila que coincide con ese codusuario

            $consulta->execute($parametros);                                    //Ejecutamos la consulta
            session_destroy();                                                  //Destruimos la sesion
            header('Location: login.php');                                      //Y volvemos al login
            exit;
            
    }catch(PDOException $e){
            $error = $e->getCode();                                             //guardamos en la variable error el error que salta
            $mensaje = $e->getMessage();                                        //guardamos en la variable mensaje el mensaje del error que salta

            echo "ERROR $error";                                                //Mostramos el error
            echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error
        } finally {
           unset($miDB); //cerramos la conexion con la base de datos
        }
    }
    
    try{
        $miDB = new PDO(HOST,USER,PASSWORD);                                    //Establecer una conexión con la base de datos 
        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);         //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE

        $sql = "Select T01_DescUsuario, T01_NumConexiones from T01_Usuario where T01_CodUsuario=:CodUsuario";     //Guardamos la consulta para verificar el codigo de usuario
        $consulta = $miDB->prepare($sql);                                      //Preparamos la consulta
        $parametros = [":CodUsuario" => $_SESSION['usuarioDAW207LoginLogoffTema5']];        //Blindeamos los parametros

        $consulta->execute($parametros);                                        //Ejecutamos la consulta            
        $registro = $consulta->fetchObject();                                   //Nos devuelve una fila como objeto
        
        $descripcionUsuario=$registro->T01_DescUsuario;                         //Guardamos la descripcion de usuario de esa fila
        $numConexiones=$registro->T01_NumConexiones;                            //Guardamos el numero de conexiones de esa fila

    }catch(PDOException $e){
            $error = $e->getCode();                                             //guardamos en la variable error el error que salta
            $mensaje = $e->getMessage();                                        //guardamos en la variable mensaje el mensaje del error que salta

            echo "ERROR $error";                                                //Mostramos el error
            echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error
    } finally {
       unset($miDB);                                                            //Cerramos la conexion con la base de datos
    }
    
    define("OBLIGATORIO", 1);                                                   //Definimos una constante para utilizar en la validacion
    $entradaOK = true;                                                          //Definimos una variable para comprobar cuando estan todos los campos bien

    $aErrores = ['errorDescripcion' => null,                                    //Declaro el array errores y lo inicializo a null
                 'errorImagen' => null];
 

    
    if(isset($_REQUEST['aceptar'])){                                            //Comprobamos que el usuario haya enviado el formulario
        $aErrores['errorDescripcion']= validacionFormularios::comprobarAlfaNumerico($_REQUEST['Descripcion'], 255, 3, OBLIGATORIO);

        if($_FILES['imagen']['tmp_name']!=null){                                //Si en la variable superglobal $File no esta vacia y tiene una imagen con nombre entonces
            $tipo = $_FILES['imagen']['type'];                                  //Nos devuelve los tipos de imagenes que hay
            if (($tipo == "image/gif") || ($tipo == "image/jpeg") || ($tipo == "image/jpg") || ($tipo == "image/png")){  //Validamos los tipos de imagen que voy a permitir
                $imagenSubida = file_get_contents($_FILES['imagen']['tmp_name']);  //Guardamos en la variable la imagen subida
            }else{
                $aErrores['errorImagen']="Formato incorrecto";                  //Si hubo algun error me guarda el mensaje en el array errores
            }
        }
                                                                                
        foreach ($aErrores as $campo => $error){                                //Recorre el array en busca de errores, con que haya uno entra
            if ($error != null){                
                $entradaOK = false;                                             //Y nos cambia la variable entrada a false
                $_REQUEST[$campo]="";                                           //Limpiamos los campos del formulario
            }
        }
         
        
    }else{
        $entradaOK = false;                                                     // Si el usuario no ha enviado el formulario asignamos a entradaOK el valor false para que rellene el formulario
    }
    if($entradaOK){                                                             // Si el usuario ha rellenado el formulario correctamente rellenamos el array aFormulario con las respuestas introducidas por el usuario
        try{
            $miDB = new PDO(HOST,USER,PASSWORD);                                //Establecer una conexión con la base de datos 
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE


            $sql = "Update T01_Usuario set T01_DescUsuario = :DescUsuario where T01_CodUsuario=:CodUsuario"; //Guardamos la consulta de actualizacion 
            $consulta = $miDB->prepare($sql);                                   //Preparamos la consulta
            $parametros = [":DescUsuario" => $_REQUEST['Descripcion'],          //Blindeamos los campos de la tabal con los campos que ha rellenado el usuario
                           ":CodUsuario" => $_SESSION['usuarioDAW207LoginLogoffTema5']];

            $consulta->execute($parametros);                                    //Ejecutamos la consulta
            
            if($imagenSubida!=null){                                            //Si hay una imagen subida entonces
                $sqlImagen = "Update T01_Usuario set T01_ImagenUsuario = :Imagen where T01_CodUsuario=:CodUsuario"; //Preparamos la consulta de actualizacion del campo de imagen en la tabla
                $consultaImagen = $miDB->prepare($sqlImagen);                   //Preparamos la consulta
                $parametrosImagen = [":Imagen" => $imagenSubida,                //Blindeamos los parametros de la tabla con los introducidos en el campo por el usuario
                                     ":CodUsuario" => $_SESSION['usuarioDAW207LoginLogoffTema5']];

                $consultaImagen->execute($parametrosImagen)                     ;//Ejecutamos la consulta
            }
            
            header('Location: programa.php');                                   //Com ya hicimos todo, volvemos al programa
            exit;
            
    }catch(PDOException $e){
            $error = $e->getCode();                                             //guardamos en la variable error el error que salta
            $mensaje = $e->getMessage();                                        //guardamos en la variable mensaje el mensaje del error que salta

            echo "ERROR $error";                                                //Mostramos el error
            echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error
        } finally {
           unset($miDB);                                                         //cerramos la conexion con la base de datos
        }
    }else{//Si el usuario no ha rellenado el formulario correctamente volvera a rellenarlo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar perfil</title>
    <link href="../webroot/css/estilo3.css" rel="stylesheet"> 
</head>
<body>
    <header>
        <h1>Login Logoff Tema 5</h1>
    </header>
        <h3>Editar perfil</h3>
        <div class="box">
            <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="formularioAlta" enctype="multipart/form-data">        
                
                <div class="input-container">   
                    <label>Usuario</label>	
                        <input type="text" name="CodUsuario" value="<?php echo $_SESSION['usuarioDAW207LoginLogoffTema5']; ?>" readonly>
                    <br><br>
                </div>
                
                <div class="input-container">           
                    <label>Descripción del usuario</label>   
                    <input type="text" name="Descripcion" value="<?php echo(isset($_REQUEST['Descripcion']) ? $_REQUEST['Descripcion'] : $descripcionUsuario); ?>">
                         
                    <br><br>
                </div>
                
                <div class="input-container">   
                    <label>Numero de conexiones</label>	
                        <input type="text" name="CodUsuario" value="<?php echo $numConexiones; ?>" readonly>
                    <br><br>
                </div>   
                
                <?php
                    if($numConexiones>1){
                ?>
                    <div class="input-container">   
                        <label>Ultima fecha de conexion</label>	
                            <input type="text" name="CodUsuario" value="<?php echo date('d/m/Y H:i:s',$_SESSION['FechaHoraUltimaConexionAnterior']);?>" readonly>
                    <br><br>
                    </div> 
                <?php
                    }
                ?>
                <div class="imagen">           
                    <label for="imagen">Imagen de perfil</label>   
                    <input  type="file" name="imagen" value=
                        "<?php if($aErrores['errorImagen'] == NULL && isset($_REQUEST['errorImagen'])){ echo $_REQUEST['errorImagen'];}?>">
                         <?php if ($aErrores['errorImagen'] != null) { echo "  ⚠️".$aErrores['errorImagen']."<br>"; } ?> <br>
                    <br><br>
                </div>
                
                    <input type="submit"  value="CAMBIAR CONTRASEÑA" name="cambiarPassword" class="contraseña">
                   
                    <br>
                   
        
                <div>
                    <input type="submit" value="Aceptar" name="aceptar" class="aceptar">
                    <input type="submit" value="Cancelar" name="cancelar" class="cancelar">
                </div>
                    <hr>
                    <input type="submit" value="ELIMINAR CUENTA" name="eliminarCuenta" class="eliminar">
                    <br><br>
                    
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