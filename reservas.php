<?php
/*
Plugin Name: Gestor de Reservas
Plugin URI: http://www.lab.inf.uc3m.es
Description:   Módulo gestor del sistema de reserva de las aulas pertenecientes al Laboratorio del Departamento de Informática de la Universidad Carlos III de Madrid, que proporciona una interfaz gráfica para la gestión y visualización de las reservas en dichas aulas. Adicionalmente, este software también incluye un gestor del software instalado y las máquinas virtuales usadas en los laboratorios.
Version: 1.0.3
Author: Francisco Javier Rodríguez Isabel
Author URI: https://plus.google.com/111466696577492868907

Copyright 2015  Francisco Javier Rodríguez Isabel  (email : fran@lab.inf.uc3m.es)
*/

/* Definimos las variables globales con los valores de ubicacion del plugin (si no estÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡n creadas ya) */
if ( ! defined( 'RESERVAS_UBICACION_BASE' ) )
    define( 'RESERVAS_UBICACION_BASE', plugin_basename( __FILE__ ) );   
    //RESERVAS_UBICACION_BASE: contiene la ruta relativa del archivo reservas.php (este archivo)

if ( ! defined( 'RESERVAS_RAIZ' ) )
    define( 'RESERVAS_RAIZ', trim( dirname( RESERVAS_UBICACION_BASE ), '/' ) ); 
    //RESERVAS_RAIZ: contiene el nombre de la carpeta en la que se aloja el el plugin

if ( ! defined( 'RESERVAS_DIRECTORIO' ) )
    define( 'RESERVAS_DIRECTORIO', WP_PLUGIN_DIR . '/' . RESERVAS_RAIZ );   
    //RESERVAS_DIRECTORIO: contiene la ruta completa de la carpeta del plugin en el servidor (ej: /var/www/wordpres...)

if ( ! defined( 'RESERVAS_DIRECCION' ) ) {
    $url = explode(":",network_site_url( '/' ));
    define( 'RESERVAS_DIRECCION', $url[1]);
}

if ( ! defined( 'RESERVAS_URL' ) )
    //define( 'RESERVAS_URL', WP_PLUGIN_URL . '/' . RESERVAS_RAIZ );
    define( 'RESERVAS_URL', RESERVAS_DIRECCION . '/wp-content/plugins/' . RESERVAS_RAIZ );   
    //RESERVAS_URL: contiene la url de la carpeta del plugin (ej: http://www.lab.inf.uc3m.es/wp-content...)

/* FIN --------------------------------------------------------------------------------------------  */

/* Cargamos los mÃƒÆ’Ã‚Â³dulos requeridos por la aplicaciÃƒÆ’Ã‚Â³n */
require_once RESERVAS_DIRECTORIO . '/modulos.php';
/* FIN --------------------------------------------- */

/* Creamos las tablas en la base de datos en caso de que sea la primera vez que activamos el plugin */
add_action( 'init', 'crearTablas' );

function crearTablas() {
    global $wpdb;

    $sql = "SHOW TABLES IN `" . $wpdb->dbname . "` LIKE 'reservas_reservas'";
    $tables = $wpdb->query( $sql );
    if ( $tables == 0 ) {
        database::definirTablas();
        database::generarOpciones();
    }
}
/* FIN --------------------------------------------------------------------------------------------- */

/* Cargamos los estilos y scripts necesarios para el funcionamiento del plugin */
wp_enqueue_style ('jquery-ui-1.8.16.custom.css', RESERVAS_URL.'/plugins/wp-jtable/themes/redmond/jquery-ui-1.8.16.custom.css');
wp_enqueue_style ('jtable.css', RESERVAS_URL.'/plugins/wp-jtable/scripts/jtable/themes/metro/lightgray/jtable.css');

wp_enqueue_script ('jquery-1.11.0.js', RESERVAS_URL.'/plugins/wp-jtable/scripts/jquery-1.11.0.js');
wp_enqueue_script ('jquery-ui-1.10.4.custom.min.js', RESERVAS_URL.'/plugins/wp-jtable/scripts/jquery-ui-1.10.4.custom.min.js');
wp_enqueue_script ('date-es-ES.js', RESERVAS_URL.'/plugins/datejs/date-es-ES.js');
wp_enqueue_script ('jquery.jtable.js', RESERVAS_URL.'/plugins/wp-jtable/scripts/jtable/jquery.jtable.js');
wp_enqueue_script ('jquery.jtable.es.js', RESERVAS_URL.'/plugins/wp-jtable/scripts/jtable/localization/jquery.jtable.es.js');
/* FIN ----------------------------------------------------------------------- */

wp_enqueue_style ('data.css', RESERVAS_URL.'/css/data.css');
wp_enqueue_style ('course.css', RESERVAS_URL.'/css/course.css');
wp_enqueue_style ('software.css', RESERVAS_URL.'/css/software.css');
wp_enqueue_style ('degree.css', RESERVAS_URL.'/css/degree.css');
wp_enqueue_style ('classroom.css', RESERVAS_URL.'/css/classroom.css');
wp_enqueue_style ('create_schedule.css', RESERVAS_URL.'/css/create_schedule.css');
wp_enqueue_style ('gest_schedule.css', RESERVAS_URL.'/css/gest_schedule.css');
wp_enqueue_style ('calendar.css', RESERVAS_URL.'/css/calendar.css');








