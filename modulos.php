<?php
/**
 * Cargamos todos los módulos requeridos por el plugin de gestion de reservas
 */
require_once RESERVAS_DIRECTORIO. '/modulos/administrator.php';
require_once RESERVAS_DIRECTORIO. '/modulos/database.php';
require_once RESERVAS_DIRECTORIO. '/modulos/user.php';
require_once RESERVAS_DIRECTORIO. '/modulos/calendar.php';

/* Modulos adicionales para la gestion del panel de administración */
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/create_schedule.php';
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/gest_schedule.php';
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/gest_classroom.php';
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/gest_course.php';
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/gest_degree.php';
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/gest_software.php';
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/gest_vm.php';
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/gest_options.php';
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/gest_data.php';

/* Modulos adicionales ocultos para la gestión del panel de administración */
require_once RESERVAS_DIRECTORIO. '/modulos/administrator/confirm_schedule.php';


