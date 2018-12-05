<?php

/**
 * Modulo de administración para la gestión de las máquinas virtuales del gestor de reservas
 */

/* Funcion principal que hace las llamadas al resto de funciones de la clase gest_vm */
function administrar_maquinas_virtuales() {
    load_javascript_mv();
    comprobar_peticion_mv();
    load_html_mv();
}

/* Con esta función cargamos todo el código javascript necesario para el correcto funcionamiento de la
 * la interfaz de usuario del menú de administración para la gestión de las maquinas virtuales */
function load_javascript_mv() {
    $URL_MOD = RESERVAS_DIRECCION.'/wp-content/plugins/reservas/plugins/wp-jtable/tables/';
    ?> 
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            //Prepare jTable
            $('#PeopleTableContainer').jtable({
                paging: true,
                pageSize: 15,
                actions: {
                    listAction: '<?php echo $URL_MOD."table_vm.php?action=list',"; ?>
                    updateAction: '<?php echo $URL_MOD."table_vm.php?action=update',"; ?>
                    deleteAction: '<?php echo $URL_MOD."table_vm.php?action=delete'"; ?>                                
                 },
                fields: {
                    id: {
                        title: 'Id',
                        key: true,
                        list: false
                    },
                    nombre: {
                        title: 'Nombre',
                        list: true,
                        edit: true
                    },
                    ssoo: {
                        title: 'Sistema Operativo',
                        list: true,
                        edit: true
                    },
                    aplicaciones: {
                        title: 'Aplicaciones',
                        list: true,
                        edit: true
                    },
                    capacidad: {
                        title: 'Capacidad',
                        list: true,
                        edit: true
                    },
                    aulas: {
                        title: 'Aulas',
                        list: true,
                        edit: true
                    },                                       
                }
            });
            $('#PeopleTableContainer').jtable('load');                
        });
    </script>
    <?php
}

/* Funcion encargada de comprobar si se ha solicitado la realización de alguna petición de inserción o borrado de datos */
function comprobar_peticion_mv() {
    $modo = $_POST['modo'];
        
        if($modo=='insertar') {
            /* Vamos recogiendo las distintas variables para la inserción en la BBDD */
            $nombre = $_POST['nombre'];
            $so = $_POST['so'];
            $aplicaciones = $_POST['aplicaciones'];
            $capacidad = $_POST['capacidad'];
            $aulas = $_POST['aulas'];
            
            /* Fin de la recogida de parámetros ------------------------------------ */
            /* Realizamos la inserción en la base de datos de wordpress ------------ */
            database::set_maquina_virtual($nombre,$so,$aplicaciones,$capacidad,$aulas);
            /* Fin de la inserción ------------------------------------------------- */
        }
}

/* Función que carga el código html de la página de gestión de las máquinas virtuales del gestor de reservas */
function load_html_mv() {
        ?>
        <div class="wrap">
            <h2>Máquinas Virtuales</h2>
        </div>
        <?php alertas_mv(); ?><br/>
        <div id="crear_aula">
            <b>Crear una nueva máquina virtual</b><br/><br/>
            <form action="<?php echo get_permalink(); ?>" method="POST">
                Nombre<br/>
                    <input type="text" name="nombre" required><br/>
                    <i>El nombre para identificar el programa</i><br/><br/>
                Sistema Operativo<br/>
                    <input type="text" name="so" required><br/>
                    <i>El sistema operativo instalado en la máquina virtual</i><br/><br/>
                Aplicaciones<br/>
                    <textarea rows="3" cols="35" name="aplicaciones" required></textarea><br/>
                    <i>Aplicaciones instaladas en la máquina virtual</i><br/><br/>
                Capacidad<br/>
                    <input type="text" name="capacidad" required><br/>
                    <i>Espacio en disco que ocupa la máquina virtual</i><br/><br/>
                Aulas<br/>
                    <input type="text" name="aulas" required><br/>
                    <i>Aulas en las que está instalada la máquina</i><br/><br/>
                <input type="hidden" name="modo" value="insertar">
                <input type="submit" class="button button-primary" value="Añadir nueva máquina virtual">
            </form>
        </div>
        <div id="gestion_aula">
            <b>Gestionar máquinas virtuales</b><br/><br/>
                <div id="PeopleTableContainer"></div>
            <br/>
            <i>
                <b>Nota:</b><br/>
                Al borrar una máquina virtual no borrarás aquellas asignaturas que la usan. En su lugar, se eliminarán
                las referencias a dicha máquina virtual en todas las asignaturas que alguna vez la utilizaron.
                <br/><br/>
                Puedes revisar las asignaturas y las máquinas virtuales que utilizan en la página <a href="admin.php?page=reservas_asig">Asignaturas</a>.
            </i>
        </div>        
        <?php
}

/* Función encargada de notificar al usuario si se han realizado las acciones que ha solicitado */
function alertas_mv() {
    $modo = $_POST['modo']; // Determinamos si se ha solicitado y procesado alguna petición.

    if($modo=='insertar') { // Si hemos insertado, mostramos la notificación pertinente.
        echo "<div id=\"message\" class=\"updated\"><p>Nueva máquina virtual añadida con éxito.</p></div>";
    }
}
