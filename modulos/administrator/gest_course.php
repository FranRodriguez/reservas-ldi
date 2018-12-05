<?php

/**
 * Modulo de administración para la gestión de las asignaturas del gestor de reservas
 */

/* Funcion principal que hace las llamadas al resto de funciones de la clase gest_course */
function administrar_asignaturas() {
    load_javascript_asignatura();
    comprobar_peticion_asignatura();
    load_html_asignatura();
}

/* Con esta función cargamos todo el código javascript necesario para el correcto funcionamiento de
 * la interfaz de usuario del menú de administración para la gestión de las asignaturas */
function load_javascript_asignatura() {
    $URL_MOD = RESERVAS_DIRECCION.'/wp-content/plugins/reservas/plugins/wp-jtable/tables/';
    $URL_IMG = RESERVAS_DIRECCION.'/wp-content/plugins/reservas/img/';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            //Prepare jTable
            $('#PeopleTableContainer').jtable({
                paging: true,
                pageSize: 20, 
                sorting: true,
                defaultSorting: 'id DESC',
                openChildAsAccordion: true,
                actions: {
                    listAction: '<?php echo $URL_MOD."table_course.php?action=list',"; ?>
                    updateAction: '<?php echo $URL_MOD."table_course.php?action=update',"; ?>
                    deleteAction: '<?php echo $URL_MOD."table_course.php?action=delete'"; ?>                                                               
                },
                fields: {                                
                    id: {
                        title: 'Id',
                        list: false,
                        key: true
                    },
                    nombre: {
                        title: 'Nombre',
                        list: true,
                        edit: true
                    },
                    grado: {
                        title: 'Grado',
                        list: true,
                        edit: true
                    },
                    cuatrimestre: {
                        title: 'Cuatrimestre',
                        width: '2%',
                        list: true,
                        edit: true
                    },         
                    curso: {
                        title: 'Curso',
                        width: '2%',
                        list: true,
                        edit: true
                    },
                    sw: {
                        title: '',
                        edit: false,
                        create: true,
                        width: '2%',
                        sorting: false,
                        display: function (asigData) {
                            //Create an image that will be used to open child table
                            var $img = $('<img src="<?php echo $URL_IMG?>sw.png" title="Software Asociado" align="center" />');
                            //Open child table when user clicks the image
                            $img.click(function () {
                                $('#PeopleTableContainer').jtable('openChildTable',
                                    $img.closest('tr'),
                                    {
                                        title:'Software requerido',
                                        actions: {
                                            listAction: '<?php echo $URL_MOD."table_sw_course.php?action=list&id=";?>' + asigData.record.id,
                                            createAction: '<?php echo $URL_MOD."table_sw_course.php?action=create&id="; ?>' + asigData.record.id,
                                            deleteAction: '<?php echo $URL_MOD."table_sw_course.php?action=delete&id=";?>' + asigData.record.id
                                        },
                                        fields: {
                                            asignatura: {
                                                title: 'asignatura',
                                                type: 'hidden',
                                                defaultValue: asigData.record.id
                                            },
                                            software: {
                                                title: 'Nombre',
                                                create: true,
                                                key: true,
                                                options: <?php echo generar_opcion_software() ?>
                                                
                                            },
                                            version: {
                                                title: 'Version',
                                                create: false
                                            }
                                        }
                                    }, function (data) { //opened handler
                                        data.childTable.jtable('load');
                                    });
                            });
                            //Return image to show on the person row
                            return $img;
                        }                                    
                    },
                    mv: {
                        title: '',
                        edit: false,
                        create: true,
                        sorting: false,
                        width: '2%',
                        display: function (asigData) {
                            //Create an image that will be used to open child table
                            var $img = $('<img src="<?php echo $URL_IMG?>vm.png" title="Máquinas Virtuales Asociadas" align="center" />');
                            //Open child table when user clicks the image
                            $img.click(function () {
                                $('#PeopleTableContainer').jtable('openChildTable',
                                    $img.closest('tr'),
                                    {
                                        title:'Máquinas Virtuales requeridas',
                                        actions: {
                                            listAction: '<?php echo $URL_MOD."table_mv_course.php?action=list&id="?>' + asigData.record.id,
                                            createAction: '<?php echo $URL_MOD."table_mv_course.php?action=create&id="; ?>' + asigData.record.id,
                                            deleteAction: '<?php echo $URL_MOD."table_mv_course.php?action=delete&id=";?>' + asigData.record.id
                                        },
                                        fields: {
                                            asignatura: {
                                                title: 'asignatura',
                                                type: 'hidden',
                                                defaultValue: asigData.record.id
                                            },
                                            maquina_virtual: {
                                                title: 'Nombre',
                                                create: true,
                                                edit: false,
                                                key: true,
                                                options: <?php echo generar_opcion_mv() ?>
                                            },
                                            capacidad: {
                                                title: 'Capacidad',
                                                create: false,
                                                edit: false
                                            },
                                            ssoo: {
                                                title: 'SSOO',
                                                create: false,
                                                edit: false
                                            },                                                             
                                            aulas: {
                                                title: 'Aulas',
                                                create: false,
                                                edit: false
                                            }
                                        }
                                    }, function (data) { //opened handler
                                        data.childTable.jtable('load');
                                });
                            });
                            //Return image to show on the person row
                            return $img;
                        }                                   
                    }
                }
            });
            $('#PeopleTableContainer').jtable('load');                
        });
    </script> 
    <?php
}

