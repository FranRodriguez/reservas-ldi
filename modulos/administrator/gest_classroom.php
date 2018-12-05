<?php

/**
 * Modulo de administración para la gestión de aulas del gestor de reservas
 */

/* Funcion principal que hace las llamadas al resto de funciones de la clase gest_classrom */
function administrar_aulas() {
    load_javascript_aula();
    comprobar_peticion_aula();
    load_html_aula();
    
}

/* Con esta función cargamos todo el código javascript necesario para el correcto funcionamiento de 
 * la interfaz de usuario del menú de administración para la gestión de las aulas */
function load_javascript_aula() {
    $URL_MOD = RESERVAS_DIRECCION.'/wp-content/plugins/reservas/plugins/wp-jtable/tables/';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            //Prepare jTable
            $('#PeopleTableContainer').jtable({
                paging: true,
                actions: {
                    listAction: '<?php echo $URL_MOD."table_classrom.php?action=list',"; ?>
                    updateAction: '<?php echo $URL_MOD."table_classrom.php?action=update',"; ?>
                    deleteAction: '<?php echo $URL_MOD."table_classrom.php?action=delete'"; ?>
                },
                fields: {
                    id: {
                        title: 'Id',
                        key: true,
                        list: false
                    },
                    nombre: {
                        title: 'nombre',
                        list: true,
                        delete: true
                    },
                    posicion: {
                        title: 'Posicion',
                        list: true,
                        delete: true
                    }
                }
            });
            $('#PeopleTableContainer').jtable('load');                
        });
    </script> 
    <?php
}

/* Funcion encargada de comprobar si se ha solicitado la realización de alguna petición de inserción o borrado de datos */
function comprobar_peticion_aula() {
    $modo = $_POST['modo'];
        
        if($modo=='insertar') {
            /* Vamos recogiendo las distintas variables para la inserción en la BBDD */
            $nombre = $_POST['nombre'];
            $posicion = $_POST['posicion'];            
            /* Fin de la recogida de parámetros ------------------------------------ */
            /* Realizamos la inserción en la base de datos de wordpress ------------ */
            database::set_aula($nombre,$posicion);
            /* Fin de la inserción ------------------------------------------------- */
        }
}

/* Función que carga el código html de la página de gestión de las aulas del gestor de reservas */
function load_html_aula() {
    ?>
        <h2>Aulas</h2>
        <?php alertas_aula(); ?><br/>
        <div id="crear_aula">
            <b>Crear un nuevo aula</b><br/><br/>
            <form action="<?php echo get_permalink(); ?>" method="POST">
                Nombre<br/>
                    <input type="text" name="nombre" required><br/>
                    <i>El nombre es cómo aparecerá el aula en el calendario</i><br/><br/>
                Posición<br/>
                    <input type="number" name="posicion" required><br/>
                    <i>Define la posición en la lista con respecto al resto de aulas</i><br/><br/>
                <input type="hidden" name="modo" value="insertar">
                <input type="submit" class="button button-primary" value="Añadir nuevo aula">
            </form>
        </div>
        <div id="gestion_aula">
            <b>Gestionar aulas</b><br/><br/>
            <div id="PeopleTableContainer"></div>
            <br/>
            <i>
                <b>Nota:</b><br/>
                Al borrar un aula borrarás todas las reservas adscritas a la misma. Además, se eliminará su 
                columna de la vista del calendario de reservas para los usuarios.
                <br/><br/>
                Puedes revisar las reservas desde la página <a href="admin.php?page=Reservas">Reservas</a>.
            </i>
        </div>
    <?php
}

/* Función encargada de notificar al usuario si se han realizado las acciones que ha solicitado */
function alertas_aula() {
    $modo = $_POST['modo']; // Determinamos si se ha solicitado y procesado alguna petición.

        if($modo=='insertar') { // Si hemos insertado, mostramos la notificación pertinente.
            echo "<div id=\"message\" class=\"updated\"><p>Nueva aula añadida con éxito.</div>";
        }
}