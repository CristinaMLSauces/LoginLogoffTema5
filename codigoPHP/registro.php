<?php
   
    if(isset($_REQUEST['cancelar'])){                                           //Si pulsa el boton cancelar volvemos al login
        header('Location: login.php');
        exit;
    }
 
    require_once '../core/210322ValidacionFormularios.php';                     //Incluimos la librería de validación para comprobar los campos del formulario
    require_once "../config/configDBPDO.php";                                   //Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores 

    define("OBLIGATORIO", 1);                                                   //Declaramos una constante de obligatorio para utilizar mas adelante en la validacion
    $entradaOK = true;

                                                                                //Para la validacion de los campos, declaramos el array de errores
    $aErrores = ['CodUsuario' => null,                                          //Lo inicializamos a null
                 'Descripcion' => null,
                 'Password' => null,
                 'PasswordRepetida' => null];

    if(isset($_REQUEST['aceptar'])){                                            //Si el usuario le ha dado a al boton de aceptar entonces
        
        $aErrores['CodUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['CodUsuario'], 15, 3, OBLIGATORIO); //Validamos cada campo con al libreria de validacion
        $aErrores['Descripcion'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['Descripcion'], 255, 3, OBLIGATORIO);
        $aErrores['Password'] = validacionFormularios::validarPassword($_REQUEST['Password'], 8, 3, 1, OBLIGATORIO); 
        $aErrores['PasswordRepetida'] = validacionFormularios::validarPassword($_REQUEST['PasswordRepetida'], 8, 3, 1, OBLIGATORIO);
        
        try{ 
            
            $miDB = new PDO(HOST,USER,PASSWORD);                                //Establecer una conexión con la base de datos 
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE


            $sqlUsuario = "Select * from T01_Usuario where T01_CodUsuario=:CodUsuario";  //Guardamos en una variable la consulta para validar el usuario mas adelante
            $consultaUsuario = $miDB->prepare($sqlUsuario);                     //Preparamos la consulta
            $parametrosUsuario = [":CodUsuario" => $_REQUEST['CodUsuario']];    //blindeamos lo que nos devuelve la consulta con lo que el usuario a introducido

            $consultaUsuario->execute($parametrosUsuario);                      //Ejecutamos los parametros
            $registro = $consultaUsuario->fetchObject();                        //Si se ha encontrado una linea devuelve un objeto
            
            if($consultaUsuario->rowCount()>0){                                 //Si la consulta es mayor a 1, significa que el usuario ya esta registrado
                $aErrores['CodUsuario'] = "El usuario ya existe";               //Guardamos el mensaje de error en el array de errores
            }
            if($_REQUEST['Password']!=$_REQUEST['PasswordRepetida']){           //Comprobamos que las dos contraseñas que ha introducido sean iguales
                $aErrores['PasswordRepetida']="Error, las contraseñas no coinciden"; //Si no lo son mostramos guaradmos el mensaje de error en el array;
            }
            
        }catch(PDOException $e){
            $error = $e->getCode();                                             //guardamos en la variable error el error que salta
            $mensaje = $e->getMessage();                                        //guardamos en la variable mensaje el mensaje del error que salta

            echo "ERROR $error";                                                //Mostramos el error
            echo "<p style='background-color: coral>Se ha producido un error! .$mensaje</p>"; //Mostramos el mensaje de error
        } finally {
           unset($miDB);                                                        //Cerramos la conexion con la base de datos
        }
        
        foreach ($aErrores as $campo => $error){                                //Recorre el array en busca de errores, con que haya uno entra
            if ($error != null){                
                $entradaOK = false;                                             //Y nos cambia la variable entrada a false
                $_REQUEST[$campo]="";                                           //Limpiamos los campos del formulario
            }
        }
        
    }else{
        $entradaOK = false;                                                     // Si el usuario no le ha dado al boton iniciar sesion, entradaok se queda en false hasta que lo rellene
    }
    if($entradaOK){                                                             // Si el usuario ha rellenado el formulario correctamente
        
        try{
            
            $miDB = new PDO(HOST,USER,PASSWORD);                                //Establece una conexión con la base de datos 
            $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE

            $sql = "Insert into T01_Usuario (T01_CodUsuario, T01_DescUsuario, T01_Password) values (:CodUsuario, :Descripcion, :Password)"; //Guardamos la consulta insert para insertar el nuevo usuario en la base de datos
            $consulta = $miDB->prepare($sql);                                   //Preparamos la consulta
            $parametros = [":CodUsuario" => $_REQUEST['CodUsuario'],            //Blindeamos los parametros de la tabala con lo que ha introducido el usuario
                           ":Descripcion" => $_REQUEST['Descripcion'],
                           ":Password" => hash("sha256", ($_REQUEST['CodUsuario'].$_REQUEST['Password']))]; //La password sera un resumen del codUsuario y la contraseña juntos

            $consulta->execute($parametros);                                    //Ejecutamos la consulta
            
            $sqlUpdate = "Update T01_Usuario set T01_NumConexiones = :NumConexiones, T01_FechaHoraUltimaConexion=:FechaHoraUltimaConexion where T01_CodUsuario=:CodUsuario"; //Guardamos la cosnulta de update para actualizar la tabal del nuevo user
            $consultaUpdate = $miDB->prepare($sqlUpdate);                       //Preparamos la consulta
            $parametrosUpdate = [":NumConexiones" => ($nConexiones+1),          //Blindeamos las conexiones añadiendole la primera
                                 ":FechaHoraUltimaConexion" => time(),          //Blindeamos la fecha con un timestamp
                                 ":CodUsuario" => $_REQUEST['CodUsuario']];   //Blindeamos la descripcion de usuario
            
            $consultaUpdate->execute($parametrosUpdate);                        //Ejecutamos los parametros
            
            session_start();                                                    //Iniciamos la sesión
            $_SESSION['usuarioDAW207LoginLogoffTema5']=$_REQUEST['CodUsuario']; //Almacenamos en una variable de sesión el codigo del usuario
            $_SESSION['FechaHoraUltimaConexionAnterior']=null;                  //Almacenamos la fecha hora de la ultima conexion en una variable de sesion
            
            header('Location: programa.php');                                   //Nos redirigimos al programa
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
        <link href="../webroot/css/estilo2.css" rel="stylesheet">
        <link rel="icon" type="image/png" sizes="16x16" href="../images/favicon-16x16.png">
    <title>Registro</title>
</head>
<body>
    <header>
        <h1>Login Logoff Tema 5</h1>
    </header>
    <h3>¡Registrate<br> ahora!</h3>
        <div class="box">
             <form name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" >
  
                <br>
                <div class="input-container">   
                    <label>Usuario</label>	
                        <input type="text" name="CodUsuario" value=
                        "<?php if($aErrores['CodUsuario'] == NULL && isset($_REQUEST['CodUsuario'])){ echo $_REQUEST['CodUsuario'];}?>">
                         <?php if ($aErrores['CodUsuario'] != null) { echo "  ⚠️".$aErrores['CodUsuario']."<br>"; } ?> <br>
                    
                        <br><br>
                </div>
                <div class="input-container">           
                    <label>Descripción del usuario</label>   
                    <input type="text" name="Descripcion" value=
                        "<?php if($aErrores['Descripcion'] == NULL && isset($_REQUEST['Descripcion'])){ echo $_REQUEST['Descripcion'];}?>">
                         <?php if ($aErrores['Descripcion'] != null) { echo "  ⚠️".$aErrores['Descripcion']."<br>"; } ?> <br>
                    
                        <br><br>
                </div>
                <div class="input-container">          
                    <label>Contraseña</label>   
                    <input type="Password" name="Password" value=
                        "<?php if($aErrores['Password'] == NULL && isset($_REQUEST['Password'])){ echo $_REQUEST['Password'];}?>">
                         <?php if ($aErrores['Password'] != null) { echo "  ⚠️".$aErrores['Password']."<br>"; } ?> <br>
                    
                        <br><br> 
                </div>
                <div class="input-container">     
                    <label>Repite la contraseña</label>   
                    <input type="Password" name="PasswordRepetida" value=
                        "<?php if($aErrores['PasswordRepetida'] == NULL && isset($_REQUEST['PasswordRepetida'])){ echo $_REQUEST['PasswordRepetida'];}?>">
                         <?php if ($aErrores['PasswordRepetida'] != null) { echo "  ⚠️".$aErrores['PasswordRepetida']."<br>"; } ?> <br>
                    
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