<?php

/* 
 * Administración para la creación de usuarios en el panel de administración.
 */

   
    /* Función que genera la interfaz de usuario para la adición de nuevas reservas desde el panel de 
     * administración de Wordpress */
    function crearReserva() {
        load_javascript_crear_reserva();
        comprobar_peticion_reserva();
        //load_html_crear_reserva();
    }
    
    /* Esta función se encarga de comprobar si hay alguna petición de inserción de nuevas reservas en el
     * sistema y, en caso de que las haya, se encarga de gestionarla y añadirla en la base de datos. */
    function comprobar_peticion_reserva() {
        $modo = $_POST['modo'];
                
        if($modo=='pagina2') {
            $asignatura = $_POST['asignatura'];
            $grupo = $_POST['grupo'];
            $tipo_reserva = $_POST['tipo_reserva'];
            $profesor = $_POST['profesor'];
            ?>
                <div class="wrap">
                    <h2>Crear Reserva</h2>
                </div>
                <?php alertas_reserva(); ?><br/>
                <b>Selección de aula y horario:</b> Paso 2 de 3<br/><br/>
            <?php    
                //A continuación se muestra la ocupación de las aulas el dia (reserva):<br/><br/>
            echo "<div class='create_schedule_calendar'>";
            if($tipo_reserva=='puntual') {
                echo "A continuación se muestra la ocupación de las aulas el día " . $_POST['fecha'].":<br/><br/>";
            }
            if($tipo_reserva=='cuatrimestral') {
                echo "A continuación se muestra la ocupación de las aulas los " . $_POST['dia'].":<br/><br/>";
            }
            if($tipo_reserva=='puntual') {
                $fecha = $_POST['fecha'];
                $dia = obtener_dia_semana($fecha);
                $cuatrimestre = obtener_cuatrimestre($fecha);
            }
            elseif ($tipo_reserva=='cuatrimestral') {
                $dia = $_POST['dia'];
                $cuatrimestre = $_POST['cuatrimestre'];
                if($cuatrimestre==0) {
                    $verano = database::get_opcion("verano_ini");
                    $verano = explode("/",$verano);
                    
                    $fecha = date(Y)."-$verano[0]-$verano[1]";
                    
                }
                else if($cuatrimestre==1) {
                    $cuatrimestre1 = database::get_opcion("cuatrimestre1_ini");
                    
                    $cuatrimestre1 = explode("/",$cuatrimestre1);
                    
                    $fecha = date(Y)."-$cuatrimestre1[0]-$cuatrimestre1[1]";
                }
                else if($cuatrimestre==2) {
                    $cuatrimestre2 = database::get_opcion("cuatrimestre2_ini");
                    $cuatrimestre2 = explode("/",$cuatrimestre2);
                    
                    $fecha = date(Y)."-$cuatrimestre2[0]-$cuatrimestre2[1]";
                }
            }
            echo load_html_preview($dia);
            echo "</div>";
            load_html_page2();
            
            
            echo "<input type=\"hidden\" name=\"asignatura\" value=\"$asignatura\">";
            echo "<input type=\"hidden\" name=\"tipo_reserva\" value=\"$tipo_reserva\">";                      
            echo "<input type=\"hidden\" name=\"fecha\" value=\"$fecha\">";
            echo "<input type=\"hidden\" name=\"dia\" value=\"$dia\">";
            echo "<input type=\"hidden\" name=\"cuatrimestre\" value=\"$cuatrimestre\">";
            echo "<input type=\"hidden\" name=\"profesor\" value=\"$profesor\">";
            echo "</form>";
        }
        else if($modo=='pagina3') {
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
                    $verano = database::get_opcion("verano_ini");
                    $verano = explode("/",$verano);
                    
                    $fecha = date(Y)."-$verano[0]-$verano[1]";
                }
                else if($cuatrimestre==1) {
                    $cuatrimestre1 = database::get_opcion("cuatrimestre1_ini");
                    $cuatrimestre1 = explode("/",$cuatrimestre1);
                    
                    $fecha = date(Y)."-$cuatrimestre1[0]-$cuatrimestre1[1]";
                }
                else if($cuatrimestre==2) {
                    $cuatrimestre2 = database::get_opcion("cuatrimestre2_ini");
                    $cuatrimestre2 = explode("/",$cuatrimestre2);
                    
                    $fecha = date(Y)."-$cuatrimestre2[0]-$cuatrimestre2[1]";
                }
            }
            $aula = $_POST['aula'];
            $hora_inicial = $_POST['hora_inicial'];
            $hora_final = $_POST['hora_final'];
            $profesor = $_POST['profesor'];
            $otrodia=$_POST['otrodia'];
            
            ?>
                <div class="wrap">
                    <h2>Crear Reserva</h2>
                </div>
                <?php alertas_reserva(); ?><br/>
                <b>Crear una nueva reserva:</b> Paso 3 de 3<br/><br/>
                    Se añadirá al sistema una nueva reserva con los siguientes datos facilitados por el usuario. Por favor, confirme que los datos de la reserva son correctos antes de finalizar la solicitud:<br/><br/>
                    <div class="create_schedule_resumen">
                        <b>Profesor: </b> <?php echo $profesor ?><br/>
                        <b>Asignatura: </b> <?php echo $asignatura ?><br/>
                        <b>Grupo: </b> <?php echo $grupo ?><br/>
                        <b>Tipo de Reserva: </b> <?php echo $tipo_reserva ?><br/>
                        <form action="<?php echo get_permalink(); ?>" method="POST">
                        <?php
                            if($tipo_reserva=='puntual') {
                                if($otrodia=='otrodia') {
                                    echo "<b>Fecha: </b><input type=\"date\" name=\"fecha\" value=\"$fecha\"><br/>";
                                }
                                else {
                                   echo "<b>Fecha: </b>" . $fecha . "<br/>"; 
                                   echo "<input type=\"hidden\" name=\"fecha\" value=\"$fecha\">";
                                }
                                
                            }
                            else {
                                echo "<b>Día: </b>Todos los " . $dia . " del ". $cuatrimestre ." cuatrimestre" . "<br/>";
                            }
                        ?>
                        <b>Aula: </b> <?php echo $aula ?><br/>
                        <b>Horario: </b> de <?php echo $hora_inicial ?> a <?php echo $hora_final ?><br/>
                        
                    </div>
            <?php
            load_html_page3();
            
            echo "<input type=\"hidden\" name=\"asignatura\" value=\"$asignatura\">";
            echo "<input type=\"hidden\" name=\"grupo\" value=\"$grupo\">";
            echo "<input type=\"hidden\" name=\"tipo_reserva\" value=\"$tipo_reserva\">";

            echo "<input type=\"hidden\" name=\"dia\" value=\"$dia\">";
            echo "<input type=\"hidden\" name=\"cuatrimestre\" value=\"$cuatrimestre\">";
            echo "<input type=\"hidden\" name=\"profesor\" value=\"$profesor\">";
            echo "<input type=\"hidden\" name=\"aula\" value=\"$aula\">";
            echo "<input type=\"hidden\" name=\"hora_inicial\" value=\"$hora_inicial\">";
            echo "<input type=\"hidden\" name=\"hora_final\" value=\"$hora_final\">";
            echo "</form>";
        }
        else if($modo=='insertar') {
            /* Vamos recogiendo las distintas variables para la inserción en la BBDD */
            $confirmada = $_POST['confirmada'];
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
            elseif ($tipo_reserva=='cuatrimestral') {
                $dia = $_POST['dia'];
                $cuatrimestre = $_POST['cuatrimestre'];
                if($cuatrimestre==0) {
                    $verano = database::get_opcion("verano_ini");
                    $verano = explode("/",$verano);
                    
                    $fecha = date(Y)."-$verano[0]-$verano[1]";
                }
                else if($cuatrimestre==1) {
                    $cuatrimestre1 = database::get_opcion("cuatrimestre1_ini");
                    $cuatrimestre1 = explode("/",$cuatrimestre1);
                    
                    $fecha = date(Y)."-$cuatrimestre1[0]-$cuatrimestre1[1]";
                }
                else if($cuatrimestre==2) {
                    $cuatrimestre2 = database::get_opcion("cuatrimestre2_ini");
                    $cuatrimestre2 = explode("/",$cuatrimestre2);
                    
                    $fecha = date(Y)."-$cuatrimestre2[0]-$cuatrimestre2[1]";
                }
            }
            /* Fin de la recogida de parámetros ------------------------------------ */
            /* Obtenemos los valores de las claves ajenas de la bbdd --------------- */
            $asignatura = database::get_asignatura("nombre",$asignatura);
            $asignatura_id = $asignatura[0]->id;

            $aula = database::get_aulas("nombre",$aula);
            $aula_id = $aula[0]->id;

            if($tipo_reserva=='cuatrimestral') $tipo_cod = 0;
            if($tipo_reserva=='puntual') $tipo_cod = 1;

            /* Fin de la obtención de valores -------------------------------------- */
            /* Realizamos la inserción en la base de datos de wordpress ------------ */
            database::set_reserva($confirmada,$asignatura_id,$grupo,$profesor,$aula_id,$tipo_cod,$dia,$cuatrimestre,$fecha,$hora_inicial,$hora_final);
            /* Fin de la inserción ------------------------------------------------- */
            load_html_page1();
        }
        else {
            load_html_page1();
        }
    }
    
    /* Con esta función cargamos todo el código javascript necesario para el correcto funcionamiento para
     * el correcto funcionamiento de la interfaz de usuario del menú de administración para crear una nueva
     * reserva. */
    function load_javascript_crear_reserva () {
        ?>
        <script>
            // Esta función modifica la interfaz dependiendo del tipo de reserva que se desea realizar 
            function tipoReserva(tipo) {
                if(tipo.value=="cuatrimestral") {
                    formulario_nuevo = document.getElementById("cuatrimestral");
                    formulario_viejo = document.getElementById("puntual");
                }
                else {
                    formulario_viejo = document.getElementById("cuatrimestral");
                    formulario_nuevo = document.getElementById("puntual");
                }

                formulario_viejo.style.display = (formulario_viejo.style.display == 'none') ? 'block' : 'none';
                formulario_nuevo.style.display = (formulario_nuevo.style.display == 'inline') ? 'block' : 'inline';
            } 
            function poner_fecha () {
                var today = new Date();
                var cuatrimestre = <?php echo definir_cuatrimestre_actual(); ?>;
                if(cuatrimestre==0) {
                    inicio = today.getFullYear()+'-<?php echo obtener_fecha_cuatrimestre(0); ?>';
                }
                else if(cuatrimestre==1){
                    inicio = today.getFullYear()+'-<?php echo obtener_fecha_cuatrimestre(1); ?>';
                }
                else if(cuatrimestre==2) {
                    inicio = today.getFullYear()+'-<?php echo obtener_fecha_cuatrimestre(2); ?>';
                }
                $('#fecha').val(inicio);
            }
            jQuery(document).ready(function ($) {
                poner_fecha();
            });
        </script>
        <?php
    }
    
