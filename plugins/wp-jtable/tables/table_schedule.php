<?php

/* 
 * Modulo de wp-jtable que atiende las solicitudes de gest_reservas.php para la creación y visualización de la
 * tabla del menú de gestión de reservas en el panel de administración del plugin de Wordpress 
 */


/* Llamamos a la función main(), que sera la encargada de realizar todas las llamadas a las funciones necesarias
 * para procesar las respuestas a las peticiones que la tabla de gestión de reservas solicite al programa 
 * ------------------------------------------------------------------------------------------------------ */
main_schedule();
/* ------------------------------------------------------------------------------------------------------ */

/* Esta función carga las librerías de Wordpress necesarias para poder utilizar la interfaz de la base de datos
 * que proporciona el CMS para realizar las peticiones de MySQL */
function load_path() {
    $path = $_SERVER['DOCUMENT_ROOT'];  // Cargamos el path con la ruta de instalacion de Wordpress

    /* Cargamos todas las clases de Wordpress que necesitamos utilizar */
    include_once $path . '/wp-config.php';
    include_once $path . '/wp-load.php';
    include_once $path . '/wp-includes/wp-db.php';
    include_once $path . '/wp-includes/pluggable.php';
    /* Fin de la carga ---------------------------------------------- */
}

/* La función generate_table_data es la verdadera encargada de procesar las peticiones de jtable para enviar la
 * respuesta que posteriormente mostrará las tablas del panel de administración del gestor de reservas */
