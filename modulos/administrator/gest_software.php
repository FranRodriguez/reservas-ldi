<?php

/* 
 * Administración para la gestión del software en el panel de administración de Wordpress 
 * 
 */

/* Función que genera la interfaz de usuario para la adición de gestión del software desde 
 * el panel de administración de Wordpress */
function administrar_software() {
    load_javascript_software();
   comprobar_peticion_software();
   load_html_software();
}

/* Con esta función cargamos todo el código javascript necesario para el correcto funcionamiento de
 * la interfaz de usuario del menú de administración para la gestión de software. */
function load_javascript_software() {
    $URL_MOD = RESERVAS_DIRECCION.'/wp-content/plugins/reservas/plugins/wp-jtable/tables/';
    ?> 
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            //Prepare jTable
            $('#PeopleTableContainer').jtable({
                paging: true,
                pageSize: 10,
                actions: {
                    listAction: '<?php echo $URL_MOD."table_software.php?action=list',"; ?>
                    updateAction: '<?php  echo $URL_MOD."table_software.php?action=update',"; ?>
                    deleteAction: '<?php echo $URL_MOD."table_software.php?action=delete'"; ?>                                                              
                },
                fields: {
                    id: {
                        title: 'Id',
                        list: false,
                        key: true
                    },
                    nombre: {
                        title: 'nombre',
                        list: true,
                        edit: true
                    },
                    version: {
                        title: 'version',
                        list: true,
                        edit: true
                    },
                    url: {
                        title: 'url',
                        list: true,
                        edit: true
                    },
                    descripcion: {
                        title: 'descripcion',
                        list: true,
                        edit: true
                    },
                    fecha_instalacion: {
                        title: 'Fecha Instalación',
                        type: 'date',
                        list: true,
                        edit: true
                    },                                
                    instalacion: {
                        title: 'Instalación',
                        list: true,
                        edit: true
                    }
                }
            });
            $('#PeopleTableContainer').jtable('load');                
        });
    </script>
    <?php 
}

/* Funcion encargada de comprobar si se ha solicitado la realización de alguna petición de inserción o borrado de datos */
function comprobar_peticion_software() {
    $modo = $_POST['modo'];
        
        if($modo=='insertar') {
            /* Vamos recogiendo las distintas variables para la inserción en la BBDD */
            $nombre = $_POST['nombre'];
            $version = $_POST['version'];
            $url = $_POST['url'];
            $descripcion = $_POST['descripcion'];
            $fecha = $_POST['fecha'];
            $instalacion = $_POST['instalacion'];
            
            /* Fin de la recogida de parámetros ------------------------------------ */
            /* Realizamos la inserción en la base de datos de wordpress ------------ */
            database::set_software($nombre,$version,$url,$descripcion,$fecha,$instalacion);
            /* Fin de la inserción ------------------------------------------------- */
        }
}

/* Función que carga el código html de la página de gestión de software del gestor de reservas */
function load_html_software() {
        ?>
        <h2>Software</h2>
        <?php alertas_software(); ?><br/>
        <div id="crear_aula">
            <b>Crear un nuevo software</b><br/><br/>
            <form action="<?php echo get_permalink(); ?>" method="POST">
                Nombre<br/>
                    <input type="text" name="nombre" required><br/>
                    <i>El nombre para identificar el programa</i><br/><br/>
                Versión<br/>
                    <input type="text" name="version" required><br/>
                    <i>La versión del software instalada en las aulas</i><br/><br/>
                Url<br/>
                    <input type="text" name="url" required><br/>
                    <i>Dirección a la web oficial de descarga del software</i><br/><br/>
                Descripción<br/>
                    <textarea rows="3" cols="35" name="descripcion" required></textarea><br/>
                    <i>Información sobre qué hace el programa</i><br/><br/>
                Fecha de instalación<br/>
                    <input type="date" name="fecha" required><br/>
                    <i>El día en el que se instaló el software</i><br/><br/>
                Instrucciones de instalación<br/>
                    <textarea rows="3" cols="35" name="instalacion" required></textarea><br/>
                    <i>Instrucciones para la correcta instalación</i><br/><br/>
                <input type="hidden" name="modo" value="insertar">
                <input type="submit" class="button button-primary" value="Añadir nuevo software">
            </form>
        </div>
        <div id="gestion_aula" >
            <b>Gestionar software</b><br/><br/>
            
            <div id="PeopleTableContainer"></div>

               
            <br/>
            <i>
                <b>Nota:</b><br/>
                Al borrar un software no borrarás aquellas asignaturas que lo usan. En su lugar, se eliminarán
                las referencias a dicho software en todas las asignaturas que alguna vez lo utilizaron.
                <br/><br/>
                Puedes revisar las asignaturas y el software que utilizan en la página <a href="admin.php?page=reservas_asig">Asignaturas</a>.
            </i>         
        </div>
    <?php
}

/* Función encargada de notificar al usuario si se han realizado las acciones que ha solicitado */
function alertas_software() {
    $modo = $_POST['modo']; // Determinamos si se ha solicitado y procesado alguna petición.

    if($modo=='insertar') { // Si hemos insertado, mostramos la notificación pertinente.
        echo "<div id=\"message\" class=\"updated\"><p>Nuevo software añadido con éxito.</p></div>";
    }
}