/* COMIENZO DE LA ZONA CON FUNCIONES PARA DEFINIR EL HTML ---------------------------------------------------- */
/* ----------------------------------------------------------------------------------------------------------- */
    
    /* Esta función carga el código html básico de la página de creación de reservas para el panel de administración.
     * Hará llamadas sucesivas a otras funciones para completar la interfaz con secciones más complejas de código.
     */
    function load_html_page1 () {
        ?>
            <div class="wrap">
                <h2>Crear Reserva</h2>
            </div>
            <?php alertas_reserva(); ?><br/>
            <b>Crear una nueva reserva:</b> Paso 1 de 3<br/><br/>
            <form action="<?php echo get_permalink(); ?>" method="POST">
                <div class="columna-1">
                    Profesor<br/>
                        <input type="text" name="profesor" required><br/>  
                        <i>Correo electrónico del profesor que realiaza la reserva.</i><br/><br/>
                    Asignatura<br/>
                        <?php echo generar_seleccion_asignatura(); ?><br/>
                        <i>Asignatura que se impartirá durante la sesión de la reserva.</i><br/><br/>
                </div>
                <div class="columna-1">
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
                    <div id="cuatrimestral">
                        Fecha <br/>
                            Todos los <select name="dia">
                                <option value="Lunes">Lunes</option>
                                <option value="Martes">Martes</option>
                                <option value="Miércoles">Miércoles</option>
                                <option value="Jueves">Jueves</option>
                                <option value="Viernes">Viernes</option>
                            </select> del <select name="cuatrimestre">
                                <option value="1">Primer Cuatrimestre</option>
                                <option value="2">Segundo Cuatrimestre</option>
                            </select><br/>
                            <i>Indique la fecha en que se desea realizar la reserva.</i><br/><br/>
                    </div>
                </div>
                <input type="hidden" name="modo" value="pagina2">
                <div class="create_schedule_button1">
                    <input type="submit" class="button button-primary" value="Siguiente">
                </div>
        </form>
        <?php
    }
    function load_html_page2() {
        ?>             
            <form action="<?php echo get_permalink(); ?>" method="POST">
                <div class="columna-21">
                    <p>
                        <i>A la izquierda puede verse la ocupación de las aulas del día en el que se desea programar
                        la reserva. Por favor, compruebe que el horario elegido se corresponde con alguno de los
                        huecos libres entre las reservas ya establecidas y complete los siguientes campos:</i> 
                    </p>
                   Grupo <br/>
                        <?php echo generar_seleccion_grupos($_POST['asignatura']); ?><br/>
                        <i>Grupo de la asignatura que va a aprovechar dicha reserva.</i><br/><br/>
                   Aula <br/>
                    <?php echo generar_seleccion_aula(); ?><br/>
                    <i>Aula que se desea reservar. Tenga en cuenta el número de puestos de trabajo disponibles en cada aula.</i><br/><br/>
                    
                    Hora<br/>
                    <input type="time" name="hora_inicial" value="09:00" required> -
                    <input type="time" name="hora_final" value="11:00" required><br/>
                    <i>Hora en la que se desea realizar la reserva.</i><br/><br/>
               
                <input type="hidden" name="modo" value="pagina3">
                
                    <input type="button" onclick="javascript:history.back()" class="button" value="Atrás">
                    <input type="submit" class="button button-primary" value="Siguiente">
                </div>
        <?php
    }
    function load_html_page3() {
                ?>           
            
                <input type="checkbox" name="confirmada" value="1"> <b>Confirmar esta reserva</b>. Solo las reservas confirmadas aparecen en el calendario de reservas visible para profesores y alumnos.
                             
                <input type="hidden" name="modo" value="insertar">
                <div class="create_schedule_button1">
                    <input type="button" onclick="javascript:history.back()" class="button" value="Atrás">
                    <input type="submit" class="button button-primary" value="Finalizar reserva">
                </div>
        <?php
    }
    
    /* Función que notifica cuando se ha realizado una solicitud de inserción en la BBDD */
    function alertas_reserva() {
        $modo = $_POST['modo']; // Determinamos si se ha solicitado y procesado alguna petición.

        if($modo=='insertar') { // Si hemos insertado, mostramos la notificación pertinente.
            ?>
            <div id="message" class="updated">
                <form action="<?php echo get_permalink(); ?>" method="POST">
                <p>Reserva publicada. Puede 
                    <a href=admin.php?page=Reservas>ver la lista de reservas</a> o 
                    <a href="javascript:document.forms[0].submit()">añadir esta misma reserva otro día</a>
                </p>
                
                <input type="hidden" name="modo" value="pagina3">
                <input type="hidden" name="confirmada" value="<?php echo $_POST['confirmada']?>">
                <input type="hidden" name="asignatura" value="<?php echo $_POST['asignatura']?>">
                <input type="hidden" name="grupo" value="<?php echo $_POST['grupo']?>">
                <input type="hidden" name="profesor" value="<?php echo $_POST['profesor']?>">
                <input type="hidden" name="aula" value="<?php echo $_POST['aula']?>">
                <input type="hidden" name="fecha" value="<?php echo $_POST['fecha']?>">
                <input type="hidden" name="tipo_reserva" value="<?php echo $_POST['tipo_reserva']?>">
                <input type="hidden" name="hora_inicial" value="<?php echo $_POST['hora_inicial']?>">
                <input type="hidden" name="hora_final" value="<?php echo $_POST['hora_final']?>">
                <input type="hidden" name="otrodia" value="otrodia">
                
                </form>
            </div>
            <?php
            }
    }
    
    /* Esta funcion solicita a la base de datos la lista de asignaturas para generar el desplegable con
     * todas las asignaturas guardadas en la misma.
     */
    function generar_seleccion_asignatura() {
        $lista_asignaturas = database::get_asignatura(NULL,NULL);   // Realizamos la solicitud a la BBDD

        $return = "<select name=\"asignatura\" required>";  // Definimos el "Select"
        foreach($lista_asignaturas as $asignatura) {    // Vamos generando las distintas opciones
            $nombre = $asignatura->nombre;

            $return .= "<option value=\"$nombre\">";
            $return .= $nombre;
            $return .='</option>';
        }
        $return .= '</select>'; // Cerramos el "Select" una vez hemos terminado

        return $return; // Retornamos el código HTML como resultado. 
    }
    
    /* Esta funcion solicita a la base de datos la lista de aulas para generar el desplegable con
     * todas las aulas guardadas en la misma.
     */
    function generar_seleccion_aula() {
        $lista_aulas = database::get_aulas(NULL,NULL);  // Realizamos la solicitud a la BBDD

        $return = '<select name="aula" required>';  // Definimos el "Select"
        foreach($lista_aulas as $aula) {    // Vamos generando las distintas opciones
            $nombre = $aula->nombre;

            $return .="<option value='$nombre'>";
            $return .= $nombre;
            $return .= "</option>";
        }
        $return .= '</select>'; // Cerramos el "Select" una vez hemos terminado

        return $return; // Retornamos el código HTML como resultado. 
    }
    
    function generar_seleccion_grupos($asignatura) {
        $asignatura = database::get_asignatura("nombre",$asignatura);
        $grado = database::get_grado("id",$asignatura[0]->grado);
        
        $grupos = explode(",",$grado[0]->grupos);
        $return = '<select name="grupo" required>';
        foreach($grupos as $grupo) {
            $return .="<option value=$grupo>";
            $return .=$grupo;
            $return .="</option>";
        }
        $return .= '</select>';
        return $return;
    }
    
    function load_html_preview($dia) {
        $reservas = database::get_reservas("dia",$dia); //Obtenemos las reservas del dia pasado como atributo
        $num_reservas = count($reservas);   //Calculamos el numero de reservas
        $aulas = database::get_aulas(NULL,NULL); //Obtenemos las aulas de la base de datos
        $num_aulas = count($aulas); //Calculamos el numero de aulas
        $hora = "09";   //Establecemos la hora inicial
        $minutos = "00";    //Establecemos los minutos iniciales
        $columna = 0;   // Variable auxiliar que nos situa en cada columna de la tabla generada
        $rowspan = array($num_aulas);   // Variable que determina el tamaño (en fracciones de tiempo) de cada reserva
        $cuatrimestre = definir_cuatrimestre_actual();  // Nos indica en qué cuatrimestre nos encontramos
        $dias_semana = definir_semana_actual();
        /* COMENZAMOS A PINTAR LA TABLA --------------------------------------------------------------------- */
        //La tabla se guardara en la variable $output que sera retornada para asi ser mostrada por Wordpress
        $output .= '<table>';

        /* Creamos las filas de la tabla con la HORA y el nombre de todas las aulas */
        $output .='<tr><th>Hora</th>';
        $alternative=0;
        foreach($aulas as $aula) {
            $output .="<th>$aula->nombre</th>";
        }
        /* Fin de la creacion de la primera fila de la tabla ---------------------- */
        /* Creamos el resto de filas de la tabla, una por cada 15 minutos --------- */
        $rowspan[$columna]=0; //Inicializamos el valor de la variable rowspan
        while($hora<"21") {
            $output .='</tr><tr>';  //Cerramos la fila anterior y comenzamos con la nueva
            $output .="<th>$hora:$minutos</th>";    //Pintamos la hora:minuto en la que nos encontramos
            /* Comprobamos aula por aula... */
            foreach($aulas as $aula) {
                /* ...y en todas las reservas... */
                foreach($reservas as $reserva) {
                   /* ... si existe una reserva en ese aula programada en la hora actual */
                    if($reserva->confirmada==1 && $aula->id==$reserva->aula && $reserva->hora_inicio=="$hora:$minutos:00") {
                        /* Comprobamos si es una reserva cuatrimestral */
                        if($reserva->tipo_reserva==0 && $reserva->cuatrimestre==$cuatrimestre) {
                            /* Si lo es la pintamos */
                             $asignatura = database::get_asignatura("id",$reserva->asignatura);   //Recuperamos el nombre de la asignatura de la base de datos
                             $asignatura = $asignatura[0]; //Cambiamos a un formato legible
                             $grado = database::get_grado("id",$asignatura->grado);               
                             $grado = $grado[0];

                             if($solapa==1) {
                                                             $final = explode(":",$reserva->hora_fin);

                                $output .="<br>&<br>$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b>"; 
                             
                                //Recalculamos el rowspan
                           if($hora==$final[0]) {
                                $rowspan_2 = ($final[1]-$minutos)/15;
                            }
                            else {
                                $rowspan_2 += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                            }
                            if($rowspan[$columna]<$rowspan_2) $rowspan[$columna]=$rowspan_2;
                             }               
                             else {
                                 /* Definimos el tamaño (en fracciones de 15 minutos) que ocupa nuestra reserva */
                             $final = explode(":",$reserva->hora_fin);
                             if($hora==$final[0]) {
                                 $rowspan[$columna] += ($final[1]-$minutos)/15;
                             }
                             else {
                                 $rowspan[$columna] += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                             }
                             /* Fin de la definicion del tamaño de la reserva ---------------------------- */
                             // Pintamos la reserva 
                                $background = database::get_opcion("c_cuatrimestral");
                                $output .="<td rowspan=$rowspan[$columna] style=\"background:$background;color:white\">$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b>"; 
                                $solapa = 1;
                             }
                        }
                        /* Si no es una reserva cuatrimestral, asumimos que se trata de una reserva puntual */
                        elseif(($reserva->fecha)>=(date("Y")."-".date("m")."-".date("d"))){
                            /* Si lo es la pintamos */
                             $asignatura = database::get_asignatura("id",$reserva->asignatura);   //Recuperamos el nombre de la asignatura de la base de datos
                             $asignatura = $asignatura[0]; //Cambiamos a un formato legible 
                                       
                             //$profesor = database::get_profesor($reserva->profesor);    //Recuperamos el nombre del profesor del LDAP  
                             $aula = database::get_aulas("id",$reserva->aula);   //Recuperamos el nombre del aula de la base de datos
                             $aula = $aula[0];  //Cambiamos a un formato legible
                             $grado = database::get_grado("id",$asignatura->grado);               
                             $grado = $grado[0];
                             if($solapa == 1) {
                                                             $final = explode(":",$reserva->hora_fin);

                                $output .="<br>&<br>$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b> el $reserva->fecha";
                                
                                //Recalculamos el rowspan
                                if($hora==$final[0]) {
                                     $rowspan_2 = ($final[1]-$minutos)/15;
                                 }
                                 else {
                                     $rowspan_2 += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                                 }
                                 if($rowspan[$columna]<$rowspan_2) $rowspan[$columna]=$rowspan_2;
                             }
                            /* Si la hora coincide definimos el tamaño de la reserva --------------------- */
                            $final = explode(":",$reserva->hora_fin);
                            if($hora==$final[0]) {
                                $rowspan[$columna] += ($final[1]-$minutos)/15;
                            }
                            else {
                                 $rowspan[$columna] += (($final[1]-$minutos)/15) + 4*($final[0]-$hora);
                             }
                             /* Fin de la definicion del tamaño de la reserva ---------------------------- */
                             // Pintamos la reserva puntual
                             $background = database::get_opcion("c_puntual");

                             $output .="<td rowspan=$rowspan[$columna] style=\"background:$background;color:white\">$asignatura->nombre (G$reserva->grupo)<br/><b>$grado->nombre</b> el $reserva->fecha";
                             $solapa = 1;
                        }  
                    }
               }
               $output.="</td>";
               $solapa=0;
               /* Si no hemos pintado ninguna reserva, cerramos el hueco de la tabla */
               if ($rowspan[$columna]==0) {
                       if($alternative==1) {
                          $output .="<td class='statistics_alternate'></td>";                              
                       }
                       else {
                          $output .="<td></td>";   
                       }
                                    
               }
               /* En caso de haber pintado una reserva, reducimos el tamaño del rowspan y no pintamos nada  */
               else $rowspan[$columna]--;
               $columna++;
            }
            /* Actualizamos el valor de la hora para pasar a la siguiente franja */
            $minutos+="15*$rowspan";
            if($minutos=="60") {
                $minutos="00";
                $hora++;
            }
            if($alternative==0) $alternative=1;
            else if($alternative==1) $alternative=0;
            $columna = 0;
        }  
        $output .='</tr></table>';  // Cerramos todas las etiquetas abiertas del calendario


        return $output;
    }

