<?php
/** 
 * Shortcode para el Gestor de Reservas:
 * Invocación de un panel que permite a los profesores realizar la reserva de aulas 
 * Requiere autenticación mediante ldap
 */

/* Función principal que genera el cuerpo del shortcode */
function shortcode_login($atts) {
    load_javascript_crear_reserva();

    error_reporting(0); //desactivamos los errores por seguridad
    // Iniciamos y configuramos la sesion con ldap
    session_start();
    $_SESSION['auth']=0;
    if($_SESSION['controlUsers_error']!=1 && $_SESSION['controlUsers_error']!=2){
	$_SESSION['controlUsers_error']=0;
    }
    // Fin del inicio de sesión -----------------
    if($_POST["logued"]) {
        form();
    }
    else {
      if ($_POST["login"]) {  // Si se ha iniciado sesión, comprobamos los credenciales
        $_SESSION['user']=$_POST['user'];   // Usuario
        $_SESSION['pass']=$_POST['pass'];   // Contraseña
        
        // Añadimos las credenciales de ldap
        $servidor_LDAP = database::get_opcion("ldap");
        $dn = database::get_opcion("dn");
        
        $uid_user = 'uid='.$_SESSION['user'];
        $usuario_LDAP = $uid_user . "," . $dn;
        $contrasena_LDAP = $_SESSION['pass'];
        $conectado_LDAP = ldap_connect($servidor_LDAP);
        $_SESSION['conexion']=$conectado_LDAP;
        
        ldap_set_option($conectado_LDAP, LDAP_OPT_PROTOCOL_VERSION, 3); // Comprobamos la conexión
        
        if ($conectado_LDAP) {  // Si hay conexión con ldap
            // Introducimos los datos de la consola y comprobamos
            $atrib=array("gidnumber");
            // Obtenemos el valor de la contraseña ldap
            global $wpdb;    
            $passwordldap = $wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"passwordldap\"");
            $usuarioldap = $wpdb->get_var("SELECT valor FROM reservas_opciones WHERE nombre=\"usuarioldap\"");
            
            $sr=ldap_search($conectado_LDAP, $dn, "(&(uid=".$_SESSION['user'].")(objectclass=inetOrgPerson))");
            $info = ldap_get_entries($conectado_LDAP, $sr);
            //$cont = ldap_count_entries($conectado_LDAP, $sr);
            if($info['count'] <= 0) {   // Si no se han introducido datos: error
                $_SESSION['controlUsers_error']=1;
                login_form();
                echo "Por favor, introduzca sus credenciales para iniciar sesión.";
            }
            else {
                $autenticado_LDAP = @ldap_bind($conectado_LDAP, $info[0]["dn"], $contrasena_LDAP);
                if($autenticado_LDAP) { // Si los datos son correctos, entramos
                    $_SESSION['auth']=1;
                    $_SESSION['controlUsers_error']=0;
                    form();
                        
                }
                else { // Error como caso para todas las demás situaciones
                    $_SESSION['controlUsers_error']=1;
                    login_form();
                    echo "$autenticado_LDAP";
                    echo "El usuario o la contraseña son incorrectos<br>";
                }
            }
        }
        else {
            echo "No se ha podido realizar la conexión con el servidor<br>";
        }
    }
    // Si no se ha iniciado sesión, cargamos el formulario de inicio
    else { 
        login_form();
    }
    ldap_close($conectado_LDAP);
    return null;  
    }
}
add_shortcode( 'login', 'shortcode_login');    //registramos el shortcode en el sistema Wordpress

/* Función que pinta en pantalla el formulario de inicio de sesión de la aplicación */
function login_form() {
    ?>
    <div style="text-align:justify">
        Bienvenido al sistema de reserva de aulas del Laboratorio del Departamento de Informática de la
        Universidad Carlos III de Madrid. Desde esta aplicación usted podrá realizar su solicitud para
        de reserva de alguna de las aulas que gestiona el laboratorio.<br/><br/>Para entrar, inicie sesión
        con su correo de pas.<br/><br/>
    </div>
    <form id="formulario" method="post" name="formulario">
        Usuario<br>
        <input type="text" name="user"><br><br>
        Contraseña<br>
        <input type="password" name="pass"><br><br>
        <input type="hidden" name="login" value="true">
        <input type="submit" value="Iniciar Sesión">
    </form>
    <?php
}

