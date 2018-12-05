<?php

/* 
 * Modulo de wp-jtable que atiende las solicitudes de gest_course.php para la creación y visualización de la
 * tabla del menú de gestión de las asignaturas en el panel de administración del plugin de Wordpress 
 */

/* Llamamos a la función main(), que sera la encargada de realizar todas las llamadas a las funciones necesarias
 * para procesar las respuestas a las peticiones que la tabla de gestión de grados solicite al programa 
 * ------------------------------------------------------------------------------------------------------ */
main_course();
/* ------------------------------------------------------------------------------------------------------ */

/* Esta función carga las librerías de Wordpress necesarias para poder utilizar la interfaz de la base de datos
 * que proporciona el CMS para realizar las peticiones de MySQL */
function load_path_course() {
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
function generate_table_course_data() {
    global $wpdb;

    // Si la solicitud es para 'listar el contenido':
    if($_GET["action"] == "list") {
        /* Realizamos un conteo del número de softwares que se mostrarán --------------------- */
        $recordCount = $wpdb->get_var("SELECT COUNT(*) AS RecordCount FROM reservas_asignatura");
        /* Fin del conteo de tuplas ---------------------------------------------------------- */
        /* Realizamos la peticion a la base de datos para obtener los datos --------------------------- */
        $rows = $wpdb->get_results("SELECT * FROM reservas_asignatura ORDER BY " . $_GET["jtSorting"] . " LIMIT " . $_GET["jtStartIndex"] . "," . $_GET["jtPageSize"] . ";");
        /* Fin de la recogida de datos ---------------------------------------------------------------- */ 
        foreach ($rows as $row) {
            $row->grado = $wpdb->get_var("SELECT nombre FROM reservas_grado WHERE id =" . $row->grado);
        }

        /* Devolvemos los resultados a jtable */
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['TotalRecordCount'] = $recordCount;
        $jTableResult['Records'] = $rows;
        print json_encode($jTableResult);
        /* Fin del retorno de los datos ---- */
    }
    // Si la solicitud es para actualizar el contenido 
    else if($_GET["action"] == "update") {
        /* Recogemos los datos que deseamos actualizar */
        $nombre = $_POST["nombre"];
        $grado = $_POST["grado"];
        $cuatrimestre = $_POST["cuatrimestre"];
        $curso = $_POST["curso"];
        /* Fin de recogida de datos ----------------- */
        /* Comprobamos que todos los datos son correctos */
        $grado = $wpdb->get_var("SELECT id FROM reservas_grado WHERE nombre='$grado'");
        if($grado==NULL) {
            $jTableResult = array();
            $jTableResult['Result'] = "Error";
            $jTableResult['Message'] = "$grado";
            print json_encode($jTableResult);
            return -1;  // Paramos la ejecucion del script
        }
        if(!is_numeric($cuatrimestre) || $cuatrimestre<0 || $cuatrimestre>2) {
            $jTableResult = array();
            $jTableResult['Result'] = "Error";
            $jTableResult['Message'] = "El cuatrimestre no es válido. Los valores aceptados son 1 ó 2.";
            print json_encode($jTableResult);
            return -1;  // Paramos la ejecucion del script
        }
        if(!is_numeric($curso) || $curso<0 || $curso>4) {
            $jTableResult = array();
            $jTableResult['Result'] = "Error";
            $jTableResult['Message'] = "El curso no es válido. Los valores aceptados son 1-4";
            print json_encode($jTableResult);
            return -1;  // Paramos la ejecucion del script
        }
        /* Fin de la comprobación de datos ------------ */
        /* Actualizamos el dato en la base de datos */
        $wpdb->update(
            'reservas_asignatura',
            array(
                'nombre' => $nombre,
                'grado' => $grado,
                'cuatrimestre' => $cuatrimestre,
                'curso' => $curso,
            ),
            array( 'id' => $_POST["id"])
        );         
        /* Fin de la actualización del dato ------- */

        /* Devolvemos el resultado a jtable */
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
        /* Fin del retorno de los datos ---- */
    }
    // Si la solicitud es para eliminar el contenido 
    else if($_GET["action"] == "delete") {
        /* Eliminamos las reservas que hacen uso de la asignatura */
        $wpdb->delete('reservas_reservas', array('asignatura' => $_POST["id"]));
        /* Fin del borrado de reservas ---------------------------------- */
        /* Eliminamos las referencias de uso del software y las maquinas virtuales */
        $wpdb->delete('reservas_mv_asig', array('asignatura' => $_POST["id"]));
        $wpdb->delete('reservas_sw_asig', array('asignatura' => $_POST["id"]));
        /* Eliminamos el dato de la base de datos */
        $wpdb->delete('reservas_asignatura', array('id' => $_POST["id"]));
        /* Fin del borrado del dato ------------- */

        /* Devolvemos el resultado a jtable */
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
    }
}
/* La función main() se encarga de las llamadas al resto de funciones para realizar satisfactoriamente las
 * funciones solicitadas por esta clase. */
function main_course() {    
    load_path_course(); 
    generate_table_course_data();
} 
?>