/* ----------------------------------------------------------------------------------------------------------- */
/* FIN DE LA ZONA DE DEFINICIÓN DEL HTML --------------------------------------------------------------------- */
    
    /* Función auxiliar que devuelve el día de la semana en función de una fecha dada */
    function obtener_dia_semana($fecha) {
        $dia_code = date('N', strtotime($fecha));
        // Convertimos el día de la semana a un formato no numérico.
        if($dia_code==1) $dia="Lunes";
        if($dia_code==2) $dia="Martes";
        if($dia_code==3) $dia="Miércoles";
        if($dia_code==4) $dia="Jueves";
        if($dia_code==5) $dia="Viernes";

        return $dia;    // Devolvemos el día de la semana.
    }
    
    /* Función auxiliar que devuelve el cuatrimestre en función de una fecha dada */
    function obtener_cuatrimestre($fecha) {
        $fecha_array = explode("-",$fecha);
        $mes = $fecha_array[1];
        
        $cuatrimestre1_ini = database::get_opcion("cuatrimestre1_ini");
        $cuatrimestre1_ini = explode("/",$cuatrimestre1_ini);
        $cuatrimestre1_fin = database::get_opcion("cuatrimestre1_fin");
        $cuatrimestre1_fin = explode("/",$cuatrimestre1_fin);
        
        $cuatrimestre2_ini = database::get_opcion("cuatrimestre2_ini");
        $cuatrimestre2_ini = explode("/",$cuatrimestre2_ini);
        $cuatrimestre2_fin = database::get_opcion("cuatrimestre2_fin");
        $cuatrimestre2_fin = explode("/",$cuatrimestre2_fin);
        
        $verano_ini = database::get_opcion("verano_ini");
        $verano_ini = explode("/",$verano_ini);
        $verano_fin = database::get_opcion("verano_fin");
        $verano_fin = explode("/",$verano_fin);
        
        if($mes>=$verano_ini[0] && $mes<=$verano_fin[0]) {
            $cuatrimestre = 0; // Entre julio y agosto: verano (0)
        }
        elseif($mes>=$cuatrimestre1_ini[0] && $mes<=$cuatrimestre1_fin[0]) {
            $cuatrimestre = 1;  // Entre septiembre y diciembre, primer cuatrimestre (1)
        }
        elseif($mes>=$cuatrimestre2_ini[0] && $mes<=$cuatrimestre2_fin[0]) {
            $cuatrimestre = 2;  // Entre enero y junio, segundo cuatrimestre (2)
        }

        return $cuatrimestre;
    }
    
    /* Función auxiliar que devuelve el cuatrimestre en función de una fecha dada */
    function obtener_fecha_cuatrimestre($fecha) {
        
        $cuatrimestre1_ini = database::get_opcion("cuatrimestre1_ini");
        $cuatrimestre1_ini = explode("/",$cuatrimestre1_ini);
        $cuatrimestre1_fin = database::get_opcion("cuatrimestre1_fin");
        $cuatrimestre1_fin = explode("/",$cuatrimestre1_fin);
        
        $cuatrimestre2_ini = database::get_opcion("cuatrimestre2_ini");
        $cuatrimestre2_ini = explode("/",$cuatrimestre2_ini);
        $cuatrimestre2_fin = database::get_opcion("cuatrimestre2_fin");
        $cuatrimestre2_fin = explode("/",$cuatrimestre2_fin);
        
        $verano_ini = database::get_opcion("verano_ini");
        $verano_ini = explode("/",$verano_ini);
        $verano_fin = database::get_opcion("verano_fin");
        $verano_fin = explode("/",$verano_fin);
        
        if($fecha==0) {
            $cuatrimestre = "$verano_ini[0]-$verano_ini[1]"; // Entre julio y agosto: verano (0)
        }
        elseif($fecha==1) {
            $cuatrimestre = "$cuatrimestre1_ini[0]-$cuatrimestre1_ini[1]";  // Entre septiembre y diciembre, primer cuatrimestre (1)
        }
        elseif($fecha==2) {
            $cuatrimestre = "$cuatrimestre2_ini[0]-$cuatrimestre2_ini[1]";  // Entre enero y junio, segundo cuatrimestre (2)
        }

        return $cuatrimestre;
    }
    function obtener_fin_cuatrimestre($fecha) {
        
        $cuatrimestre1_ini = database::get_opcion("cuatrimestre1_ini");
        $cuatrimestre1_ini = explode("/",$cuatrimestre1_ini);
        $cuatrimestre1_fin = database::get_opcion("cuatrimestre1_fin");
        $cuatrimestre1_fin = explode("/",$cuatrimestre1_fin);
        
        $cuatrimestre2_ini = database::get_opcion("cuatrimestre2_ini");
        $cuatrimestre2_ini = explode("/",$cuatrimestre2_ini);
        $cuatrimestre2_fin = database::get_opcion("cuatrimestre2_fin");
        $cuatrimestre2_fin = explode("/",$cuatrimestre2_fin);
        
        $verano_ini = database::get_opcion("verano_ini");
        $verano_ini = explode("/",$verano_ini);
        $verano_fin = database::get_opcion("verano_fin");
        $verano_fin = explode("/",$verano_fin);
        
        if($fecha==0) {
            $cuatrimestre = "$verano_fin[0]-$verano_fin[1]"; // Entre julio y agosto: verano (0)
        }
        elseif($fecha==1) {
            $cuatrimestre = "$cuatrimestre1_fin[0]-$cuatrimestre1_fin[1]";  // Entre septiembre y diciembre, primer cuatrimestre (1)
        }
        elseif($fecha==2) {
            $cuatrimestre = "$cuatrimestre2_fin[0]-$cuatrimestre2_fin[1]";  // Entre enero y junio, segundo cuatrimestre (2)
        }

        return $cuatrimestre;
    }
//}   


