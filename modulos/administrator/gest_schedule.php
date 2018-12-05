<?php

/* 
 * AdministraciÃ³n para la gestiÃ³n de las reservas en el panel de administraciÃ³n de Wordpress 
 * 
 */

/* FunciÃ³n que genera la interfaz de usuario para la adiciÃ³n de gestiÃ³n de las reservas desde 
 * el panel de administraciÃ³n de Wordpress */
function administrar_reservas() {
    load_html_reserva();
    load_javascript_reserva();
}

/* Con esta funciÃ³n cargamos todo el cÃ³digo javascript necesario para el correcto funcionamiento de la
 * la interfaz de usuario del menÃº de administraciÃ³n para la gestiÃ³n de las reservas existentes. */
function load_javascript_reserva() {
    $URL_MOD = RESERVAS_DIRECCION.'/wp-content/plugins/reservas/plugins/wp-jtable/tables/';
    ?>
    <script> 
        // Esta funcion se encarga de actualizar los datos de las tablas cuando se solicita una consulta a la BBDD
        function recargar_datos () {
            $('#PeopleTableContainer').jtable('load', {
                tipo_reserva: $('#tipo_reserva').val(),
                cuatrimestre: $('#cuatrimestre').val(),
                time_ini: $('#hora_ini').val() + ':' + $('#min_ini').val() + ':00',
                time_fin: $('#hora_fin').val() + ':' + $('#min_fin').val() + ':00',
                fecha_ini: $('#fecha_ini').val(),
                fecha_fin: $('#fecha_fin').val(), 
                aula: $('#aula').val(),
                profesor: $('#profesor').val(), 
                asignatura: $('#asignatura').val(), 
                dia: $('#dia').val() 
            });
        }
        // Esta funciÃ³n se encarga de dar valores por defecto a los campos de fecha para los datos mostrados
        // por defecto en la aplicacion.
        function poner_fecha () {
            var today = new Date();

            var dd = today.getDate();
            var mm = today.getMonth();
            var cuatrimestre = <?php echo definir_cuatrimestre_actual(); ?>;
            if(cuatrimestre==0) {
                inicio = today.getFullYear()+'-<?php echo obtener_fecha_cuatrimestre(0); ?>';
                fin = today.getFullYear()+'-<?php echo obtener_fin_cuatrimestre(0); ?>';
            }
            else if(cuatrimestre==1){
                inicio = today.getFullYear()+'-<?php echo obtener_fecha_cuatrimestre(1); ?>';
                fin = (today.getFullYear()+1)+'-<?php echo obtener_fin_cuatrimestre(2); ?>';
            }
            else if(cuatrimestre==2) {
                inicio = today.getFullYear()+'-<?php echo obtener_fecha_cuatrimestre(2); ?>';
                fin = today.getFullYear()+'-<?php echo obtener_fin_cuatrimestre(2); ?>';
            }
            $('#fecha_ini').val(inicio);
            $('#fecha_fin').val(fin);
        }
        // Esta funcion es la encargada de cargar la tabla de reservas en la interfaz del panel de administracion.
        jQuery(document).ready(function ($) {
            //Preparamos el plugin 'jtable':
            $('#PeopleTableContainer').jtable({
                paging: true,
                pageSize: 20,
                pageSizes: [10, 20, 30, 40],
                sorting: true,
                defaultSorting: 'id DESC',
                actions: {
                    listAction:   '<?php echo $URL_MOD."table_schedule.php?action=list',";  ?>
                    createAction: '<?php echo $URL_MOD."table_schedule.php?action=create',";?>
                    updateAction: '<?php echo $URL_MOD."table_schedule.php?action=update',";?>
                    deleteAction: '<?php echo $URL_MOD."table_schedule.php?action=delete'"; ?>   
                },
                fields: {
                    descripcion: {
                        title: 'Descripcion',
                        sorting: false,
                        edit: false,
                        width: '20%'
                    },
                    id: {
                        title: 'Id',
                        key: true,
                        create: false,
                        edit: false,
                        list: false,
                        width: '1%'
                    },
                    confirmada: {
                        title: 'confirmada',
                        list:true,
                        edit: true,
                        width: '5%',
                        options: {'0': 'No','1': 'Sí'}
                    },
                    asignatura: {
                        title: 'asignatura',
                        list:true,
                        edit:false
                    },
                    grupo: {
                        title: 'grupo',
                        list:true,
                        width: '1%'
                    },

                    profesor: {
                        title: 'profesor',
                        list: true,
                        width: '5%'
                    },
                    aula: {
                        title: 'Aula',
                        list: false,
                        options: <?php echo generar_opcion_aula() ?>
                    },
                    tipo_reserva: {
                        title: 'tipo_reserva',
                        list: false,
                        width: '1%',
                        options: {'0': 'Cuatrimestral','1': 'Puntual'}
                    },
                    dia: {
                        title: 'dia',
                        list:false,
                        options: {'Lunes': 'Lunes','Martes': 'Martes', 'Miércoles': 'Miércoles', 'Jueves': 'Jueves', 'Viernes': 'Viernes'}
                    },
                    cuatrimestre: {
                        title: 'cuatrimestre',
                        list: false,
                        width: '1%',
                        options: {'1': 'Primer Cuatrimestre','2': 'Segundo Cuatrimestre', '0': 'Verano'}
                    },
                    fecha: {
                        title: 'fecha',
                        type: 'date', 
                        displayFormat: "DD-dd-MM-yy",
                        create: true,
                        edit: true,
                        list: false
                    },
                    hora_inicio: {
                        title: 'hora_inicio',
                        list:false
                    },
                    hora_fin: {
                        title: 'hora_fin',
                        list:false
                    }
                }
            });
            // Funcion encargada de recargar los datos cuando hacemos una consulta.
            $('#LoadRecordsButton').click(function (e) {
                e.preventDefault();              
                recargar_datos();
             });
                poner_fecha();
                $('#LoadRecordsButton').click();
        });
    </script>
    <?php
}

