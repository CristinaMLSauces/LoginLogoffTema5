<?php
 
    session_start();//reanudamos la sesion existente
    
    if (!isset($_SESSION['usuarioDAW207LoginLogoffTema5'])) {//Si el usuario no se ha autentificado
        header('Location: login.php');//Redirigimos al usuario al ejercicio01.php para que se autentifique
        exit;
    }

//
//    if(isset($_REQUEST['detalles'])){
//        header('Location: detalles.php');
//        exit;
//    }
//    
//    if(isset($_REQUEST['editarPerfil'])){
//        header('Location: editarPerfil.php');
//        exit;
//    }
//    
    if(isset($_REQUEST['salir'])){
        session_destroy();
        header('Location: login.php');
        exit;
    }
    
    require_once '../core/210322ValidacionFormularios.php';                     //Incluimos la librería de validación para comprobar los campos del formulario
    require_once "../config/configDBPDO_CASA.php";                              //Incluimos el archivo confDBPDO.php para poder acceder al valor de las constantes de los distintos valores de la conexión 

    try{
        $miDB = new PDO(HOST,USER,PASSWORD);                                    //Establecer una conexión con la base de datos 
        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);         //La clase PDO permite definir la fórmula que usará cuando se produzca un error, utilizando el atributo PDO::ATTR_ERRMODE

        $sql = "Select T01_NumConexiones, T01_DescUsuario, T01_ImagenUsuario from T01_Usuario where T01_CodUsuario=:CodUsuario"; //Guardamos la consulta para saber si el usuario es correcto
        $consulta = $miDB->prepare($sql);                                       //Preparamos la consulta
        $parametros = [":CodUsuario" => $_SESSION['usuarioDAW207LoginLogoffTema5']]; //guardamos lo que nos devuelve en parametros

        $consulta->execute($parametros);//Ejecutamos la consulta
        $registro = $consulta->fetchObject();//Obtenemos el primer registro de la consulta

        $nConexiones=$registro->T01_NumConexiones;//Guardamos el número de conexiones del usuario en $nConexiones
        $descUsuario=$registro->T01_DescUsuario;//Guardamos la descripcion del usuario
        $imagenUsuario=$registro->T01_ImagenUsuario;//Guardamos la descripcion del usuario

    }catch(PDOException $excepcion){
        $errorExcepcion = $excepcion->getCode();//Almacenamos el código del error de la excepción en la variable $errorExcepcion
        $mensajeExcepcion = $excepcion->getMessage();//Almacenamos el mensaje de la excepción en la variable $mensajeExcepcion

        echo "<span style='color: red;'>Error: </span>".$mensajeExcepcion."<br>";//Mostramos el mensaje de la excepción
        echo "<span style='color: red;'>Código del error: </span>".$errorExcepcion;//Mostramos el código de la excepción
    } finally {
       unset($miDB); //cerramos la conexion con la base de datos
    }
    
?>
<!DOCTYPE html>
<html lang="es">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../webroot/css/estilo1.css" rel="stylesheet" type="text/css"/>
     <link rel="icon" type="image/png" sizes="16x16" href="../images/favicon-16x16.png">
     <title>LoginLogoff</title>
</head>
<body>
    <header>
        <h1>Estas dentro. Bienvenido</h1>
            <form name="formularioIdioma" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="formularioIdioma">
            <?php
                if($imagenUsuario!=null){
                echo '<img style="margin-rigth: 2px;" src = "data:image/png;base64,' . base64_encode($imagenUsuario) . '" width = "50px"/>';
                }
                ?>
<!--                <input type="submit" value="Editar Perfil" name="editarPerfil" id="editarPerfil">-->
                <input class="botones" type="submit" value="Cerrar Sesion" name="salir" id="cerrarSesion">
            </form>
    </header>
        <div class="info">
            <br><br>
                <h3><?php echo "Hola ".$descUsuario;?></h3>                     
                    <?php
                        if($nConexiones==1){                                    //Si es la primera vez que inicia sesion
                    ?>
                            <h3><?php echo "Bienvenido! Es tu primera vez por aqui." ?></h3>
                    <?php
                        }else{                                                  //Si no es la prinera vez que inicias sesion
                    ?>
                            <h3><?php echo "Te has conectado ".$nConexiones." veces" ?></h3>
                            <h3><?php echo "Tu ultima visita ha sido el ".date('d/m/Y H:i:s',$_SESSION['FechaHoraUltimaConexionAnterior']);?> </h3>
                    <?php   
                        }
                    ?> 
        </div>
        <footer>
                <p class="footer"> 2020-21 I.E.S. Los sauces. ©Todos los derechos reservados. Cristina Manjon Lacalle <p> 
                <a href="https://github.com/CristinaMLSauces" target="_blank"> <img src="../images/git.png" class="logogit" /> </a>
        </footer>
</body>
</html>
