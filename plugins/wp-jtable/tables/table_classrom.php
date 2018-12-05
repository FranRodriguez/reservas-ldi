<?php

/* 
 * Modulo de wp-jtable que atiende las solicitudes de gest_classrom.php para la creación y visualización de la
 * tabla del menú de gestión de las aulas en el panel de administración del plugin de Wordpress 
 */

/* Llamamos a la función main(), que sera la encargada de realizar todas las llamadas a las funciones necesarias
 * para procesar las respuestas a las peticiones que la tabla de gestión de reservas solicite al programa 
 * ------------------------------------------------------------------------------------------------------ */
main_classrom();
/* ------------------------------------------------------------------------------------------------------ */

/* Esta función carga las librerías de Wordpress necesarias para poder utilizar la interfaz de la base de datos
 * que proporciona el CMS para realizar las peticiones de MySQL */
function load_path_classrom() {
    $path = $_SERVER['DOCUMENT_ROOT'];  // Cargamos el path con la ruta de instalacion de Wordpress

    /* Cargamos todas las clases de Wordpress que necesitamos utilizar */
    include_once $path . '/wp-config.php';
    include_once $path . '/wp-load.php';
    include_once $path . '/wp-includes/wp-db.php';
    include_once $path . '/wp-includes/pluggable.php';
    /* Fin de la carga ---------------------------------------------- */
}
/* La función generate_table_data es la verdadera encargada de procesar las peticiones de jtable para enviar la
 * respuesta que posteriormente mostrará las tablas del panel de administración del gestor de aulas
 */
function generate_table_classrom_data() {
    global $wpdb;
    
    // Si la solicitud es para listar contenido:
    if($_GET["action"] == "list") {
        /* Realizamos un conteo del número de máquinas virtuales que se mostrarán --------------------- */
        $recordCount = $wpdb->get_var("SELECT COUNT(*) AS RecordCount FROM reservas_aulas");
        /* Fin del conteo de tuplas ------------------------------------------------------------------- */
        /* Realizamos la peticion a la base de datos para obtener los datos --------------------------- */
        $rows = $wpdb->get_results("SELECT * FROM reservas_aulas");
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
        /* Comprobamos que los datos introducidos son correctos */
        if(!is_numeric($_POST["posicion"])) {
            /* Devolvemos el resultado a jtable */
            $jTableResult = array();
            $jTableResult['Result'] = "ERROR";
            $jTableResult['Message'] = "El valor del campo Posicion debe ser un entero";
            print json_encode($jTableResult);
            return -1;
            /* Fin del retorno de los datos ---- */
        }
        /* Actualizamos el dato en la base de datos */
        $wpdb->update(
            'reservas_aulas',
            array(
                'nombre' => $_POST["nombre"],
                'posicion' => $_POST["posicion"],
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
        /* Eliminamos el dato de la base de datos */
        $wpdb->delete('reservas_aulas', array('id' => $_POST["id"]));
        /* Fin del borrado del dato ------------- */

        /* Devolvemos el resultado a jtable */
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        print json_encode($jTableResult);
        /* Fin del retorno de los datos ---- */
    }
}
/* La función main() se encarga de las llamadas al resto de funciones para realizar satisfactoriamente las
 * funciones solicitadas por esta clase. */
function main_classrom() {    
    load_path_classrom(); 
    generate_table_classrom_data();
}
?>