/* COMIENZO DE LA ZONA CON FUNCIONES PARA DEFINIR EL HTML ---------------------------------------------------- */
/* ----------------------------------------------------------------------------------------------------------- */

/* Esta funciÃ³n carga el cÃ³digo html bÃ¡sico de la pÃ¡gina de gestiÃ³n de reservas para el panel de administraciÃ³n.
 * HarÃ¡ llamadas sucesivas a otras funciones para completar la interfaz con secciones mÃ¡s complejas de cÃ³digo.
 */
function load_html_reserva() {
    ?>
    <div class="wrap">
        <h2>Reservas <a href="admin.php?page=reservas_admin_page" class="add-new-h2">Añadir nueva</a></h2>
    </div>
    <?php comprobar_confirmacion(); alerta_pendientes();?></br>
    <br/>
    <div id="gestion_reserva">
        <div class="filtering">
            <form>
                <select name="Tipo Reserva" id="tipo_reserva">
                    <option selected="selected" value="2">Tipo de Reserva</option>
                    <option value="0">Cuatrimestral</option>
                    <option value="1">Puntual</option>
                </select>
                <select id="cuatrimestre" name="Cuatrimestre">
                    <option selected="selected" value="0">Cuatrimestre</option>
                    <option value="1">Primero</option>
                    <option value="2">Segundo</option>
                </select>
                <select name="aula" id="aula">
                    <option selected="selected" value="0">Aula</option>
                    <?php echo generar_aulas() ?>
                </select>
                <select id="asignatura" name="asignatura">
                    <option selected="selected" value="0">Asignatura</option>
                    <?php echo generar_asignaturas() ?>
                </select>
                <select id="profesor" name="profesor">
                    <option selected="selected" value="0">Profesor</option>
                    <?php echo generar_profesores() ?>
                </select>
                <select id="dia" name="dia">
                    <option selected="selected" value="0">Día</option>
                    <option value="Lunes">Lunes</option>
                    <option value="Martes">Martes</option>
                    <option value="Miércoles">Miércoles</option>
                    <option value="Jueves">Jueves</option>
                    <option value="Viernes">Viernes</option>
                </select><br/><br/>
                Desde el 
                <input type="date" id="fecha_ini" name="fecha_ini">     
                al 
                <input type="date" id="fecha_fin" name="fecha_fin">    
                Hora Inicio:
                <select id="hora_ini" name="hora_ini">
                    <option selected="selected" value="09">09</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option value="21">21</option>
                </select>
                :
                <select id="min_ini" name="min_ini">
                    <option selected="selected" value="00">00</option>
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="45">45</option>
                </select>      
                Hora Fin:
                <select id="hora_fin" name="hora_fin">
                    <option value="09">09</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                    <option value="13">13</option>
                    <option value="14">14</option>
                    <option value="15">15</option>
                    <option value="16">16</option>
                    <option value="17">17</option>
                    <option value="18">18</option>
                    <option value="19">19</option>
                    <option value="20">20</option>
                    <option selected="selected" value="21">21</option>
                </select>
                :
                <select id="min_fin" name="min_fin">
                    <option selected="selected" value="00">00</option>
                    <option value="15">15</option>
                    <option value="30">30</option>
                    <option value="45">45</option>
                </select>
                <button style="display: always;margin-top:5px;margin-left:5px" type="submit" class="button action" id="LoadRecordsButton">Cargar Resultados</button>
            </form>
        </div>
        <div id="PeopleTableContainer"></div>
    </div>   
    <?php
}

