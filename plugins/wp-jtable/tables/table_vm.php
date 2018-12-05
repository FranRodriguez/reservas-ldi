<?php

/* 
 * Modulo de wp-jtable que atiende las solicitudes de gest_vm.php para la creación y visualización de la
 * tabla del menú de gestión de máquinas virtuales en el panel de administración del plugin de Wordpress 
 */

/* Llamamos a la función main(), que sera la encargada de realizar todas las llamadas a las funciones necesarias
 * para procesar las respuestas a las peticiones que la tabla de gestión de reservas solicite al programa 
 * ------------------------------------------------------------------------------------------------------ */
main_vm();
/* ------------------------------------------------------------------------------------------------------ */

/* Esta función carga las librerías de Wordpress necesarias para poder utilizar la interfaz de la base de datos
 * que proporciona el CMS para realizar las peticiones de MySQL */
function load_path_vm() {
    $path = $_SERVER['DOCUMENT_ROOT'];  // Cargamos el path con la ruta de instalacion de Wordpress

    /* Cargamos todas las clases de Wordpress que necesitamos utilizar */
    include_once $path . '/wp-config.php';
    include_once $path . '/wp-load.php';
    include_once $path . '/wp-includes/wp-db.php';
    include_once $path . '/wp-includes/pluggable.php';
    /* Fin de la carga ---------------------------------------------- */
}
/* La función generate_table_data es la verdadera encargada de procesar las peticiones de jtable para enviar la
 * respuesta que posteriormente mostrará las tablas del panel de administración del gestor de máquinas virtuales
 */
function generate_table_vm_data() {
    global $wpdb;

    // Si la solicitud es para 'listar el contenido':
    if($_GET["action"] == "list") { 
        /* Realizamos un conteo del número de máquinas virtuales que se mostrarán --------------------- */
        $recordCount = $wpdb->get_var("SELECT COUNT(*) AS RecordCount FROM reservas_maquinas_virtuales");
        /* Fin del conteo de tuplas ------------------------------------------------------------------- */
        /* Realizamos la peticion a la base de datos para obtener los datos --------------------------- */
        $rows = $wpdb->get_results("SELECT * FROM reservas_maquinas_virtuales");
        /* Fin de la recogida de datos ---------------------------------------------------------------- */  
        /* Devolvemos los resultados a jtable */
        $jTableResult = array();
        $jTableResult['Result'] = "OK";
        $jTableResult['TotalRecordCount'] = $recordCount;
        $jTableResult['Records'] = $rows;
        print json_encode($jTableResult);
        /* Fin del retorno de los datos ---- */
    }
    else if($_GET["action"] == "update") {  // Si se solicita la actualización de una tupla determinada:
        /* Actualizamos el dato en la base de datos */
        $wpdb->update(
            'reservas_maquinas_virtuales',
            array(
                'nombre' => $_POST["nombre"],
                'ssoo' => $_POST["ssoo"],
                'aplicaciones' => $_POST["aplicaciones"],
                'capacidad' => $_POST["capacidad"],
                'aulas' => $_POST["aulas"],
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
    else if($_GET["action"] == "delete") { // Si se solicita el borrado de una tupla determinada:
        /* Eliminamos las referencias a la maquina vitual de las asignaturas */
        $wpdb->delete('reservas_mv_asig', array('maquina_virtual' => $_POST["id"]));
        /* Fin del borrado de referencias ---------------------------------- */
        /* Eliminamos el dato de la base de datos */
        $wpdb->delete('reservas_maquinas_virtuales', array('id' => $_POST["id"]));
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
function main_vm() {    
    load_path_vm(); 
    generate_table_vm_data();
}   
?>