/* Funcion encargada de comprobar si se ha solicitado la realización de alguna petición de inserción o borrado de datos */
function comprobar_peticion_asignatura() {
    $modo = $_POST['modo'];
        
        if($modo=='insertar') {
            /* Vamos recogiendo las distintas variables para la inserción en la BBDD */
            $nombre = $_POST['nombre'];
            $grado = $_POST['grado'];
            $curso = $_POST['curso'];
            $cuatrimestre = $_POST['cuatrimestre'];
            $num_software = $_POST['num_software'];
            
            $software = "";            
            for($i=0;$i<$num_software;$i++) {
                $numero = "software$i";
                $lista_software = $_POST[$numero];
                
                if($lista_software!="") {
                     $software .="$lista_software,";
                }
               
            }            
            
            $num_maquinas = $_POST['num_maquina'];
            for($j=0;$j<$num_maquinas;$j++) {
                $numero = "maquina$j";
                $lista_maquinas = $_POST[$numero];

                if($lista_maquinas!="") {
                    $maquinas .="$lista_maquinas,";
                }
            }  
            /* Fin de la recogida de parámetros ------------------------------------ */
            /* Realizamos la inserción en la base de datos de wordpress ------------ */
            database::set_asignatura($nombre,$grado,$curso,$cuatrimestre,$software,$maquinas);
            /* Fin de la inserción ------------------------------------------------- */
        }
}

/* Función que carga el código html de la página de gestión de asignaturas del gestor de reservas */
function load_html_asignatura() {
        ?>
        <h2>Asignatura</h2>
        <?php alertas_asignatura(); ?><br/>
        <div id="crear_aula">
            <b>Crear una nueva asignatura</b><br/><br/>
            <form action="<?php echo get_permalink(); ?>" method="POST">
                Nombre<br/>
                    <input type="text" name="nombre" required><br/>
                    <i>El nombre es cómo aparecerá la asignatura en la BBDD</i><br/><br/>
                Grado<br/>
                    <?php echo generar_seleccion_grados() ?><br/>
                    <i>Corresponde con la titulación a la que pertenece la asignatura</i><br/><br/>
                Curso<br/>
                    <select name="curso">
                        <option value="1">Primero</option>
                        <option value="2">Segundo</option>
                        <option value="3">Tercero</option>
                        <option value="4">Cuarto</option>
                    </select><br/>
                    <i>El año de la titulación en que se cursa la asignatura</i><br/><br/>
                Cuatrimestre <br/>
                    <select name="cuatrimestre">
                        <option value="1">Primer Cuatrimestre</option>
                        <option value="2">Segundo Cuatrimestre</option>
                    </select><br/>
                    <i>Cuatrimestre para el que se imparte la asignatura según el plan de la titulación.</i><br/><br/>
                Software<br/>
                    <div id="software">
                        <?php echo generar_seleccion_software() ?>
                    </div><br/>
                    <i>El software utilizado durante las clases de dicha asignatura</i><br/><br/>
                Máquinas Virtuales<br/>
                    <div id="mvs">
                        <?php echo generar_seleccion_maquinas_virtuales() ?>
                    </div><br/>
                    <i>Las máquinas virtuales utilizadas durante las clases</i><br/><br/>
                <input type="hidden" name="modo" value="insertar">
                <input type="submit" class="button button-primary" value="Añadir nueva asignatura">
            </form>
        </div>
        <div id="gestion_aula">
            <b>Gestionar asignaturas</b><br/><br/>

                <div id="PeopleTableContainer"></div>
      
            <br/>
            <i>
                <b>Nota:</b><br/>
                Al borrar una asignatura borrarás de forma permanente todas las reservas asociadas a la misma.
                Sin embargo, no se verán afectados ni el software ni las máquinas virtuales usadas para 
                impartirla.                
                <br/><br/>
                Puedes revisar las Reservas disponibles en la página <a href="admin.php?page=Reservas">Reservas</a>.
            </i>
        </div>
    <?php
}