function generate_table_schedule_data() {
    global $wpdb; 
    
    // Si la solicitud es para 'listar el contenido':
    if($_GET["action"] == "list") {
        // Realizamos un conteo del número de reservas que se mostrarán dependiendo de los atributos
        $result = "SELECT COUNT(*) AS RecordCound FROM reservas_reservas WHERE";
        $control = 0;
        
        if($_POST["tipo_reserva"]!=2) {
            $result.= " tipo_reserva=".$_POST["tipo_reserva"];
            $control++;
        }
        if($_POST["cuatrimestre"]!=0) {
            if($control!=0) $result.=" AND";
            $result.= " cuatrimestre=".$_POST["cuatrimestre"];
            $control++;
        }
        if($_POST["aula"]!=0) {
            if($control!=0) $result.=" AND";
            $result.=" aula=".$_POST["aula"];
            $control++;
        }
        if($_POST["asignatura"]) {
            if($control!=0) $result.=" AND";
            $result.=" asignatura=\"".$_POST["asignatura"]."\"";
            $control++;
        }
        if($_POST["profesor"]) {
            if($control!=0) $result.=" AND";
            $result.=" profesor=\"".$_POST["profesor"]."\"";
            $control++;
        }
        if($_POST["dia"]) {
            if($control!=0) $result.=" AND";
            $result.=" dia=\"".$_POST["dia"]."\"";
            $control++;
        }
        if($control!=0) $result.=" AND";
        $result .= " (fecha >= '" .$_POST["fecha_ini"]. "' AND fecha <= '" .$_POST["fecha_fin"]. "') AND";
        $result.=" TIME(hora_inicio)>='".$_POST["time_ini"]."' AND TIME(hora_fin)<='".$_POST["time_fin"]."' ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
        $control++;
        $result.=";";
        
        $recordCount = $wpdb->get_var($result); // Realizamos la consulta y guardamos el resultado

        // Obtenemos todas las reservas solicitadas y se las enviamos a la tabla de administración
        $result = "SELECT * FROM reservas_reservas WHERE";
        $control = 0;
        
        if($_POST["tipo_reserva"]!=2) {
            $result.= " tipo_reserva=".$_POST["tipo_reserva"];
            $control++;
        }
        if($_POST["cuatrimestre"]!=0) {
            if($control!=0) $result.=" AND";
            $result.= " cuatrimestre=".$_POST["cuatrimestre"];
            $control++;
        }
        if($_POST["aula"]!=0) {
            if($control!=0) $result.=" AND";
            $result.=" aula=".$_POST["aula"];
            $control++;
        }
        if($_POST["asignatura"]) {
            if($control!=0) $result.=" AND";
            $result.=" asignatura=\"".$_POST["asignatura"]."\"";
            $control++;
        }
        if($_POST["profesor"]) {
            if($control!=0) $result.=" AND";
            $result.=" profesor=\"".$_POST["profesor"]."\"";
            $control++;
        }
        if($_POST["dia"]) {
            if($control!=0) $result.=" AND";
            $result.=" dia=\"".$_POST["dia"]."\"";
            $control++;
        }
        if($control!=0) $result.=" AND";
        $result .= " (fecha >= '" .$_POST["fecha_ini"]. "' AND fecha <= '" .$_POST["fecha_fin"]. "') AND";
        $result.=" TIME(hora_inicio)>='".$_POST["time_ini"]."' AND TIME(hora_fin)<='".$_POST["time_fin"]."' ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"];
        $control++;

        $result.=";";
        $rows = $wpdb->get_results($result);    // Realizamos la consulta
        //Formateamos los valores de la base de datos para hacerlos amigables al usuario
        foreach($rows as $row) {
            // Generamos el contenido del campo "Descripcion"
            $row->descripcion = obtener_descripcion_array($row); 
            
            // Recogemos el nombre de la asignatura 
            $row->asignatura = ($wpdb->get_var("SELECT nombre FROM reservas_asignatura WHERE id ='$row->asignatura'"));
            
            /*// Formateamos el valor del campo 'confirmada' para hacerlo entendible
            if(($row->confirmada)=='1') $row->confirmada="Sí";
            if(($row->confirmada)=='0') $row->confirmada="No";*/
            
            // Recogemos el nombre del aula
            //$row->aula = ($wpdb->get_var("SELECT nombre FROM reservas_aulas WHERE id ='$row->aula'"));
            
            // Formateamos el valor del campo 'tipo_reserva' para hacerlo entendible
           /* if($row->tipo_reserva=='0') $row->tipo_reserva="Cuatrimestral";
            if($row->tipo_reserva=='1') $row->tipo_reserva="Puntual";*/
        }
        //Enviamos el resultado a la tabla de jtable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['TotalRecordCount'] = $recordCount;
        $jTableResult['Records'] = $rows;
        print json_encode($jTableResult);
    }
    // Si la solicitud es para 'actualizar el contenido':
    else if($_GET["action"] == "update") {
        
        // Obtenemos el valor del campo "confirmada" y lo formateamos para almacenarlo en la BBDD
        $confirmada = $_POST["confirmada"];

        //Obtenemos el valor del grupo y comprobamos que su valor es correcto para insertar en la BBDD
        $grupo = $_POST["grupo"];
        if(!ctype_digit($grupo)) {
            $jTableResult = array();
            $jTableResult['Result'] = "Error";
            $jTableResult['Message'] = "El campo 'grupo' debe contener únicamente caracteres numéricos.";
            print json_encode($jTableResult);
            return -1;  // Paramos la ejecucion del script
        } 
        // Obtenemos el valor de "aula" y obtenemos su id de la tabla de aulas
        $aula = $_POST["aula"];
        // Obtenemos el valor de "tipo_reserva" y lo formateamos para almacenarlo en la BBDD
        $tipo_reserva = $_POST["tipo_reserva"];
        /* Realizamos la actualización -------------------------------------------------------------- */
        $wpdb->update(
            'reservas_reservas',
            array(
                'confirmada' => $confirmada,
                'grupo' => $_POST["grupo"],
                'profesor' => $_POST["profesor"],
                'aula' => $aula,
                'tipo_reserva' => $tipo_reserva,
                'dia' => $_POST["dia"],
                'cuatrimestre' => $_POST["cuatrimestre"],
                //'fecha' => $_POST["fecha"],
                'hora_inicio' => $_POST["hora_inicio"],
                'hora_fin' => $_POST["hora_fin"],
            ),
            array( 'id' => $_POST["id"])
        );       
        /* Fin de la funcion UPDATE ----------------------------------------------------------------- */
        // Enviamos el resultado a jtable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
    }
    // Si la solicitud es para 'eliminar un contenido':
    else if($_GET["action"] == "delete") {
        // Realizamos la eliminación de la base de datos 
        $wpdb->delete('reservas_reservas', array('id' => $_POST["id"]));
        /* ------------------------------------------------------------------------------- */
        // Enviamos el resultado a jtable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
    }
}

/* La función main() se encarga de las llamadas al resto de funciones para realizar satisfactoriamente las
 * funciones solicitadas por esta clase. */
function main_schedule() {    
    load_path(); 
    generate_table_schedule_data();
}
?>