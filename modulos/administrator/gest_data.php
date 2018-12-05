<?php

/**
 * Modulo de administración para la gestión de los informes del gestor de reservas
 */

/* Funcion principal que hace las llamadas al resto de funciones de la clase gest_vm */
function administrar_informes() {
    //generate_excel(2);
    load_html_data();
}

function load_html_data() {
    $anno = $_POST["anno"];
    if($anno=='') $anno = date("Y");
    ?>
    <div class="wrap">
        <h2>Informes</h2>
        <br/>
        <!--<h2>19 de Febrero, 2015, 9:53 am</h2>-->
        <?php printToday($anno); ?>
         
        <?php echo generar_seleccion_annos($anno); ?>
    </div>
    <div id="primer_cuatrimestre">
        <h3>Primer Cuatrimestre</h3>
        <table class="tabla_informe_cuatrimestre_1">
            <tr>
                <th>Asignatura</th> 
                <th>Aula</th>
                <th>Horas</th>
            </tr>
            <?php echo load_statistics(1,$anno); ?>
        </table>
    </div>
    <div id="segundo_cuatrimestre">
        <h3>Segundo Cuatrimestre</h3>
        <table class="tabla_informe_cuatrimestre_1">
            <tr>
                <th>Asignatura</th> 
                <th>Aula</th>
                <th>Horas</th>
            </tr>
            <?php echo load_statistics(2,$anno); ?>
        </table>
    </div>
    <div id="excel">
        <b>Descargar este informe</b><br/>
        <div>
            Puede descargar este informe como un documento de hoja de cálculo (.xls) de <b>Microsoft Office Excel</b>.
            Tenga en cuenta que los informes realizados para el presente curso son provisionales y podrían cambiar 
            si se solicitan nuevas reservas puntuales o cuatrimestrales por parte del equipo docente de la Universidad. 
            <form method="POST" action="<?PHP echo RESERVAS_URL ?>/plugins/Excel/generate_excel.php">
                <input type="hidden" name="output" value="<?php echo generate_excel($anno)?>">
                <input type="hidden" name="year" value="<?php echo $anno ?>">
            <br/><button class="button-secondary" type="submit">Descargar</button>
            </form>
        </div>
    </div>
    <?php
    generae_excel();
}

function load_statistics($cuatrimestre,$anno) {
    $lista_reservas = database::get_reservas_for_statistics($cuatrimestre,$anno);
    $horas = 0;
    $output="<tr>";
    $alternate = 0;
    foreach($lista_reservas as $reserva) {
        $asignatura = database::get_asignatura("id",$reserva->asignatura);
        $aula = database::get_aulas("id",$reserva->aula);
        $asignatura = $asignatura[0]->nombre;
        $aula = $aula[0]->nombre;
        $hora = $reserva->horas;
        $horas+=$hora;
        
        
        $output.="<td>$asignatura</td>";
        $output.="<td>$aula</td>";
        $output.="<td>$hora</td>";
        if($alternate==0) {
            $output.="</tr><tr>";
            $alternate=1;
        }
        else {
          $output.="</tr><tr class='statistics_alternate'>";
          $alternate=0;    
        }
    }
    $output.="<th></th><th>Total</th>";
    $output.="<th><b>$horas</b></th>";
    return $output; // Retornamos el cÃ³digo HTML como resultado.    
}

function generate_excel($anno) {
    $lista_reservas = database::get_reservas_for_statistics(1,$anno);
    $lista_reservas2 = database::get_reservas_for_statistics(2,$anno);
    
    $horas = 0;
    $fin = 0;
    $horas2 = 0;
    $fin2 = 0;
    $output = "\t\t\t\t\tReservas del $anno\t";
    $output .= "\n\n\t 1er cuatrimestre: \t\t\t\t 2o cuatrimestre:\t\n";
    $output .= "\n\tAsignatura\tAula\tHoras\t\tAsignatura\tAula\tHoras";
    
    for($i=0;$i<count($lista_reservas) || $i<count($lista_reservas2);$i++) {
        if($i<count($lista_reservas)) {
            $asignatura = database::get_asignatura("id",$lista_reservas[$i]->asignatura);
            $aula = database::get_aulas("id",$lista_reservas[$i]->asignatura);
            $asignatura = $asignatura[0]->nombre;
            $aula = $aula[0]->nombre;
            $hora = $lista_reservas[$i]->horas;
            $horas+=$hora;
            
            $output.="\t$asignatura\t";
            $output.="$aula\t";
            $output.="$hora\t";
            //$output.="\n";
        }
        else {
            if($fin==0) {
                $output.="\t\tTotal\t";
                $output.="$horas\t";
                $fin=1;
            }
            else {
                $output.="\t\t\t\t"; 
            }
        }
        
        if($i<count($lista_reservas2)) {
            $asignatura = database::get_asignatura("id",$lista_reservas2[$i]->asignatura);
            $aula = database::get_aulas("id",$lista_reservas2[$i]->asignatura);
            $asignatura = $asignatura[0]->nombre;
            $aula = $aula[0]->nombre;
            $hora = $lista_reservas2[$i]->horas;
            $horas2+=$hora;
            
            $output.="\t$asignatura\t";
            $output.="$aula\t";
            $output.="$hora\t";
            
        }
        else {
           if($fin2==0) {
                $output.="\t\tTotal\t";
                $output.="$horas2\t";
                $fin2=1;
            }
        }
        $output.="\n";
        
    }
    if(fin2==0) {
        $output.="\t\t\t\t\t\tTotal\t";
        $output.="$horas2\t";
    }
    
    /*foreach($lista_reservas as $reserva) {
        $asignatura = database::get_asignatura("id",$reserva->asignatura);
        $aula = database::get_aulas("id",$reserva->aula);
        $asignatura = $asignatura[0]->nombre;
        $aula = $aula[0]->nombre;
        $hora = $reserva->horas;
        $horas+=$hora;
        
        
        $output.="\t$asignatura\t";
        $output.="$aula\t";
        $output.="$hora\t";
        $output.="\n";
    }*/
    
    
    return $output;
}

function printToday($anno) {
    if($anno!=date("Y")) {
        echo "<h2>Informe del pasado año $anno</h2>";
    }
    else {
        $date = date("F j, Y, g:i a");
        echo "<h2>$date</h2>";
    }
    
}

function generar_seleccion_annos($anno) {
    $years = database::get_years_for_statistics();
    
    $output = "<form method=\"POST\" onchange=\"this.submit();\"><i>Puede volver a ver las estadísticas de cualquier curso del que haya registros en la base de datos</i> "
            . "<select name='anno'>";
    for($i=0;$i<count($years);$i++) {
        if($years[$i]==$anno) {
            $output .= "<option value=\"$years[$i]\" onchange=\"this.form.submit()\" selected='selected'>$years[$i]</option>";
        }
        else {
            $output .= "<option value=\"$years[$i]\" onchange=\"this.form.submit()\">$years[$i]</option>";
        }
    }
    $output .= "</select><input type='hidden' name='form' value='yes'></form>";
    
    return $output;
}




