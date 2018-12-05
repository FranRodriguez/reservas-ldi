<?php

/* 
 * Modulo de wp-jtable que atiende las solicitudes de gest_degree.php para la creación y visualización de la
 * tabla del menú de gestión de los grados en el panel de administración del plugin de Wordpress 
 */

/* Llamamos a la función main(), que sera la encargada de realizar todas las llamadas a las funciones necesarias
 * para procesar las respuestas a las peticiones que la tabla de gestión de grados solicite al programa 
 * ------------------------------------------------------------------------------------------------------ */
main_degree();
/* ------------------------------------------------------------------------------------------------------ */

/* Esta función carga las librerías de Wordpress necesarias para poder utilizar la interfaz de la base de datos
 * que proporciona el CMS para realizar las peticiones de MySQL */
function load_path_degree() {
    $path = $_SERVER['DOCUMENT_ROOT'];  // Cargamos el path con la ruta de instalacion de Wordpress

    /* Cargamos todas las clases de Wordpress que necesitamos utilizar */
    include_once $path . '/wp-config.php';
    include_once $path . '/wp-load.php';
    include_once $path . '/wp-includes/wp-db.php';
    include_once $path . '/wp-includes/pluggable.php';
    /* Fin de la carga ---------------------------------------------- */
}
/* La función generate_table_data es la verdadera encargada de procesar las peticiones de jtable para enviar la
 * respuesta que posteriormente mostrará las tablas del panel de administración del gestor de grados
 */
function generate_table_degree_data() {
    global $wpdb;
        
    // Si la solicitud es para 'listar el contenido':
    if($_GET["action"] == "list") {
        /* Realizamos un conteo del número de softwares que se mostrarán --------------------- */
        $recordCount = $wpdb->get_var("SELECT COUNT(*) AS RecordCount FROM reservas_grado");
        /* Fin del conteo de tuplas ---------------------------------------------------------- */
        /* Realizamos la peticion a la base de datos para obtener los datos --------------------------- */
        $rows = $wpdb->get_results("SELECT * FROM reservas_grado");
        /* Fin de la recogida de datos ---------------------------------------------------------------- */ 
        /* Devolvemos los resultados a jtable */
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['TotalRecordCount'] = $recordCount;
        $jTableResult['Records'] = $rows;
        print json_encode($jTableResult);
        /* Fin del retorno de los datos ---- */
    }
    //Updating a record (updateAction)
    else if($_GET["action"] == "update") {
        /* Actualizamos el dato en la base de datos */
        $wpdb->update(
            'reservas_grado',
            array(
                'nombre' => $_POST["nombre"],
                'grupos' => $_POST["grupos"],
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
    //Deleting a record (deleteAction)
    else if($_GET["action"] == "delete") {
        $id = $_POST["id"];
        /* Obtenemos todas las asignaturas asociadas a la titulación */
        $asignaturas = $wpdb->get_results("SELECT * FROM reservas_asignatura WHERE grado=$id");
        /* Eliminamos todas las reservas de esas asignaturas */
        foreach($asignaturas as $asignatura) {
            $wpdb->delete('reservas_reservas', array('asignatura' => $asignatura->id));
        }
        /* Eliminamos también las asignaturas de la titulación */
        $wpdb->delete('reservas_asignatura', array('grado' => $id));
        /* Eliminamos el grado */
        $wpdb->delete('reservas_grado', array('id' => $id));
        /* Fin del borrado de datos ------------------------------ */

        /* Devolvemos el resultado a jtable */
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
    }
}
/* La función main() se encarga de las llamadas al resto de funciones para realizar satisfactoriamente las
 * funciones solicitadas por esta clase. */
function main_degree() {    
    load_path_degree(); 
    generate_table_degree_data();
} 
	
	
?>