function form() {
    $modo = $_POST["modo"];
    if($modo=="pagina2") {
        ?>
        <!DOCTYPE html>
        <html>
            <body>
                <div class="titulo">Paso 2 de 3: Selección de horario</div>            
                <form action="<?php echo get_permalink(); ?>" method="POST">
                    <?php
                    $asignatura = $_POST['asignatura'];
                    $grupo = $_POST['grupo'];
                    $tipo_reserva = $_POST['tipo_reserva'];
                    $profesor = $_POST['profesor'];

                    echo "<input type=\"hidden\" name=\"asignatura\" value=\"$asignatura\">";
                    echo "<input type=\"hidden\" name=\"logued\" value=true\">";
                    echo "<input type=\"hidden\" name=\"tipo_reserva\" value=\"$tipo_reserva\">";

                    if($tipo_reserva=='puntual') {
                        $fecha = $_POST['fecha'];
                        $dia = obtener_dia_semana($fecha);
                        $cuatrimestre = obtener_cuatrimestre($fecha);
                    }
                    elseif ($tipo_reserva=='cuatrimestral') {
                        $dia = $_POST['dia'];
                        $cuatrimestre = $_POST['cuatrimestre'];
                        if($cuatrimestre==0) {
                            $fecha = date(Y).'-07-01';
                        }
                        else if($cuatrimestre==1) {
                            $fecha = date(Y).'-09-01';
                        }
                        else if($cuatrimestre==2) {
                            $fecha = date(Y).'-01-01';
                        }
                    }
                    echo "<input type=\"hidden\" name=\"fecha\" value=\"$fecha\">";
                    echo "<input type=\"hidden\" name=\"dia\" value=\"$dia\">";
                    echo "<input type=\"hidden\" name=\"cuatrimestre\" value=\"$cuatrimestre\">";
                    echo "<input type=\"hidden\" name=\"profesor\" value=\"$profesor\">";
                    echo "<div style=\"width:100%;float:left\">";
            if($tipo_reserva=='puntual') {
                echo "A continuación se muestra la ocupación de las aulas el día " . $_POST['fecha'].":<br/><br/>";
            }
            if($tipo_reserva=='cuatrimestral') {
                echo "A continuación se muestra la ocupación de las aulas los " . $_POST['dia'].":<br/><br/>";
            }
            echo "<div id='calendario'>";
            echo load_html_preview($dia);
            echo "</div></div>";
                    ?>
                    
                    <div class="columna-1" style="padding:10px;box-shadow: 10px 10px 5px #888888;border: 1px solid;width:200px;margin-left:72.5%;position:fixed;background-color:white;">
                        Grupo <br/>
                            <?php echo generar_seleccion_grupos($_POST['asignatura']); ?><br/>
                            <i>Grupo de la asignatura que va a aprovechar dicha reserva.</i><br/><br/>
                        Aula <br/>
                            <?php echo generar_seleccion_aula(); ?><br/>
                            <i>Aula que se desea reservar. Tenga en cuenta el número de puestos de trabajo disponibles en cada aula a la hora de realizar la reserva.</i><br/><br/>
                        Hora<br/>
                            <input type="time" name="hora_inicial" value="09:00" required> - 
                            <input type="time" name="hora_final" value="11:00" required><br/>
                            <i>Hora en la que se desea realizar la reserva</i><br/><br/>
                        <!--<p>
                            Para comprobar que todos los datos introducidos son correctos y realizar la petición de su reserva
                            haga click en "Enviar solicitud de reserva". Tenga en cuenta que haber solicitado la reserva no significa que esta le 
                            haya sido concedida. El equipo del Laboratorio del Departamento de Informática se pondrá en contacto
                            con usted en breve para comunicarle la resolución de su reserva.
                        </p>-->
                        <input type="hidden" name="modo" value="pagina3">
                        <input type="button" onclick="javascript:history.back()" class="button" value="Atrás">
                        <input type="submit" value="Siguiente">
                    </div>
                </form>
            </body>
        </html>
        <?php
    }
    elseif($modo=="pagina3") {
                ?>
        <!DOCTYPE html>
        <html>
            <body>
                 <div class="titulo">Paso 3 de 3: Confirmar datos</div>            
                <form action="<?php echo get_permalink(); ?>" method="POST">
                    <?php
                        $asignatura = $_POST['asignatura'];
                        $grupo = $_POST['grupo'];
                        $tipo_reserva = $_POST['tipo_reserva'];
                        if($tipo_reserva=='puntual') {
                            $fecha = $_POST['fecha'];
                            $dia = obtener_dia_semana($fecha);
                            $cuatrimestre = obtener_cuatrimestre($fecha);
                        }
                        elseif ($tipo_reserva=='cuatrimestral') {
                            $dia = $_POST['dia'];
                            $cuatrimestre = $_POST['cuatrimestre'];
                            if($cuatrimestre==0) {
                                $fecha = date(Y).'-07-01';
                            }
                            else if($cuatrimestre==1) {
                                $fecha = date(Y).'-09-01';
                            }
                            else if($cuatrimestre==2) {
                                $fecha = date(Y).'-01-01';
                            }
                        }
                        $aula = $_POST['aula'];
                        $hora_inicial = $_POST['hora_inicial'];
                        $hora_final = $_POST['hora_final'];
                        $profesor = $_POST['profesor'];

                        echo "<input type=\"hidden\" name=\"asignatura\" value=\"$asignatura\">";
                        echo "<input type=\"hidden\" name=\"grupo\" value=\"$grupo\">";
                        echo "<input type=\"hidden\" name=\"tipo_reserva\" value=\"$tipo_reserva\">";
                        echo "<input type=\"hidden\" name=\"fecha\" value=\"$fecha\">";
                        echo "<input type=\"hidden\" name=\"dia\" value=\"$dia\">";
                        echo "<input type=\"hidden\" name=\"cuatrimestre\" value=\"$cuatrimestre\">";
                        echo "<input type=\"hidden\" name=\"profesor\" value=\"$profesor\">";
                        echo "<input type=\"hidden\" name=\"aula\" value=\"$aula\">";
                        echo "<input type=\"hidden\" name=\"hora_inicial\" value=\"$hora_inicial\">";
                        echo "<input type=\"hidden\" name=\"hora_final\" value=\"$hora_final\">";
                    ?>
                    <div style="font-size:15px">
                        <b>Profesor: </b> <?php echo $profesor ?><br/>
                        <b>Asignatura: </b> <?php echo $asignatura ?><br/>
                        <b>Grupo: </b> <?php echo $grupo ?><br/>
                        <b>Tipo de Reserva: </b> <?php echo $tipo_reserva ?><br/>
                        <?php
                            if($tipo_reserva=='puntual') {
                                echo "<b>Fecha: </b>" . $fecha . "<br/>";
                            }
                            else {
                                echo "<b>Día: </b>Todos los " . $dia . " del ". $cuatrimestre ." cuatrimestre" . "<br/>";
                            }
                        ?>
                        <b>Aula: </b> <?php echo $aula ?><br/>
                        <b>Horario: </b> de <?php echo $hora_inicial ?> a <?php echo $hora_final ?><br/><br/><br/>
                    </div>
                    <p style="text-align:justify">
                        Para comprobar que todos los datos introducidos son correctos y realizar la petición de su reserva
                        haga click en "Enviar solicitud". Tenga en cuenta que haber solicitado la reserva no significa que esta le 
                        haya sido concedida. El equipo del Laboratorio del Departamento de Informática se pondrá en contacto
                        con usted en breve para comunicarle la resolución de su reserva.
                    </p>
                    <input type="hidden" name="modo" value="insertar">
                    <input type="hidden" name="logued" value=true">
                    <input type="button" onclick="javascript:history.back()" class="button" value="Atrás">
                    <input type="submit" value="Enviar Solicitud">
                </form>
            </body>
        </html>
        <?php
    }
    elseif($modo=="insertar") {
        /* Vamos recogiendo las distintas variables para la inserción en la BBDD */
            $confirmada = 0; // Las reservas solicitadas nunca son confirmadas
            $asignatura = $_POST['asignatura'];
            $grupo = $_POST['grupo'];
            $profesor = $_POST['profesor'];
            $aula = $_POST['aula'];
            $tipo_reserva = $_POST['tipo_reserva'];
            $hora_inicial = $_POST['hora_inicial'];
            $hora_final = $_POST['hora_final'];

            if ($tipo_reserva=='puntual') { // Si es puntual solo nos interesa la fecha
                $fecha = $_POST['fecha'];
                $dia = obtener_dia_semana($fecha);
                $cuatrimestre = obtener_cuatrimestre($fecha);
            }
            elseif ($tipo_reserva=='cuatrimestral') {   // Si es cuatrimestral, recogemos el dia y el cuatrimestre
                $dia = $_POST['dia'];
                $cuatrimestre = $_POST['cuatrimestre'];
                if($cuatrimestre==0) {
                    $fecha = date(Y).'-07-01';
                }
                else if($cuatrimestre==1) {
                    $fecha = date(Y).'-09-01';
                }
                else if($cuatrimestre==2) {
                    $fecha = date(Y).'-01-01';
                }
            }
            /* Fin de la recogida de parámetros ------------------------------------ */
            
           /* Obtenemos los valores de las claves ajenas de la bbdd --------------- */
            $asignatura_b = database::get_asignatura("nombre",$asignatura);
            $asignatura_id = $asignatura_b[0]->id;

            $aula_b = database::get_aulas("nombre",$aula);
            $aula_id = $aula_b[0]->id;

            if($tipo_reserva=='cuatrimestral') $tipo_cod = 0;
            if($tipo_reserva=='puntual') $tipo_cod = 1;

            /* Fin de la obtención de valores -------------------------------------- */
            /* Realizamos la inserción en la base de datos de wordpress ------------ */
            $id_correo = database::set_reserva($confirmada,$asignatura_id,$grupo,$profesor,$aula_id,$tipo_cod,$dia,$cuatrimestre,$fecha,$hora_inicial,$hora_final);
            /* Fin de la inserción ------------------------------------------------- */
            
                        /* Enviamos correo de aviso al laboratorio ----------------------------- */
            /* Construimos el mensaje del correo electronico ----------------------- */
            $message = "<h2>Solicitud de correo para el aula $aula</h2> 
                        <p>Se ha realizado la solicitud de una nueva reserva a través del
                        formulario de la página del laboratorio con los siguientes detalles:</p>
                        <b>Profesor: </b> ".database::get_profesor($profesor)."<br/>
                        <b>Asignatura: </b>$asignatura<br/>
                        <b>Grupo: </b> $grupo<br/>
                        <b>Tipo de Reserva: </b> $tipo_reserva<br/>";
            if($tipo_reserva=='puntual') {
                $message.= "<b>Fecha: </b> $fecha<br/>";
            }
            else {
                $message.= "<b>Día: </b>Todos los $dia del $cuatrimestre cuatrimestre<br/>";
            }
            global $wpdb;
            $message.= "<b>Aula: </b> $aula<br/>
                        <b>Horario: </b> de $hora_inicial a $hora_final<br/><br/><br/>
                        Puede confirmar directamente esta reserva en el siguiente enlace: 
                        <a href=".network_site_url( '/' )."wp-admin/admin.php?page=reservas_prueba_page&id=$id_correo>
                        Confirmar Reserva</a>";
            
            /* Enviamos el correo */
            add_filter( 'wp_mail_content_type', 'set_html_content_type' );
            wp_mail( 'system@mail.com', "Solicitud de reserva del aula ". $aula, $message, "From:Sistema de reserva de aulas informáticas <system@mail.com>" );
            remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
            /* Fin del envío de correo --------------------------------------------- */
            
             ?>
                <!DOCTYPE html>
                <html>
                    <body>
                        <p style="text-align:justify">Su solicitud ha sido enviada correctamente. El personal
                        del laboratorio se pondrá en contacto con usted para notificarle si su reserva queda
                        confirmada.</p>            
                    </body>
                </html>
            <?php
            
    }
    else {
        ?>
    <!DOCTYPE html>
    <html>
        <body>
            <div class="titulo">Solicitar reserva</div>            
            <p style="text-align:justify">Bienvenido al sistema de solicitud de reservas para las aulas del Laboratorio del Departamento de
            Informática. El siguiente formulario le ayudará a tramitar su reserva de la forma más sencilla 
            posible:</p>            
            <form action="<?php echo get_permalink(); ?>" method="POST">
                Profesor <br/>
                
                    <input type="text" name="profesor_name" value="<?php echo database::get_profesor($_POST['user']); ?>" disabled><br/>
                    <input type="hidden" name="profesor" value="<?php echo $_POST['user'];?>">

                    <i>Nombre del profesor que impartirá la asignatura o, en su defecto, del coordinador de la asignatura.</i><br/><br/>
                Asignatura<br/>
                    <?php echo generar_seleccion_asignatura(); ?><br/>
                    <i>Asignatura que se impartirá durante la sesión de la reserva.</i><br/><br/>
                Tipo de reserva <br/>
                    <select name="tipo_reserva" onchange="tipoReserva(this)">
                        <option value="puntual">Puntual</option>
                        <option value="cuatrimestral">Cuatrimestral</option>
                    </select><br/>
                    <i>Indica la periodicidad de la reserva: la puntual solo se hará efectiva para un único día, mientras que la cuatrimestral reserva el aula para todo el cuatrimestre.</i><br/><br/>
                <div id="puntual">
                    Fecha <br/>
                        <input type="date" name="fecha" id="fecha" required> <br/>
                        <i>Indique la fecha en que se desea realizar la reserva.</i><br/><br/>
                </div> 
                <div id="cuatrimestral" style='display:none;'>
                    Fecha <br/>
                        Todos los <select name="dia">
                            <option value="Lunes">Lunes</option>
                            <option value="Martes">Martes</option>
                            <option value="Miércoles">Miércoles</option>
                            <option value="Jueves">Jueves</option>
                            <option value="Viernes">Viernes</option>
                        </select> del 
                        <select name="cuatrimestre">
                            <option value="1">Primer Cuatrimestre</option>
                            <option value="2">Segundo Cuatrimestre</option>
                        </select><br/>
                        <i>Indique la fecha en que se desea realizar la reserva.</i><br/><br/>
                </div>
                <input type="hidden" name="modo" value="pagina2">
                <input type="hidden" name="logued" value=true">
                <input type="submit" value="Siguiente">
            </form>
        </body>
    </html>
    <?php
    }
}

function set_html_content_type() {
    return "text/html";
}