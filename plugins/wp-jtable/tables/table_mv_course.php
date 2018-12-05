<?php

/* 
 * Modulo de wp-jtable que atiende las solicitudes de gest_course.php para la creación y visualización de la
 * parte de las maquinas virtuales de la tabla del menú de gestión de las asignaturas en el panel de 
 * administración del plugin de Wordpress. 
 */

/* Llamamos a la función main(), que sera la encargada de realizar todas las llamadas a las funciones necesarias
 * para procesar las respuestas a las peticiones que la tabla de gestión de grados solicite al programa 
 * ------------------------------------------------------------------------------------------------------ */
main_mv_course();
/* ------------------------------------------------------------------------------------------------------ */

/* Esta función carga las librerías de Wordpress necesarias para poder utilizar la interfaz de la base de datos
 * que proporciona el CMS para realizar las peticiones de MySQL */
function load_path_mv_course() {
    $path = $_SERVER['DOCUMENT_ROOT'];  // Cargamos el path con la ruta de instalacion de Wordpress

    /* Cargamos todas las clases de Wordpress que necesitamos utilizar */
    include_once $path . '/wp-config.php';
    include_once $path . '/wp-load.php';
    include_once $path . '/wp-includes/wp-db.php';
    include_once $path . '/wp-includes/pluggable.php';
    /* Fin de la carga ---------------------------------------------- */
}
/* La función generate_course_data es la verdadera encargada de procesar las peticiones de jtable para enviar la
 * respuesta que posteriormente mostrará las tablas del panel de administración del gestor de asignaturas */
function generate_table_mv_course_data() {
    global $wpdb;
    
        // Si la solicitud es para 'listar el contenido':
    if($_GET["action"] == "list") {
        $recordCount = $wpdb->get_var("SELECT COUNT(*) AS RecordCount FROM reservas_mv_asig WHERE asignatura = '" . $_GET["id"] . "';");
        $rows = $wpdb->get_results("SELECT * FROM reservas_mv_asig WHERE asignatura = '" . $_GET["id"] . "';");

        foreach($rows as $row) {
            $result = $wpdb->get_results("SELECT * FROM reservas_maquinas_virtuales WHERE id = '" . $row->maquina_virtual . "';");
            $row->nombre = $result[0]->id;
            $row->ssoo = $result[0]->ssoo;
            $row->capacidad = $result[0]->capacidad;
            $row->aulas = $result[0]->aulas;
        }

        //Return result to jTable
            $jTableResult = array();
            $jTableResult['Result'] = "OK";
            $jTableResult['TotalRecordCount'] = $recordCount;
            $jTableResult['Records'] = $rows;
            print json_encode($jTableResult);
    }
    // Si la solicitud es para eliminar contenido:
    else if($_GET["action"] == "delete") {
        // Eliminamos la referencia al software de la asignatura 
        $asignatura = $_GET["id"];
        $maquina = $_POST["maquina_virtual"];
        $wpdb->delete('reservas_mv_asig', array('asignatura' => $asignatura,'maquina_virtual' => $maquina));

        // Devolvemos el resultado a jtable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
    }
    // Si la solicitud es para crear contenido:
    else if($_GET["action"] == "create") {
        // Realizamos la inserción de los nuevos datos en la bbdd
        $asignatura = $_POST["asignatura"];
        $maquina = $_POST["maquina_virtual"];
        
        $resultado = $wpdb->insert(
                'reservas_mv_asig',
                array(
                    'asignatura' => $asignatura,
                    'maquina_virtual' => $maquina,
                ));
        
        $resultado = $wpdb->get_results("SELECT * FROM reservas_maquinas_virtuales WHERE id = '$maquina';");
        $resultado[0]->maquina_virtual = $resultado[0]->id;
        
        // Devolvemos el resultado a jtable
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['Record'] = $resultado[0];
        print json_encode($jTableResult);
    }   
}
/* La función main() se encarga de las llamadas al resto de funciones para realizar satisfactoriamente las
 * funciones solicitadas por esta clase. */
function main_mv_course() {    
    load_path_mv_course(); 
    generate_table_mv_course_data();
} 
?>