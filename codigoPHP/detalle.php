<?php

    session_start();                                                            //Reanudamos la sesión existente
    
    if(isset($_REQUEST['volver'])){                                             //Si el usuario pulsa el botón de salir
        header('Location: programa.php');                                       //Redirigimos al usuario al programa de nuevo
        exit;
    }
    
    if (!isset($_SESSION['usuarioDAW207LoginLogoffTema5'])) {                   //Si el usuario no se ha autentificado
        header('Location: login.php');                                          //Redirigimos al usuario al login para que se autentifique
        exit;
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="../webroot/css/estilo4.css" rel="stylesheet" type="text/css"/>
        <link rel="icon" type="image/png" sizes="16x16" href="../images/favicon-16x16.png"/>
        <title>LoginLogoff</title>
    </head>
    <body>
        <header>
            <form  name="formulario" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                <button type="submit" name='volver' value="volver" class="volver">VOLVER</button>
            </form>
            <h1>Estas viendo las variables superglobales.</h1>

        </header>
        
        <h3>$_COOKIE</h3>
        <div>  
        <?php
            foreach ($_COOKIE as $key => $value) {
                    echo $key."  ";
                    echo $value."<br>";
            }
        ?>
        </div>
        <h3>$_SESSION</h3>
        <div>     
        <?php
            if(isset($_SESSION)){
                foreach ($_SESSION as $key => $value) {
                    echo $key." ";
                    echo $value."<br>";
                }
            }
        ?>
        </div>
        <h3>$_SERVER</h3>
        <div>    
        <?php
            if(isset($_SERVER)){
                foreach ($_SERVER as $key => $value) {
                    echo $key." ";
                    echo $value."<br>";
                }
            }
        ?>
        </div>
        <h3>$_GET</h3>
        <div> 
        <?php
            foreach ($_GET as $key => $value) {
                echo $key." ";
                echo $value."<br>";
            }
        ?>
        </div>
        <h3>$_POST</h3>
        <div>
        <?php
            foreach ($_POST as $key => $value) {
                echo $key." ";
                echo $value."<br>";
            }
        ?>
        </div>
        <h3>$_FILES</h3>
        <div>   
        <?php
            foreach ($_FILES as $key => $value) {
                echo $key." ";
                echo $value."<br>";
            }
        ?>
        </div>
        <h3>$_REQUEST</h3>
        <div>    
        <?php
            foreach ($_REQUEST as $key => $value) {
                echo $key." ";
                echo $value."<br>";
            }
        ?>
        </div>
        <h3>$_ENV</h3>
        <div> 
        <?php
            foreach ($_ENV as $key => $value) {
                echo $key." ";
                echo $value."<br>";
            }
        ?>
        </div>
        <footer>
                <p class="footer"> 2020-21 I.E.S. Los sauces. ©Todos los derechos reservados. Cristina Manjon Lacalle <p> 
                <a href="https://github.com/CristinaMLSauces/LoginLogoffTema5.git" target="_blank"> <img src="../images/git.png" class="logogit" /> </a>
        </footer>
    
    </body>
</html>