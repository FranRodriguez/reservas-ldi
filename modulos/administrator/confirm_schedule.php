<?php

/**
 * Modulo de administración para la confirmación de reservas a través de correo electrónico del plugin
 */

/* Funcion principal que hace las llamadas al resto de funciones de la clase confirm_schedule */
function confirmar_reservas() { 
    comprobar_peticion_confirmacion();
    load_html_confirmacion();
}

/* Funcion que comprueba las peticiones de confirmacion y actualiza los datos en la base de datos*/
function comprobar_peticion_confirmacion() {
    if($_POST["modo"]=="confirmar") {
        global $wpdb;
        $id = $_POST['id']; // Recogemos el id de reserva
        
        // Actualizamos la reserva
        $wpdb->update(
            'reservas_reservas',
            array(
                'confirmada' => 1,
            ),
            array( 'id' => $_POST["id"]));
        // Añadimos un aviso con html
        ?>
        <div class="wrap">
            <h2>Confirmar Reserva</h2>
        </div>
        <div id="message" class="updated">
            <p>
                Reserva confirmada. <a href='admin.php?page=Reservas'>Ver lista de reservas</a>
            </p>
        </div>
        <?php
    }
}

/* Funcion que carga el codigo html del menu de confirmacion del plugin */
function load_html_confirmacion() {
    // Obtenemos los datos de la reserva para mostrarlos
    $id_reserva = $_GET['id'];
    $reserva = database::get_reservas("id",$id_reserva);
    
    $profesor = $reserva[0]->profesor;
    
    $asignatura = $reserva[0]->asignatura;
    $asignatura = database::get_asignatura("id",$asignatura);
    $asignatura = $asignatura[0]->nombre;
    
    $grupo = $reserva[0]->grupo;
    
    $tipo_reserva = $reserva[0]->tipo_reserva;
    if($tipo_reserva==1) $tipo_reserva = "Puntual";
    if($tipo_reserva==0) $tipo_reserva = "Cuatrimestral";
    
    $fecha = $reserva[0]->fecha;
    
    $cuatrimestre = $reserva[0]->cuatrimestre;
    if($cuatrimestre==0) $cuatrimestre = "Verano";
    if($cuatrimestre==1) $cuatrimestre = "Primer Cuatrimestre";
    if($cuatrimestre==2) $cuatrimestre = "Segundo Cuatrimestre";
    
    $dia = $reserva[0]->dia;
    
    $aula = $reserva[0]->aula;
    $aula = database::get_aulas("id",$aula);
    $aula = $aula[0]->nombre;
    
    $hora_inicial = $reserva[0]->hora_inicio;
    $hora_final = $reserva[0]->hora_fin;
    
    // Presentamos la informacion mediante codigo html
    ?>
        <div class="wrap">
            <h2>Confirmar Reserva</h2>
        </div>
        <br/>
        Existe una reserva pendiente realizada a través del sistema automático de reservas con los siguientes datos facilitados por el usuario. Por favor, confirme que los datos de la reserva son correctos antes de confirmar definitivamente la solicitud:<br/><br/>
            <div class="create_schedule_resumen">
                <b>Profesor: </b> <?php echo $profesor ?><br/>
                <b>Asignatura: </b> <?php echo $asignatura ?><br/>
                <b>Grupo: </b> <?php echo $grupo ?><br/>
                <b>Tipo de Reserva: </b> <?php echo $tipo_reserva ?><br/>
                <?php
                    if($tipo_reserva=='Puntual') {
                        echo "<b>Fecha: </b>" . $fecha . "<br/>";
                    }
                    else {
                        echo "<b>Día: </b>Todos los " . $dia . " del ". $cuatrimestre ."<br/>";
                    }
                ?>
                <b>Aula: </b> <?php echo $aula ?><br/>
                <b>Horario: </b> de <?php echo $hora_inicial ?> a <?php echo $hora_final ?><br/><br/><br/>
            </div>
            <form action="<?php echo get_permalink(); ?>" method="POST">                           
                <input type="hidden" name="modo" value="confirmar">
                <div class="create_schedule_button1">
                    <input type="button" onclick="location.href='admin.php?page=Reservas'" class="button" value="Salir">
                    <input type="submit" class="button button-primary" value="Confirmar Reserva">
                </div></br></br>
                <h2>Ocupación actual</h2></br>
                
    <?php     
       echo "<div class='confirm_schedule_calendar'>";
       echo load_html_preview($dia);
       echo "<input type='hidden' name='id' value=$id_reserva></form></div>"; 
}