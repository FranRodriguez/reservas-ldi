<?php

/* 
 * Función que carga el panel de administración del gestor de reservas dentro de la interfaz de Wordpress
 */

add_action( 'admin_menu', 'reg_panel_admin' );  // Añadimos el menú adicional del plugin

/* Función encargada de insertar las distintas secciones del menú del gestor de reservas y las inserta en 
 * el panel de administración. */
function reg_panel_admin() {
      add_menu_page('Reservas','Reservas', 'manage_options', 'Reservas', 'gestion_reserva', WP_PLUGIN_URL . '/reservas/img/favicon.ico' );
      
      add_submenu_page( 'Reservas', 'Crear Reserva', 'Crear Reserva', 'manage_options', 'reservas_admin_page', 'crea_reserva');
      add_submenu_page( 'Reservas', 'Aulas', 'Aulas', 'manage_options', 'reservas_aula', 'gestion_aula');
      add_submenu_page( 'Reservas', 'Asignaturas', 'Asignaturas', 'manage_options', 'reservas_asig', 'gestion_asig');
      add_submenu_page( 'Reservas', 'Grados', 'Grados', 'manage_options', 'reservas_grados', 'gestion_grados');
      add_submenu_page( 'Reservas', 'Software', 'Software', 'manage_options', 'reservas_sw', 'gestion_software');
      add_submenu_page( 'Reservas', 'Máquinas Virtuales', 'Máquinas Virtuales', 'manage_options', 'reservas_mvs', 'gestion_mvs');
      add_submenu_page( 'Reservas', 'Informes', 'Informes', 'manage_options', 'reservas_informes', 'gestion_data');
      add_submenu_page( 'Reservas', 'Opciones', 'Opciones', 'manage_options', 'reservas_options', 'gestion_options');
      
      add_submenu_page( 'Crear Reserva', 'Confirmar Reserva', 'Crear Reserva', 'manage_options', 'reservas_prueba_page', 'confirm_schedule');

      
}

/* Cargamos la página de gestión de reservas */
function gestion_reserva() {
    administrar_reservas();
}

/* Cargamos la página de creación de reservas */
function crea_reserva(){
    crearReserva();
}

/* Cargamos la página de gestión de las aulas */
function gestion_aula(){
    administrar_aulas();
}

/* Cargamos la página de gestión de las asignaturas */
function gestion_asig(){
    administrar_asignaturas();
}

/* Cargamos la página de gestión de los grados */
function gestion_grados(){
    administrar_grados();
}

/* Cargamos la página de gestión de software */
function gestion_software(){
    administrar_software();
}

/* Cargamos la página de gestión de máquinas virtuales */
function gestion_mvs(){
    administrar_maquinas_virtuales();
}

/* Cargamos la página de informes */
function gestion_data() {
    administrar_informes();
}

/* Cargamos la página de opciones */
function gestion_options() {
    administrar_opciones();
}

/* Cargamos la página (oculta) de confirmacion de reservas */
/* Cargamos la página de gestión de reservas */
function confirm_schedule() {
    confirmar_reservas();
}