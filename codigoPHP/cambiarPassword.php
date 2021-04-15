<?php
    session_start();                                                            //Reaunados la sesion que tenia abierta
   
    if (!isset($_SESSION['usuarioDAW207LoginLogoffTema5'])) {                   //Si el usuario no se ha autentificado
        header('Location: login.php');                                          //Lo mandamos al login
        exit;
    }
    
    if(isset($_REQUEST['cancelar'])){                                           //Si el usuario pulsa cancelar
        header('Location: editarperfil.php');                                   //Lo amndamos a editarperfil
        exit;
    }
    

    
    require_once '../core/210322ValidacionFormularios.php';                     //Incluimos la librería de validación para comprobar los campos del formulario
    require_once "../config/configDBPDO_CASA.php";                              //Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 


    define("OBLIGATORIO", 1);
    $entradaOK = true;
                                                                                //Declaramos el array de errores y lo inicializamos a null
    $aErrores = ['PasswordActual' => null,
                 'PasswordNueva' => null,
                 'PasswordRepetida' => null];

    if(isset($_REQUEST['aceptar'])){                                            //Si le ha dado al boton de aceptar entonces
        
        $aErrores['PasswordActual'] = validacionFormularios::validarPassword($_REQUEST['PasswordActual'], 8, 3, 1, OBLIGATORIO); //Validamos todas las password tanto la actual como la nueva
        $aErrores['PasswordNueva'] = validacionFormularios::validarPassword($_REQUEST['PasswordNueva'], 8, 3, 1, OBLIGATORIO);
        $aErrores['PasswordRepetida'] = validacionFormularios::validarPassword($_REQUEST['PasswordRepetida'], 8, 3, 1, OBLIGATORIO);
        
        try{
            $miDB = new PDO(HOST,USER,PASSWORD);                              //Establecer una conexión con la base de datos 
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE

            $sqlUsuario = "Select T01_Password from T01_Usuario where T01_CodUsuario=:CodUsuario"; //Guardamos la consulta para comprobar que la contraseña actual eta bien
            $consultaUsuario = $miDB->prepare($sqlUsuario);                     //Preparamos la consulta
            $parametrosUsuario = [":CodUsuario" => $_SESSION['usuarioDAW207LoginLogoffTema5']];         //Blindeamos la contraseña de la tabala y la introducida

            $consultaUsuario->execute($parametrosUsuario);                      //Ejecutamos la consulta
            $registro = $consultaUsuario->fetchObject();                        //Obtenemos la fila de la tabla como objeto
            $passwordUsuario = $registro->T01_Password;                         //Obtenemos el valor de esa fila
            $passwordEncriptada=hash("sha256",($_SESSION['usuarioDAW207LoginLogoffTema5'].$_REQUEST['PasswordActual'])); //Calculamos el resumen de la contraseña introducida
            
            if($passwordEncriptada!=$passwordUsuario){                          //Si los dos resumenes son iguales significa que las dos contraseñas son iguales, si no
                $aErrores['PasswordActual'] = "Contraseña incorrecta";          //Guardamos un mensaje de error
            }
            
            if($_REQUEST['PasswordNueva']!=$_REQUEST['PasswordRepetida']){      //Si la contraseña nueva no coincide en los dos campos
                $aErrores['PasswordRepetida']="Error, las contraseñas no coinciden";  //Guardamos un mensaje de error
            }
            
         }catch(PDOException $e){
            $error = $e->getCode();                                             //guardamos en la variable error el error que salta
            $mensaje = $e->getMessage();                                        //guardamos en la variable mensaje el mensaje del error que salta

            echo "ERROR $error";                                                //Mostramos el error
            echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error
        } finally {
           unset($miDB); //cerramos la conexion con la base de datos
        }
       
        foreach ($aErrores as $campo => $error){                                //Recorre el array en busca de errores, con que haya uno entra
            if ($error != null){                
                $entradaOK = false;                                             //Y nos cambia la variable entrada a false
                $_REQUEST[$campo]="";                                            //Limpiamos los campos del formulario
                
            }
        }
        
    }else{
        $entradaOK = false;                                                     // Si el usuario no ha enviado el formulario asignamos a entradaOK el valor false para que rellene el formulario
    }
    if($entradaOK){                                                             // Si el usuario ha rellenado el formulario correctamente rellenamos el array aFormulario con las respuestas introducidas por el usuario
        try{
            $miDB = new PDO(HOST,USER,PASSWORD);                                //Establecer una conexión con la base de datos 
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE

            $sql = "Update T01_Usuario set T01_Password = :Password where T01_CodUsuario=:CodUsuario"; //Guardamos la consulta de actualizacion
            $consulta = $miDB->prepare($sql);                                       //Preparamos la consulta
            $parametros = [":Password" => hash("sha256", ($_SESSION['usuarioDAW207LoginLogoffTema5'].$_REQUEST['PasswordNueva'])), //Calculamos el resumen de la nueva contraseña
                           ":CodUsuario" => $_SESSION['usuarioDAW207LoginLogoffTema5']]; //Blindeamos el usuario para que coincida

            $consulta->execute($parametros);                                    //Ejecutamos la consulta para guardar la nueva contraseña
            
            header('Location: editarperfil.php');                               //Cuando acaba nos redirige a editar perfil
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
    <title>Cambiar password</title>
    <link href="../webroot/css/estilo5.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="16x16" href="../images/favicon-16x16.png"> 
</head>
<body>
    <header>
        <h1>Login Logoff Tema 5</h1>
    </header>
   
        <div class="box">
            <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="formularioAlta">
                <h3>Cambiar<br>password</h3>
                <br>
                <div>
                    <div class="input-container">
                         <label>Password actual</label>
                            <input type = "password"  name = "PasswordActual" value=
                            "<?php if($aErrores['PasswordActual'] == NULL && isset($_REQUEST['PasswordActual'])){ echo $_REQUEST['PasswordActual'];}?>">
                            <?php if ($aErrores['PasswordActual'] != null) { echo "   ⚠️".$aErrores['PasswordActual']."<br>"; } ?> <br>
                    </div>
                    <br><br>
                    <div class="input-container">
                         <label>Password nueva</label>
                            <input type = "password"  name = "PasswordNueva" value=
                            "<?php if($aErrores['PasswordNueva'] == NULL && isset($_REQUEST['PasswordNueva'])){ echo $_REQUEST['PasswordNueva'];}?>">
                            <?php if ($aErrores['PasswordNueva'] != null) { echo "   ⚠️".$aErrores['PasswordNueva']."<br>"; } ?> <br>
                    </div>  
                    <br><br>
                    <div class="input-container">
                         <label>Password nueva</label>
                            <input type = "password"  name = "PasswordRepetida" value=
                            "<?php if($aErrores['PasswordRepetida'] == NULL && isset($_REQUEST['PasswordRepetida'])){ echo $_REQUEST['PasswordRepetida'];}?>">
                            <?php if ($aErrores['PasswordRepetida'] != null) { echo "   ⚠️".$aErrores['PasswordRepetida']."<br>"; } ?> <br>
                    </div> 

                    <br><br>
                </div>
                <div>
                    <input type="submit" value="Aceptar" name="aceptar" class="aceptar">
                    <input type="submit" value="Cancelar" name="cancelar" class="cancelar">
                </div>
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