/* Esta funciÃ³n nos permite obtener, dada una peticion a la base de datos, los resultados de las reservas
 * solicitadas en el panel de la administraciÃ³n de reservas. */
function obtener_descripcion_array($tupla){
    $desc='';
    // Obtenemos cuatrimestre
    if ($tupla->cuatrimestre == 1)
        $desc=$desc.'1C, ';
    elseif ($tupla->cuatrimestre == 2)
        $desc=$desc.'2C, ';
    
    // Obtenemos aula
    $aula=database::get_aulas(id, $tupla->aula);
    $desc=$desc.$aula[0]->nombre.', ';
    
    //DÃƒÂ­a de la semana
    $desc=$desc.$tupla->dia.', de ';
    
    //Hora inicio
    $desc=$desc.$tupla->hora_inicio.' a ';
    
    //Hora fin
    $desc=$desc.$tupla->hora_fin.' (';
    
    //Tipo reserva
    if ($tupla->tipo_reserva == 1)
        $desc=$desc.'<b>puntual</b> ';
    elseif ($tupla->tipo_reserva == 0)
        $desc=$desc.'cuatrimestral ';
    
    //DÃƒÂ­a, mes
    $desc=$desc.$tupla->fecha.')';
    
    return $desc;
}

/* ----------------------------------------------------------------------------------------------------------- */
/* FIN DE LA ZONA DE DEFINICIÃ“N DEL HTML --------------------------------------------------------------------- */

/* Genera la lista de maquinas en el formulario de adhesion de nuevas mÃ¡quinas virtuales */
function generar_opcion_aula() {
    $lista_aulas = database::get_aulas(NULL,NULL);
    $num_aulas = count($lista_mv);
    $i = 1;
    $return = '{';
    foreach($lista_aulas as $aula) {
        $return .= "'$aula->id': '$aula->nombre'";
       
        if($i!=$num_mv) {
            $return .=',';
        }
        $i++;
    }
    $return .= '}';
    
    return $return;
}

/* Genera la lista de aulas para el formulario de filtrado de reservas */
function generar_aulas() {
        $lista_aulas = database::get_aulas(NULL,NULL);  // Realizamos la solicitud a la BBDD

        foreach($lista_aulas as $aula) {    // Vamos generando las distintas opciones
            $nombre = $aula->nombre;
            $id = $aula->id;

            $return .="<option value=$id>";
            $return .= $nombre;
            $return .= "</option>";
        }
        return $return; // Retornamos el cÃ³digo HTML como resultado. 
}

/* Genera la lista de aulas para el formulario de filtrado de reservas */
function generar_profesores() {
        $lista_profesores = database::get_profesores();  // Realizamos la solicitud a la BBDD

        foreach($lista_profesores as $profesor) {    // Vamos generando las distintas opciones
            $profesor = $profesor->profesor;
            //$id = $aula->id;

            $return .="<option value=$profesor>";
            $return .= database::get_profesor($profesor);
            $return .= "</option>";
        }
        return $return; // Retornamos el cÃ³digo HTML como resultado. 
}

/* Genera la lista de aulas para el formulario de filtrado de reservas */
function generar_asignaturas() {
        $lista_asignaturas = database::get_asignaturas();  // Realizamos la solicitud a la BBDD

        foreach($lista_asignaturas as $asignatura) {    // Vamos generando las distintas opciones
            $id = $asignatura->id;
            $asignatura = $asignatura->nombre;
            //$id = $aula->id;

            $return .="<option value=$id>";
            $return .= $asignatura;
            $return .= "</option>";
        }
        return $return; // Retornamos el cÃ³digo HTML como resultado. 
}

function comprobar_confirmacion() {
    $modo = $_POST['modo'];
    
    if($modo=="confirmar") {
        database::confirmar_reservas_pendientes();
        echo "<div id=\"message\" class=\"updated\"><p>Todas las reservas pendientes han sido confirmadas.</div>";
    }
}

function alerta_pendientes () {
    $reservas_pendientes = database::get_reservas_pendientes();
    if($reservas_pendientes>0) {
        ?>
        <div class="update-nag">
            <form id="confirmar" action="<?php echo get_permalink(); ?>" method="POST">
                Actualmente hay <?php echo $reservas_pendientes ?> <b>reservas pendientes</b> de confirmación. Si ha comprobado que son correctas, puede <a href="javascript:;" onclick="document.getElementById('confirmar').submit();">confirmar todas automáticamente</a>.
                <input type="hidden" name="modo" value="confirmar">
            </form>
        </div>
        <?php
    }
}