/* Función encargada de notificar al usuario si se han realizado las acciones que ha solicitado */
function alertas_asignatura() {
    $modo = $_POST['modo']; // Determinamos si se ha solicitado y procesado alguna petición.

    if($modo=='insertar') { // Si hemos insertado, mostramos la notificación pertinente.
        echo "<div id=\"message\" class=\"updated\"><p>Nueva asignatura añadida con éxito.</div>";
    }
}

/* Genera la lista de grados en el formulario de creación de asignaturas */
function generar_seleccion_grados() {
    $lista_grados = database::get_grado(NULL,NULL);
    
    $return = "<select name=\"grado\" required>";
    foreach($lista_grados as $grado) {
        $nombre = $grado->nombre;        
        
        $return .= "<option value=\"$grado->id\">";
        $return .= $nombre;
        $return .='</option>';
    }
    $return .= '</select>';

    return $return;
}

/* Genera la lista de software en el formulario de creación de asignaturas */
function generar_seleccion_software() {
    $lista_software = database::get_software(NULL,NULL);
    $num_software = count($lista_software);
    $return = "<input type=\"hidden\" name=\"num_software\" value=\"$num_software\">";
    $i = 0;
    foreach($lista_software as $software) {
        $nombre = $software->nombre;
        
        $return.= "<input type=\"checkbox\" name=\"software$i\" value=\"$software->id\">$nombre<br>";
        $i++;
    }
    
    return $return;
}

/* Genera la lista de software en el formulario de adhesion de nuevo software */
function generar_opcion_software() {
    $lista_software = database::get_software(NULL,NULL);
    $num_software = count($lista_software);
    $i = 1;
    $return = '{';
    foreach($lista_software as $software) {
        $return .= "'$software->id': '$software->nombre'";
        
        if($i!=$num_software) {
            $return .=',';
        }
        $i++;
    }
    $return .= '}';
    
    return $return;
}

/* Genera la lista de maquinas en el formulario de creación de asignaturas */
function generar_seleccion_maquinas_virtuales() {
    $lista_maquinas = database::get_maquinas_virtuales(NULL,NULL);
    $num_maquinas = count($lista_maquinas);
    $return = "<input type=\"hidden\" name=\"num_maquina\" value=\"$num_maquinas\">";
    $i = 0;
    foreach($lista_maquinas as $maquina) {
        $nombre = $maquina->nombre;
        
        $return.= "<input type=\"checkbox\" name=\"maquina$i\" value=\"$maquina->id\">$nombre<br>";
        $i++;
    } 
    
    return $return;
}

/* Genera la lista de maquinas en el formulario de adhesion de nuevas máquinas virtuales */
function generar_opcion_mv() {
    $lista_mv = database::get_maquinas_virtuales(NULL,NULL);
    $num_mv = count($lista_mv);
    $i = 1;
    $return = '{';
    foreach($lista_mv as $maquina) {
        $return .= "'$maquina->id': '$maquina->nombre'";
        
        if($i!=$num_mv) {
            $return .=',';
        }
        $i++;
    }
    $return .= '}';
    
    return $return;
}