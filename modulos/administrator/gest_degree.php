<?php

/**
 * Modulo de administración para la gestión de los grados del gestor de reservas
 */

/* Funcion principal que hace las llamadas al resto de funciones de la clase gest_degree */
function administrar_grados() {
    load_javascript_grado();
    comprobar_peticion_grado();
    load_html_grado();
}

/* Con esta función cargamos todo el código javascript necesario para el correcto funcionamiento de
 * la interfaz de usuario del menú de administración para la gestión de los grados */
function load_javascript_grado() {
    $URL_MOD = RESERVAS_DIRECCION.'/wp-content/plugins/reservas/plugins/wp-jtable/tables/';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            //Prepare jTable
            $('#PeopleTableContainer').jtable({
                paging: true,
                pageSize: 10,                            
                actions: {
                    listAction: '<?php echo $URL_MOD."table_degree.php?action=list',";?>
                    updateAction: '<?php echo $URL_MOD."table_degree.php?action=update',";?>
                    deleteAction: '<?php echo $URL_MOD."table_degree.php?action=delete'";?>                                                                
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
                    grupos: {
                        title: 'Grupos',
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
function comprobar_peticion_grado() {
    $modo = $_POST['modo'];
        
        if($modo=='insertar') {
            /* Vamos recogiendo las distintas variables para la inserción en la BBDD */
            $nombre = $_POST['nombre'];
            $grupos = $_POST['grupos'];
            /* Fin de la recogida de parámetros ------------------------------------ */
            /* Realizamos la inserción en la base de datos de wordpress ------------ */
            database::set_grado($nombre,$grupos);
            /* Fin de la inserción ------------------------------------------------- */
        }
}

/* Función que carga el código html de la página de gestión de las máquinas virtuales del gestor de reservas */
function load_html_grado() {
        ?>
        <h2>Grados</h2>
        <?php alertas_grado(); ?><br/>
        <div id="crear_grado">
            <b>Crear un nuevo grado</b><br/><br/>
            <form action="<?php echo get_permalink(); ?>" method="POST">
                Nombre<br/>
                    <input type="text" name="nombre" required><br/>
                    <i>El nombre es cómo aparecerá el grado en la BBDD</i><br/><br/>
                Grupos<br/>
                    <input type="text" name="grupos" required><br/>
                    <i>Los diferentes grupos, en formato numérico y separados por comas, que tiene asignados la titulación</i><br/><br/>
                <input type="hidden" name="modo" value="insertar">
                <input type="submit" class="button button-primary" value="Añadir nuevo grado">
            </form>
        </div>
        <div id="gestion_grado">
            <b>Gestionar grados</b><br/><br/>
            <div id="PeopleTableContainer"></div>       
            <br/>
            <i>
                <b>Nota:</b><br/>
                Al borrar un grado borrarás de forma permanente todas las asignaturas asociadas a dicha 
                titulación, y por consiguiente también se eliminarán de la base de datos las reservas de
                dichas asignaturas.
                <br/><br/>
                Puedes revisar las asignaturas en la página <a href="admin.php?page=reservas_asig">Asignaturas</a> 
                y las reservas disponibles en la página <a href="admin.php?page=Reservas">Reservas</a>.
            </i>
        </div>
    <?php
}

/* Función encargada de notificar al usuario si se han realizado las acciones que ha solicitado */
function alertas_grado() {
    $modo = $_POST['modo']; // Determinamos si se ha solicitado y procesado alguna petición.

    if($modo=='insertar') { // Si hemos insertado, mostramos la notificación pertinente.
        echo "<div id=\"message\" class=\"updated\"><p>Nuevo grado añadido con éxito.</p></div>";
    }
}